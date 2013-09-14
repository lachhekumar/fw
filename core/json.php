<?php

/**
 * @author Lachhekumar Nadar<lachhekumar@gmail.com>
 * Send json header report
 */

// format html as per the user request
include __DIR__ . "/_boot.php";

//header('Content-Type: application/json;  charset=utf-8');
$__controller =  explode("/",str_replace(".json","",url));


$__output = array();
// Getting the model details
if($__controller[0] == "_model") {
    
    $_model = new model(); 
    //$_model->getTable($__controller[1]);
}

echo json_encode($__output);



