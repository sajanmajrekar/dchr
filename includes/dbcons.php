<?php ob_start();
date_default_timezone_set("Asia/Kolkata");

// define('DB_SERVER', 'localhost');
// define('DB_USERNAME', 'liveapzm_live101');
// define('DB_PASSWORD', 'live@1234');
// define('DB_DATABASE','liveapzm_live101');

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'liveapzm_netel');
define('DB_PASSWORD', 'netel*786');
define('DB_DATABASE','liveapzm_netel');


$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
	
?>