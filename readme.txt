Platform  & SEO friendly framework
-----------------------------------------------------------

Simple and fast framework to develop good gamification site, quality html to 
rank your website high at Google.


Technology Used
----------------------------------------------------------------------
PHP
JQuery
HTML 5
MySQL

Learning :-
-----------------------------------------------------------------------------
1. Creating module in javascript
2. Implemenation of callback funcationalty.
3. SEO technics ( Not got the  time to implement it completely)


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


$__secure  => make the page accessable after login validation

Event
---------------------------------------------------------------------
Every single table access has a  event, that has to be placed in event folder of specific domain
for example user table can have user.php in the event folder with function

beforeselect();
onmove()
beforeinsert()
afterinsert()
beforeupdate()
afterupdate()
beforedelete()
afterdelete()


controller
---------------------------------------------------------------------------
hold the controller for the given url, for url
product/details.html

product.php from controller will be load and 'product' class is opened
and detailsAction function is called 



