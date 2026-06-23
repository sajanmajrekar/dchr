<?php 	



require_once 'core.php';

$sql = "SELECT tblleadsstatus.id, tblleadsstatus.name, COUNT(tblleads.status) AS Total FROM `tblleadsstatus` LEFT JOIN `tblleads` ON tblleadsstatus.id = tblleads.status GROUP BY tblleadsstatus.id";

$accesstype="";
$result = $connect->query($sql);

$output = array('data' => array());

if($result->num_rows > 0) { 

 // $row = $result->fetch_array();
 $active = ""; 
 $i= 1;

 while($row = $result->fetch_array()) {
 	
 	$output['data'][] = array( 
 		//sr no.		
 		$i++,
 		// Username
 		$row[1],
 		$row[2]

 			
 		); 	
 } // /while 

}// if num_rows

$connect->close();
//print_r($output);
echo json_encode($output);