<?php

class template  {
    
    private $_template;
    
    public function __construct() {
    }
    
    
    public function display($template) {
        global $__template;
        
        // getting the output varaible
        $output = output::instance();               
        $this->_template = file_get_contents($template);                
        $this->_template = str_replace("{jquery}",'<script data-main="http://' .host  .base. '_backbone/main" type="text/javascript" src="http://' .host  .base. '/_js/require-jquery.js"></script>',$this->_template);
        
        
        if($output->content == "") {
            // try to get the conent
            if(file_exists(domain . "/html/view/" . $__template[1] . ".html")) {
                $_template =  domain . "/html/view/" . $__template[1] . ".html";
                $output->param("content",  file_get_contents($_template));
            }  
            
        }
        
        // if the content is 0 then send 404 error;
        if($output->content == "") {
            header('HTTP/1.0 404 Not Found');
        }
        
        
        $_parameter = output::display();
        
        foreach($_parameter as $key => $value) {
            if(is_array($value )) {
                foreach($value as $_k => $_v) {
                    $this->_template = str_replace("{:" .$key . "." . $_k."}",$_v,$this->_template);
                }
                $this->_template = str_replace("{:" .$key . "}",join(",",$value),$this->_template);
            } else {
                $this->_template = str_replace("{:" .$key . "}",$value,$this->_template);
            }
        }
        
        
        $this->_template = str_replace("{:content}",$output->content,$this->_template);
        echo $this->_template;
    }
}
?>
