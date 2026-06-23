<?php 	

require_once 'core.php';

$SourceId = $_POST['SourceId'];

$sql = "SELECT * FROM tblrole WHERE id = $SourceId";
$result = $connect->query($sql);

if($result->num_rows > 0) { 
 $row = $result->fetch_array();
} // if num_rows
$connect->close();

echo json_encode($row);