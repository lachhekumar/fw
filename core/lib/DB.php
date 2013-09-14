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
   
    public function __construct() {
        
        global $__connect_database;
        // we do not have the database connection details, lets of the
        if($__connect_database == "") {
        }
    }
}

?>
