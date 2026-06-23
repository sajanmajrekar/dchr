<?php 	



require_once 'core.php';

$output = array('data' => array());
for($i=0; $i<=5;$i++){
$query = "Select * from tblleads where followup='".$i."'";
 $result = $connect->query($query);
 

 	$output['data'][] = array( 
 		//sr no.		
 		$i,
 		// Source name
 		$result->num_rows
 		); 	
 } // /while 

$connect->close();
//print_r($output);
echo json_encode($output);