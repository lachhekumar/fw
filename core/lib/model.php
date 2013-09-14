<?php


/**
 * Work with database to get the required output
 * getTable($tablename)
 * 
 * @author Lachhekumar Nadar < lachhekumar@gmail.com >
 */
class model {
    
    private $_db;
    
    /**
     * Initial connection to database
     */
    public function __construct() {
    }
    
    /**
     * Get the table details
     */
    public function getTable($tablename) {
        
        $input = input::instance();
        
        // getting records from the serevr
        $db = new DB;        
        if(is_array($input->fields)) {
            
            foreach($input->fields as $key => $value) {
                $db->fields($value);
            }
        }
        
        if(is_array($input->join)) {
            
            foreach($input->join as $key => $value) {
                $db->joins($value["type"], $value["table"], $value["condition"]);
            }
        }        
        
        if(is_array($input->where)) {
            foreach($input->where as $key => $value) {
                $db->where($value);
            }
        }
            
        if(is_array($input->orderby)) {
            foreach($input->orderby as $key => $value) {
                $db->orderby($value);
            }
        }        

        if(is_array($input->groupby)) {
            foreach($input->groupby as $key => $value) {
                $db->groupby($value);
            }
        }        

        if(is_array($input->limit)) {
            foreach($input->limit as $key => $value) {
                $db->limit($value);
            }
        }        
                
        $db->select($tablename,(($input->parameter != false)?$input->parameter:null));
        
        return $db->fetchAll();
        
    }
    
    
    public function insert($tablename) {     
        
        $db = new DB;
        
        $input = input::instance();
        $db->insert($tablename,$input->display());
        return true;
    }    
    
    public function update($tablename) {     
        
        $db = new DB;       
        $input = input::instance();
        
        
        $db->tablecol($tablename);
        if(is_array($input->where)) {
            foreach($input->where as $key => $value) {
                $db->where($value);
            }
            $db->update($tablename,$input->parameter);
        } else {
            
            if(is_object($parameter)) {
                $parameter = get_object_vars($parameter);
            }
            $db->update($tablename,$input->parameter,$db->primary ."='" .addslashes($parameter[$db->primary]). "'");
        }
        
        return true;
        
        
    }    
    
    
    public function delete($tablename) {     
        
        $db = new DB;       
        $input = input::instance();
        
        
        $db->tablecol($tablename);
        if(is_array($input->where)) {
            foreach($input->where as $key => $value) {
                $db->where($value);
            }
            $db->delete($tablename,$input->parameter);
        } else if($input->condition != "") {
            $db->delete($tablename,$input->parameter,$input->condition);
        }
        else {
            
            if(is_object($parameter)) {
                $parameter = get_object_vars($parameter);
            }
            $db->delete($tablename,$input->parameter,$db->primary ."='" .addslashes($parameter[$db->primary]). "'");
        }
        
        return true;
        
        
    }        
    
}

?>
