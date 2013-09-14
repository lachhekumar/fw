<?php

class Token {
    
    
    public function get() {
        
        // let us validate the javascript request
        $db = new DB;
        $db->where("domainname='" .host ."'");
        $db->select("_site");        
        
        if($db->count() < 1) {
            $site_id = 0;
        } else {
            $_site = $db->fetch();            
            $site_id = $_site->site_id;
        }
        
        return md5($site_id . " " . $_SERVER["REMOTE_ADDR"]);
        
    }
    
    public function verify() {
        
        // let us validate the javascript request
        $db = new DB;
        $db->where("domainname='" .host ."'");
        $db->select("_site");        
        
        if($db->count() < 1) {
            $site_id = 0;
        } else {
            $_site = $db->fetch();            
            $site_id = $_site->site_id;
        }
        
        $input = input::instance();
        
        if(md5($site_id . " " . $_SERVER["REMOTE_ADDR"]) == $input->__token) {
            return true;
        } else {
            return false;
        }
        
    }    
    
}
?>
