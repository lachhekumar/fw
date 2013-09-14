<?php


/**
 * Work with database to get the required output
 *
 * @author Lachhekumar Nadar < lachhekumar@gmail.com >
 */
class model {
    
    private $_db;
    
    /**
     * Initial connection to database
     */
    public function __construct() {
        $this->_db = new DB;
    }
}

?>
