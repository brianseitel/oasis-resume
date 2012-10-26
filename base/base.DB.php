<?

class DB {
	
	private static $connected = array();
	private static $link;
	private static $ping = false;
	private static $stats = array(DB::READ => array('SELECT' => 0));
	private static $log = array();
	private static $notified_error = false;
	
	private static $using_instance_cache = false;
	private static $instance_cache = array();
	private static $instance_cache_reads = 0;
	
	const NOW = '_ _ _ _ _ NOW() _ _ _ _ _';
	const NULL = '_ _ _ _ _ NULL _ _ _ _ _';
	const READ = 0;
	const WRITE = 1;
	const UNIX_TIMESTAMP = '_ _ _ _ _ UNIX_TIMESTAMP() _ _ _ _ _';
	
	public static function clean($data) {
		if (is_array($data))
			foreach($data as $key => $var)
				$data[$key] = self::clean($var); // a little recursive action
		else
			$data = self::clean_string($data);

		return $data;
	}
	
	private static function clean_string($string) {
        if(is_string($string)){
            if (!strlen($string)) return NULL;

            if (get_magic_quotes_gpc()) $string = stripslashes($string);

            if (!is_numeric($string)) {
                $string = mysql_escape_string($string);
            }
        }

        return $string;
	}
	
	public static function connect($server = DB::READ) {
		if (!isset($GLOBALS['db']) || !is_object($GLOBALS['db']))
			$GLOBALS['db'] = new DB;
		
		if (!fetch(self::$connected, $server, false))
			$GLOBALS['db']->reconnect($server);
		elseif (self::$ping) {
			if (!mysql_ping(self::$link[$server])) {
				$GLOBALS['db']->disconnect($server);
				$GLOBALS['db']->reconnect($server);
			}
		}
		
		if ($server == DB::WRITE || !defined('DB_READ_DATABASE'))
			mysql_select_db(PRE.DB_DATABASE, self::$link[$server]);
		else
			mysql_select_db(PRE.DB_READ_DATABASE, self::$link[$server]);
	}
	
	public static function delete($query, $server = DB::WRITE) {
		self::$stats[$server]['DELETE']++;
		self::query($query, $server);
		return mysql_affected_rows(self::$link[$server]);
	}
	
	public static function disconnect($server = DB::READ) {
		mysql_close(self::$link[$server]);
		self::$connected[$server] = false;
	}
	
	public static function getArray($query, $server = DB::READ) {
		$result = DB::select($query, $server);
		$output = array();
		
		if (@mysql_num_fields($result) > 1) {
			while ($entry = mysql_fetch_assoc($result)) {
				unset($clean_entry);
				
				foreach ($entry as $label => $value)
					$clean_entry[$label] = stripslashes($value);
					
				$output[] = $clean_entry;
			}
		} elseif (@mysql_num_fields($result) == 1) {
			while ($entry = mysql_fetch_row($result))
				$output[] = stripslashes($entry[0]);
		}
		
		return $output;
	}
	
	public static function getKeyedArray($query, $index, $server = DB::READ) {
		$rows = DB::getArray($query, $server);
		$data = array();
		
		if ($rows)
			foreach ($rows as $row)
				$data[$row[$index]] = $row;
		
		return $data;
	}
	
	public static function getKeyValueArray($query, $key_field, $value_field, $server = DB::READ) {
		$rows = DB::getArray($query, $server);
		return Util::array_to_map($key_field, $value_field, $rows);
	}
	
	public static function getRow($query, $server = DB::READ) {
		if (self::$using_instance_cache) {
			if (isset(self::$instance_cache[$query])) {
				return self::$instance_cache[$query];
			} else {
				self::$instance_cache[$query] = mysql_fetch_assoc(DB::select($query, $server));
				return self::$instance_cache[$query];
			}
		} else {
			return mysql_fetch_assoc(DB::select($query, $server));
		}
	}
	
	public static function getValue($query, $server = DB::READ) {
		if (self::$using_instance_cache) {
			if (isset(self::$instance_cache[$query])) {
				return self::$instance_cache[$query];
			} else {
				self::$instance_cache[$query] = @mysql_result(DB::select($query, $server), 0);
				return self::$instance_cache[$query];
			}
		} else {
			return @mysql_result(DB::select($query, $server), 0);
		}
	}
	
	public static function insert($query, $server = DB::WRITE) {
		self::clear_instance_cache();
		if (!isset(self::$stats[$server]['INSERT'])) self::$stats[$server]['INSERT'] = 0;
		self::$stats[$server]['INSERT']++;
		self::query($query, $server);
		return mysql_insert_id(self::$link[$server]);
	}
	
	public static function log() {
		return self::$log;
	}
	
	public static function other($query, $server = DB::WRITE) {
		self::clear_instance_cache();
		preg_match('/^[\s(]*([a-zA-Z]+)/', $query, $matches);
		self::$stats[$server][$matches[1]]++;
		self::query($query, $server);
	}
	
	private static function query($query, $server = DB::READ) {
		DB::connect($server);
		$debug = fetch($_COOKIE, 'debug_db_queries') == 'on' ? true : false;
		if ($debug) $query_start = microtime(true);
		$result = mysql_query($query, self::$link[$server]);
		if ($result === false && !self::$notified_error) {
			$error = mysql_error(self::$link[$server]);
			if (isset($error{0})) {
				$email = defined('DEBUG_EMAIL') ? DEBUG_EMAIL : 'brianseitel@gmail.com';
				die($error);
			}
		}
		if ($debug) {
			$query_end = microtime(true);
			self::$log[] = array('query' => $query, 'runtime' => ($query_end - $query_start));
		}
		return $result;
	}
	
	public static function reconnect($server = DB::READ) {
		if (fetch(self::$connected, $server, false))
			return;
		
		if ($server == DB::READ) 
		    self::$link[$server] = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
		elseif ($server == DB::WRITE || !defined('DB_READ_SERVER'))
			self::$link[$server] = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
		else
			self::$link[$server] = mysql_connect(DB_READ_SERVER, DB_READ_USERNAME, DB_READ_PASSWORD);
		
		if (!self::$link[$server])
			die('Unable to connect to database');
		
		self::$connected[$server] = true;
	}
	
	public static function string($string, $server = DB::READ) {
		DB::connect($server);
		return mysql_real_escape_string($string, self::$link[$server]);
	}
	
	public static function select($query, $server = DB::READ) {
		self::$stats[$server]['SELECT']++;
		return self::query($query, $server);
	}
	
	public static function set_ping($ping = true) {
		self::$ping = $ping;
	}
	
	public static function stats() {
		return self::$stats;
	}
	
	public static function update($query, $server = DB::WRITE) {
		self::clear_instance_cache();
		if (!isset(self::$stats[$server]['UPDATE'])) self::$stats[$server]['UPDATE'] = 0;
		self::$stats[$server]['UPDATE']++;
		self::query($query, $server);
		return mysql_affected_rows(self::$link[$server]);
	}
	
	// INSTANCE CACHE FUNCTIONS
	
	private static function clear_instance_cache() {
		self::$instance_cache = array();
	}
	
	public static function disable_instance_cache() {
		self::$using_instance_cache = false;
	}
	
	public static function enable_instance_cache() {
		self::$using_instance_cache = true;
	}
	
	public static function instance_cache_reads() {
		return self::$instance_cache_reads;
	}
}
