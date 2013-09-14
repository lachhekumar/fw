<?php

/**
 * Maintian the database connection and other things
 * @author kumar
 */

$__connect_database = "";

// 
class DB {
    
    
    // decalring the varaibale
    private $_link;
    private $_where;
    private $_orderby;
    private $_join;
    private $_groupby;
    private $_fields;
    private $_limit;
    
    
    public $result;         // store the result set
   
    public function __construct() {
        
        global $__connect_database;
        global $__database;
        
        // we do not have the database connection details, lets of the
        if($__connect_database == "") {
            
            try {
                // connecting to the database
                $this->link = $__connect_database = new PDO($__database["dsn"], $__database["username"], $__database["password"]); 
                // set required setting for UTF8, utf8 setting is required for
                // multilingal support.                
                $this->query("SET NAMES 'utf8'");
                $this->query("SET CHARACTER SET utf8");
                $this->query("SET SQL_BIG_SELECTS=1");
                
            } catch(PDOException $e) {
                // display error message if there is any error
                $this->error("Unable to connect to database " . $e->getMessage() . " at " .__LINE__ );
            }
                        
        }
        
        $this->link =  $__connect_database;
    }
    
    
    private function loadevent($table) {
        if(file_exists(domain. "/event/" .$table . ".php")) {
            include_once domain. "/event/" .$table . ".php";
            
            $_c = '$__Class = new Tbl' . $table. '();';
            eval($_c);
            
            return $_c;
        }        
        
        return false;
    }
    
    public function count() {
        if(!$this->result) {
            // so sad we do not have the proper result set
            return false;
        }
        return $this->result->rowCount();
    }
    /**
     * Execute query and get the result
     * @param type $statment
     * @param type $parameter
     */
    
    public function query($statement,$parameter = null) {

        global $__allquery;
        $parameter = @$parameter?:array();
        
        // We are executing the query 
        $this->result = $this->link->prepare($statement);
        $this->result->execute($parameter);

        if(DEBUG == 1) {
            global $__allquery;
            $__allquery[] =$statement;
        }

        // :( there is a error in the query lets inform user that there is a error
        $error = $this->result->errorInfo();
        if($error[0] != "00000") {
            $this->error("Unable to process SQL Statement  " . $error[2] ." <br />".$statement ."<br />");        
         }
         
         return $this->result;
    }
    
    
    /**
     * generate select statment from the given parameter
     * @param type $table
     */
    public function select($table,$parameter= null) {
        
        if($table == "") {
            return false;
        }
        
        $this->table = $table;        
        $this->loadevent($table);
        if(class_exists("Tbl" . $table)) {
            $_c = '$__Class = new Tbl' . $table. '();';
            eval($_c);
            if(method_exists($__Class, "beforeselect")) {
                $_c->beforeselect($table,$parameter);
            }
        }
        
        $fields = (count($this->_fields) > 0)?join($this->_fields):"*";
        // lets create the select statement
        $stmt = "SELECT " . $fields . "  FROM " .$table;
        $stmt .= (count($this->_join) > 0)?" " .join($this->_join):" ";
        $stmt .= (count($this->_where) > 0)?" WHERE " .join($this->_where):" ";
        $stmt .= (count($this->_groupby) > 0)?" GROUP BY " .join($this->_groupby):" ";
        $stmt .= (count($this->_orderby) > 0)?" ORDER BY " .join($this->_orderby):" ";
        
        if($this->_limit != "") {
            $stmt .= $this->_limit;    
        }
        
        return $this->query($stmt,$parameter);
        
    }
    
    
    /**
     * @name fetch
     * Fetch records from the result set
     * @return \stdClass
     */
    public function fetch() {
        if(!$this->result) {
            // so sad we do not have the proper result set
            return new stdClass();
        }
        $_row = $this->result->fetch(PDO::FETCH_OBJ);
        
        if(isset($this->table) && $this->table != "") {
            
            $this->loadevent($this->table);
            if(class_exists("Tbl" . $this->table)) {
                $_c = '$__Class = new Tbl' . $this->table. '();';
                eval($_c);
                if(method_exists($__Class, "onmove")) {
                    $_row1 = $_c->onmove($_row);
                    
                    if(is_array($_row1)) {
                        $_row = array_merge($_row,$_row1);
                    }
                }
            }
            
        }
        
        return $_row;
        
    }

    /**
     * @name fetch
     * Fetch records from the result set
     * @return \stdClass
     */
    public function fetchAll() {
        if(!$this->result) {
            // so sad we do not have the proper result set
            return new stdClass();
        }
        return $this->result->fetchAll(PDO::FETCH_OBJ);
        
    }
    
    public function tablecol($table) {
        $this->query("describe " . $table);
        $_cols = $this->fetchAll();
        
        $this->primary = "";
        
        $_columns = array();
        foreach($_cols as $key => $value) {
            
            if($value->Extra != "auto_increment") {
                $_columns[] = $value->Field;
            } else {
                $this->primary = $value->Field;
            }
        }
        
        return $_columns;
        
    }

