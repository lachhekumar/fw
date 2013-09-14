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


$_js = (samehost != 1)?str_replace("{callback}","?callback=?",$_js):str_replace("{callback}","",$_js);
$_js = str_replace("{url}","http://" .host  .base,$_js);
$_js = str_replace("{token}",$token->get(),$_js);

echo $_js;
