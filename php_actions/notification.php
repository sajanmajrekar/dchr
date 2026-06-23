<?php 	
require_once 'core.php';
$id=$_SESSION['id'];

	$output = array('data' => array());

	$totalleads = "SELECT tblleads.id, tblleads.status, tblleads.source, tblleadsstatus.name, tblleadssources.name, tblleads.lastcontact FROM tblleads INNER JOIN tblleadssources on tblleads.source = tblleadssources.id inner join tblleadsstatus on tblleads.status = tblleadsstatus.id where tblleads.assigned = ".$id."";
	

	//$totalleads = "SELECT * from tblleads";
	$newleads = "SELECT tblleads.id, tblleads.status, tblleads.source, tblleadsstatus.name, tblleadssources.name, tblleads.lastcontact FROM tblleads INNER JOIN tblleadssources on tblleads.source = tblleadssources.id inner join tblleadsstatus on tblleads.status = tblleadsstatus.id where tblleads.status=20 and tblleads.assigned = ".$id."";

	$otherleads = "SELECT tblleads.id, tblleads.status, tblleads.source, tblleadsstatus.name, tblleadssources.name, tblleads.lastcontact FROM tblleads INNER JOIN tblleadssources on tblleads.source = tblleadssources.id inner join tblleadsstatus on tblleads.status = tblleadsstatus.id where tblleads.assigned = ".$id."";

	$result = $connect->query($totalleads);
	$result1 = $connect->query($newleads);
	$result2 = $connect->query($otherleads);

 	$output['data'][] = array( 
 		"Total Assigned Leads: " .$result->num_rows. "\nNew Leads: " .$result1->num_rows."\nIn progress Leads: "  .$result2->num_rows
 	);

 	$connect->close();
	echo json_encode($output);