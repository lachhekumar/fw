<?php

/**
 * @author Lachhekumar Nadar<lachhekumar@gmail.com>
 * Send json header report
 */

// format html as per the user request
include __DIR__ . "/_boot.php";

header('Content-Type: application/json;  charset=utf-8');
$__controller =  explode("/",str_replace(".json","",url));

$token = new Token();
if($token->verify() == false) {
    
    $__output["error"] = array("code" => 0,"message" => "Invalid code");
    $input = input::instance();
    if($input->callback != "" && samehost == 0) {
        echo $input->callback ."(" . json_encode($__output) .")";
    } else {
        echo json_encode($__output);
    }
    exit;
    
}

$db = new DB;
$db->where("domainname='" .host ."'");
$db->select("_site");

if($db->count() < 1) {
    define("site_id",0);
} else {
    $_site = $db->fetch();
    define("site_id",$_site->site_id);
}


$__output = array();
// Getting the model details
if($__controller[0] == "_model") {
    
    $_model = new model(); 
    
    switch($_SERVER["REQUEST_METHOD"]) {
        case "GET":
        case "POST":
            $_result = $_model->getTable($__controller[1]);
            break;
        
        case "PUT":
            $_result = $_model->insert($__controller[1]);
            break;

        case "PATCH":
            $_result = $_model->update($__controller[1]);
            break;

        case "DELETE":
            $_result = $_model->delete($__controller[1]);
            break;

        default:
            $_result = $_model->getTable($__controller[1]);
            break;
            
    }
    
    $__output["result"] = $_result;

} else {

    $__controller[0] = @$__controller[0]?:"index";
    $__controller[1] = @$__controller[1]?:"index";


    // getting controller loaded
    if(class_exists($__controller[0],1)) {

        $_c = '$__class = new ' . $table. '()';
        eval($_c);
        if(method_exists($__class, $__controller[1] . "Action")) {
            $__class->{$__controller[1] . "Action"}('json');
            
            $output = output::instance();
            $_result = $output->display();
            $__output["result"] = $_result;
        }

    }
    
}

$input = input::instance();
if($input->callback != "" && samehost == 0) {
    echo $input->callback ."(" . json_encode($__output) .")";
} else {
    echo json_encode($__output);
}



