<?php

/**
 * @author  Lachhekumar Nadar<lachhekumar@gamil.com>
 * 
 * Add required configuration to the system file
 * 
 * core         -> core include folder path
 * _host        -> actual host
 * host         -> main host file
 */


/**
 * Convert the given path to linux file format
 * @param type $url
 * @return type
 */
define("DEBUG",1);
function linuxPath($url) {
    return str_replace('\\',"/",$url);
}

// load domain routin configuration file
$_path = preg_replace("#core$#ismU","",linuxPath(__DIR__));

$__domain = array();
if(file_exists($_path . "domain.inc.php")) {
    include_once $_path . "domain.inc.php";
}

// define core include folder
define("core",__DIR__);
define("_host",$_SERVER["HTTP_HOST"]);

// getting the different local host
if(isset($_SERVER["HTTP_REFERER"])) {
    // we have the referer for the given url
    $__host = parse_url($_SERVER["HTTP_REFERER"]);
    if(isset($__domain[$__host["host"]])) {
        // let get the actual domain name 
        $_mydomain = $__domain[$__host["host"]];
        if(is_array($_mydomain)) {
            // if array getting the details for the other users
            define("host",$_mydomain["domain"]);
            
            if(isset($_mydomain["parameter"])) {
                // looping and assing value in the get varaible
                foreach($_mydomain["parameter"] as $key => $value) {
                    $_GET[$key] = $value;
                }
            }
            
        } else {
            define("host",$_mydomain);
        }
    } else {
        define("host",$__host["host"]);
    }
    
    
    // host same as the referer host then javascript modification is not required
    define("samehost",(_host == host)?1:0);
} else {
    define("samehost",1);
    define("host",$_SERVER["HTTP_HOST"]);
}


// Getting the execution base folder
define("document",$_SERVER["DOCUMENT_ROOT"]);
$_basefolder =  preg_replace("/core$/ismU","",str_replace(document,"",linuxPath(core)));

define("base",$_basefolder);
define("path",preg_replace("/core$/ismU","",linuxPath(core)));


// create path for the code folder
if(file_exists(path . "sites/" . host)) {
    define("domain",path . "sites/" . host);
} else {
    define("domain",path . "sites/default");
}


// get the URL details
define("url",preg_replace("#" . base ."#ismU","",$_SERVER["REDIRECT_URL"]));

// loading all the configuation file
$_general = glob(path . "config/*.inc.php");
foreach($_general as $key => $value)  {
    include_once $value;
}


$_general = glob(domain . "/config/*.inc.php");

foreach($_general as $key => $value)  {
    include_once $value;
}


/**
 * 
 */
spl_autoload_register(function($class) {
    // load class as per the user request
    if(file_exists(core . "/lib/" . $class . ".php")) {
        include_once(core . "/lib/" . $class . ".php"); return true;
    }
    
    if(file_exists(domain . "/controller/" . $class . ".php")) {
        include_once(domain . "/controller/" . $class . ".php"); return true;
    }    
    
});


$_general = glob(domain . "/function/*.php");

foreach($_general as $key => $value)  {
    include_once $value;
}


// create object of input class
$_input = input::instance();
$_input->post()->get();                        // process post & get value

// check for RAW input of user
$_rawinput = trim(file_get_contents('php://input',true));
if($_rawinput != "" && json_decode($_rawinput) == "") {
    parse_str($_rawinput,$__in);
    $_input->set((array)$__in);
} else if($_rawinput != "") { $_input->set((array)json_decode($_rawinput)); }

$__controller =  explode("/",str_replace(".json","",str_replace(".html","",str_replace(".php","",url))));
if(!isset($__controller[1])) {
    $__controller = "index";
}
$_sec = $__controller[0] . "/" . $__controller[1];


if(isset($__secure)) {
    if(in_array($_sec, $__secure)) {
        if(!isset($_SESSION["_login"]) || $_SESSION["_login"] == false) {
            echo "<h1>You cannot accesss this page </h1>";
            exit;
        }
    }
}