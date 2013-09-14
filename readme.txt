Platform  & SEO friendly framework
-----------------------------------------------------------

Simple and fast framework to develop good gamification site, quality html to 
rank your website high at Google.


code folder
----------------------------------------------------------------------------
All the custom code will be placed under the sites folder with domain name as
there folder name. eg: if kumar.ws is the folder then create the folder as kumar.ws
default folder will be looked if the custom site folder is not avaliable


Configuration
----------------------------------------------------------
/domain.inc.php

$__domain array will hold the key & value combination of the domain routing value
$__domain["www.koolphp.com"] = array("domain" => "koolphp.com", "parameter" => array("name" => "Test"));
this setting will help user to route 2 site 1 common code base eg www.koolphp.com &n koolphp.com will 
share the common code. 

/config/database.inc.php    
All the database configuration should be in $__database array