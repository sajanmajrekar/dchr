<?php 	

require_once 'core.php';

$UserId = $_POST['UserId'];

$sql = "SELECT * FROM tblstaff WHERE staffid = $UserId";
$result = $connect->query($sql);

if($result->num_rows > 0) { 
 $row = $result->fetch_array();
} // if num_rows
$_SESSION["fields"] = $row[2];
$connect->close();

echo json_encode($row);