    /**
     * insert into table
     * @param type $table
     * @param type $parameter
     */
    public function insert($table,$parameter) {
        $event = $this->loadevent($table);
        
        if(is_object($parameter)) {
            $parameter = get_object_vars($parameter);
        }
        
        if($event != false) {
            if(method_exists($event, "beforeinsert")) {
                $_details = $event->beforeinsert($parameter);
                
                if(is_array($_details))
                    $parameter = array_merge ($_details, $parameter);
            }
        }
        
        $_columns = $this->tablecol($table);
        
        $stmt = "insert into `$table`"; 
        
        $_fields = array();
        $_values = array();
        foreach($_columns as $key => $value) {
            
            if(isset($parameter[$value])) {
                  $_fields[] = "`$value`";
                  
                  if($value == "password") {
                      $parameter[$value] = md5($value);
                  }
                  $_values[] = "'" . addslashes($parameter[$value]). "'";
            }
        }
        
        $stmt .= "(" . join(",",$_fields) .")";
        $stmt .= " values(" . join(",",$_values) .")";
        
        $this->query($stmt);
        $this->id = $this->link->lastInsertId();
        
        if($event != false) {
            if(method_exists($event, "afterinsert")) {
                $_details = $event->afterinsert($this->id);                
            }
        }
        
        $db = new DB;
        $db->query("select * from _track  where site_id='" .site_id. "' and tablename='$table'");
        if($db->count() < 1) {
            $db->query("INSERT _track(site_id,tablename,updatedate) VALUES(" .site_id. ",'$table',NOW())");
        }
        $db->query("update _track  set updatedate=NOW() where site_id='" .site_id. "' and tablename='$table'");
        
        
        return $this->link->lastInsertId();        
    }


    /**
     * update a records
     * @param type $table
     * @param type $parameter
     * @param type $contition
     */
    public function update($table,$parameter,$condition = null) {
        
        $event = $this->loadevent($table);
        
        if(is_object($parameter)) {
            $parameter = get_object_vars($parameter);
        }
        
        if($event != false) {
            if(method_exists($event, "beforeupdate")) {
                $_details = $event->beforeinsert($parameter);
                
                if(is_array($_details))
                    $parameter = array_merge ($_details, $parameter);
            }
        }
        
        $_columns = $this->tablecol($table);
        
        $stmt = "update  `$table` set"; 
        
        $_fields = array();
        $_values = array();
        foreach($_columns as $key => $value) {
            
            if(isset($parameter[$value])) {
                
                  if($value == "password") {
                      $parameter[$value] = md5($value);
                  }

                  
                  $_values[] = "`$value` = '" . addslashes($parameter[$value]). "'";
            }
        }
        
        if(count($_values) < 1) {
            return false;
        }
        $stmt .= " " . join(",",$_values) ." ";
        $sep  = " WHERE ";
        if($condition != null) {
            $stmt .= $sep . " " .$condition;
            $sep = "";
        }
        $stmt .= (count($this->_where) > 0)?" $sep " .join($this->_where):" ";          
        $this->query($stmt);
        
        
        if($event != false) {
            if(method_exists($event, "afterupdate")) {
                $_details = $event->afterinsert($parameter);                
            }
        }
        
        
        $db = new DB;
        $db->query("select * from _track  where site_id='" .site_id. "' and tablename='$table'");
        if($db->count() < 1) {
            $db->query("INSERT _track(site_id,tablename,updatedate) VALUES(" .site_id. ",'$table',NOW())");
        }
        $db->query("update _track  set updatedate=NOW() where site_id='" .site_id. "' and tablename='$table'");
        

      
        
        return true;          
    }
    
    /**
     * delete from records
     * @param type $table
     * @param type $parameter
     * @param type $condition
     */
    public function delete($table,$parameter,$condition = "") {
        $event = $this->loadevent($table);
        
        if(is_object($parameter)) {
            $parameter = get_object_vars($parameter);
        }
        
        if($event != false) {
            if(method_exists($event, "beforedelete")) {
                $_details = $event->beforeinsert($parameter);
                
                if(is_array($_details))
                    $parameter = array_merge ($_details, $parameter);
            }
        }
        
        $_columns = $this->tablecol($table);
        
        $stmt = "delete from `$table` "; 
        $sep  = " WHERE ";
        if($condition != null) {
            $stmt .= $sep . " " .$condition;
            $sep = "";
        }
        $stmt .= (count($this->_where) > 0)?" $sep " .join($this->_where):" ";          
        $this->query($stmt);
        
        
        if($event != false) {
            if(method_exists($event, "afterdelete")) {
                $_details = $event->afterinsert($parameter);                
            }
        }
        
        $db = new DB;
        $db->query("select * from _track  where site_id='" .site_id. "' and tablename='$table'");
        if($db->count() < 1) {
            $db->query("INSERT _track(site_id,tablename,updatedate) VALUES(" .site_id. ",'$table',NOW())");
        }
        $db->query("update _track  set updatedate= NOW() where site_id='" .site_id. "' and tablename='$table'");
        
        
        return true;          
        
    }

    /**
     * where condition
     * @param type $condition
     */
    public function where($condition) {
        $this->_where[] = stripslashes($condition);
    }
    
    /**
     * fields
     * @param type $fields
     */
    public function fields($fields) {
        $this->_fields[] = $fields;
    }
    
    
    /**
     * join 
     * @param type $type
     * @param type $table
     * @param type $condition
     */
    public function joins($type,$table,$condition) {
        $this->_fields[] = $type . " JOIN " . $table . " ON " . stripslashes($condition);
    }
    
    /**
     * order by
     * @param type $field
     * @param type $type
     */
    public function orderby($field) {
        $this->_orderby[] = $field;
    }
    
    /**
     * group by
     * @param type $field
     */
    public function groupby($field) {
        $this->_orderby[] = $field;
    }
    
    
    
    public function limit($start,$total) {
        $this->_limit = " LIMIT " . $start . ", " . $total;
    }
    
    
    
    
    /**
     * Holding the error
     * @global array $__error
     * @param type $stmt
     */
    public function error($stmt) {
        global $__error;
        $__error[] = $stmt;        
        print_r($__error);
        exit;        
    }
    
    
    
}
