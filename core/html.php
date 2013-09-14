<?php

/**
 * @author Lachhekumar Nadar<lachhekumar@gmail.com>
 * 
 * Forat HTML output as the user requirement
 * 1. Selection of the controller
 * 2. Selection of the View and Template
 * 3. Format forms
 */

// format html as per the user request
include __DIR__ . "/_boot.php";

header('Content-Type: text/html;  charset=utf-8');
$__controller =  explode("/",str_replace(".json","",url));
$__template =  explode("/",str_replace(".json","",url));


$__template[0] = @$__template[0]?:"index";
$__template[1] = @$__template[1]?:"index";


$__controller[0] = @$__controller[0]?:"index";
$__controller[1] = @$__controller[1]?:"index";


// getting controller loaded
if(class_exists($__controller[0],1)) {
    
    $_c = '$__class = new ' . $__controller[0]. '();';
    eval($_c);
    if(method_exists($__class, $__controller[1] . "Action")) {
        $__class->{$__controller[1] . "Action"}('html');
    }
    
}

// getting the template

if(file_exists(domain . "/html/" . $__template[0] . ".html")) {
    $_template =  domain . "/html/" . $__template[0] . ".html";
}  else {
    $_template =  domain . "/html/default.html";
}


// reading the template and update it to the client
$_temp = new template();
$_temp->display($_template);
