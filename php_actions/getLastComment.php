<?php 	

require_once 'core.php';

$id = $_POST['id'];
$sql = "SELECT additional_data from tblleadactivitylog where leadid=$id and TRIM(COALESCE(additional_data, '')) <> '' and additional_data not like 'VIEW_LOG::%' order by id desc limit 1";
$row = 0;
$result = $connect->query($sql);

if($result->num_rows > 0) { 
 $row = $result->fetch_array();
} // if num_rows
$connect->close();

echo json_encode($row);
