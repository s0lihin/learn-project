<?php // Rememeber to change the username,password and database name to acutal values
define('DB_HOST','mysql'); // localhost is the default host
define('DB_USER','root'); 
define('DB_PASS','example');
define('DB_NAME','restaurantdb'); // database name

//Create Connection
$link = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

//Check COnnection
if($link->connect_error){ //if not Connection
die('Connection Failed'.$link->connect_error);//kills the Connection OR terminate execution
}

if (!$link->set_charset("utf8")) {
    die("Error loading character set utf8: " . $link->error);
}
?>