<?php
 
class Object {
    public $_values = array();
    public $_dirty_values = array();
    public $_fresh = true;

    public function __construct($id = null) {
         // Use your database to get the data for this object where ID = $id
         // Return each value as $this->whatever
        if ($id) {
            $sql = "SELECT * FROM {$this->table} WHERE id = {$id}"; // Sanitize this
            $results = DB::getRow($sql);
            if ($results) {
                foreach ($results as $field => $value) {
                    $this->_values[$field] = $value;
                }
                $this->_fresh = false;
            }
        }
    }

    public function __get($name) {
        if (isset($this->_values[$name]))
            return $this->_values[$name];
        else {
            if (isset($this->_values[$name.'_id'])) {
                $class = str_replace(' ', '_', ucwords(str_replace('_', ' ', $name)));
                $id = $this->_values[$name.'_id'];

                if (class_exists($class)) {
                    $obj = new $class_name ($id);
                    return $obj;
                }
            } else {
                return false;
            }
        }
    }

    public function __set($name, $value) {
        if ($name == 'id') return false;
        
        $class_name = strtolower(get_class($this));
        
        $this->_dirty_values[$name] = $value;
    }

    public function reload() {
        $sql = "SELECT * FROM {$this->table} WHERE id = {$this->_values['id']}"; // Sanitize this
        $results = DB::getRow($sql);
        if ($results) {
            foreach ($results as $k => $v)
                $this->_values[$k] = $v;
        }
    }

    public function save($print = false, &$old_values = null) {
        if (!count($this->_dirty_values))
            return;
        
        $old_values = $this->_values;
        foreach ($this->_dirty_values as $name => $value) {
            if ($value === DB::NULL)
                $clauses[] = "`$name` = NULL";
            elseif ($value === DB::NOW)
                $clauses[] = "`$name` = NOW()";
            elseif ($value === DB::UNIX_TIMESTAMP)
                $clauses[] = "`$name` = UNIX_TIMESTAMP()";
            else
                $clauses[] = "`$name` = \"".DB::string($value).'"';
            
            $this->_values[$name] = $value;
        }
        
        $set_clause = implode(',', $clauses);
        
        if ($this->_fresh) {
            $query = 'INSERT '.($this->insert_ignore ? 'IGNORE ' : '').'INTO '.$this->table.' SET '.str_replace('\\\'', '\'', $set_clause);
            $this->_values['id'] = DB::insert($query);
            if ($this->_values['id'] > 0) {
                $this->_loaded = true;
                $this->_fresh = false;
                $this->reload();
            }
            
            // set $old_values to just a blank array w/ all the correct field names, for the _on_save() call later
            $old_values = array_fill_keys(array_keys($this->_values), null);
            
        } else {
            $query = 'UPDATE '.$this->table.' SET '.str_replace('\\\'', '\'', $set_clause).' WHERE id = "'.$this->_values['id'].'"';
            DB::update($query);
            
            Cache::delete($this->table_name.':'.$this->_values['id']);
            
            $this->reload();
            
            // make sure $old_values has all keys
            foreach (array_diff(array_keys($this->_values), array_keys($old_values)) as $key)
                $old_values[$key] = null;
        }
        
        if ($print)
            pq($query);
        
        $this->_dirty_values = array();
        
        $old_values = (object) $old_values;
    }

}