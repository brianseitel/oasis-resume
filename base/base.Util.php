<?

class Util {
	
	public static function fetch($array, $key, $default = '') {
		if (array_key_exists($key, $array))
			return $array[$key];
		else
			return $default;
	}
}

function fetch($array, $key, $default = '') {
	return Util::fetch($array, $key, $default);
}

function pp($array) {
	echo '<pre>'.print_r($array, 1).'</pre>';
}

function pd($array) {
	pp($array);die();
}