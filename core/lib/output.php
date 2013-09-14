<?php
/**
 * @class: input
 * @authour: lachhekumar nadar < lachhekumar@gmail.com >
 *
 * Output class will handling all outgoing varaible with in the class
 */

final class output {
    // hold reference to the previous loaded class
    private static $instance;

    // keep track of POST, GET and CUSTOM value passed to this input class
    private static $variable;

    private function  __construct() {
        // do any initialization work
        self::$variable = array();
    }
    

    public static function instance() {
        // verify do we have any previous instance runing
        if(!isset(self::$instance)) {
            self::$instance = new output;
        }
        
        // send back instance of class  
        return self::$instance;
    }

    public static function set($variable) {
        // add to list if it array
        if(is_array($variable)) {
            self::$variable = array_merge_recursive(self::$variable, (array)$variable);
        } 
        return self::$instance;
    }

    public static function param($name,$data) {
        // add to list if it array
        self::$variable[$name] = $data;
        return self::$instance;
    }

    public static function display() {
        return self::$variable;
    }
    

    public function __get($name) {
        return @self::$variable[$name]?:false;
    }

    public function __isset($name) {
        return isset(self::$variable[$name]);
    }
    
}

