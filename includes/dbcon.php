<?php ob_start();
date_default_timezone_set("Asia/Kolkata");

// define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE','hrcrm');
define('DB_SERVER', '127.0.0.1');
define('DB_PORT', 3307);

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
	
?>