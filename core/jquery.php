<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include __DIR__ . "/_boot.php";
header('Content-Type: application/x-javascript;  charset=utf-8');
$_js = file_get_contents(core . "/js/" . $_GET["id"]. ".js");
echo $_js;