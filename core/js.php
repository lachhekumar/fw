<?php

// format html as per the user request
include __DIR__ . "/_boot.php";

header('Content-Type: application/x-javascript;  charset=utf-8');
$_js = file_get_contents(core . "/js/_frame.js");


$token = new Token();



// let us validate the javascript request
$db = new DB;
$db->where("MD5(apikey)='" . addslashes($_GET["id"]) ."' and domainname='" .host ."'");
$db->select("_site");


if($db->count() < 1) {
    
    if(samehost != 1) {
        echo "alert('Please provide valid js path');";
        exit;
    }
}


$db = new DB;
$db->query("SELECT NOW() as date1");
$_r = $db->fetch();

$_js = (samehost != 1)?str_replace("{callback}","?callback=?",$_js):str_replace("{callback}","",$_js);
$_js = str_replace("{url}","http://" .host  .base,$_js);
$_js = str_replace("{token}",$token->get(),$_js);
$_js = str_replace("{now}",$_r->date1,$_js);

echo $_js;

$_function = get_defined_functions();
foreach($_function["user"] as $key => $value) {
    echo <<<EOF
   function $value(options,callback) {
       game.load({url: '_function/$value.json',data: {parameter: options},callback: callback});
   }
   

EOF;
    
}




