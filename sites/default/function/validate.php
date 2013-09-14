<?php


function validateusername($username) {
    
    $db = new DB;

    $db->where("username='$username'");
    $db->select("users");
    return $db->count();
}
