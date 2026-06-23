<?php 	



require_once 'core.php';

$sql = "SELECT emaillogs.id, emaillogs.subject, emaillogs.senttime, tblstaff.staffid, tblstaff.firstname FROM emaillogs INNER JOIN tblstaff on emaillogs.sentby = tblstaff.staffid order by emaillogs.senttime desc";

$result = $connect->query($sql);

$output = array('data' => array());

if($result->num_rows > 0) { 

 // $row = $result->fetch_array();
 $active = ""; 
 $i= 1;

 while($row = $result->fetch_array()) {
 	$leadid = $row[0];
 	$button = '<center><a data-target="#editMyLeadModal" onclick="editUser('.$leadid.')" data-toggle="modal" title="Edit User" class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-eye"></i></a></center>';
 	$output['data'][] = array( 
 		$button,
 		$row['subject'],
 		// Email
 		date("d-m-Y",strtotime($row['senttime'])),
 		// phone
 		$row['firstname']
 		); 	
 } // /while 

}// if num_rows

$connect->close();
//print_r($output);
echo json_encode($output);