<?php 	



require_once 'core.php';

$sql = "SELECT tblleads.id, tblleads.name, tblleads.email, tblleads.phonenumber, tblleads.dateadded, tblleads.status, tblleads.source, tblleadsstatus.name, tblleadssources.name FROM tblleads INNER JOIN tblleadssources on tblleads.source = tblleadssources.id inner join tblleadsstatus on tblleads.status = tblleadsstatus.id where assigned=0";

$result = $connect->query($sql);

$output = array('data' => array());

if($result->num_rows > 0) { 

 // $row = $result->fetch_array();
 $active = ""; 
 $i= 1;

 while($row = $result->fetch_array()) {
 	$leadid = $row[0];
 	if($row[5] == 1) {
 		// activate member
 		$active = "<center><span class='label label-success'>Active</span></center>";
 	} else {
 		// deactivate member
 		$active = "<center><span class='label label-danger'>Deactive</span></center>";
 	} // /else
 	$button = '<center><a data-target="#editUserModal" onclick="editUser('.$leadid.')" data-toggle="modal" title="Edit User" class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-pencil"></i></a><a data-target="#removeUserModal" onclick="removeUser('.$leadid.')" data-toggle="tooltip" title="Delete User" class="btn btn-effect-ripple btn-xs btn-danger"><i class="fa fa-times"></i></a></center>';
 	$output['data'][] = array( 
 		//sr no.		
 		'<center><label class="csscheckbox csscheckbox-primary"><input type="checkbox" name="leadcheckbox" value="'.$leadid.'"><span></span></label></center>',
 		// Username
 		$row[1],
 		// Email
 		$row[2],
 		// phone
 		$row[3],
 		// active
 		date("d-m-Y",strtotime($row[4])),
 		//source
 		$row[8]
 		); 	
 } // /while 

}// if num_rows

$connect->close();
//print_r($output);
echo json_encode($output);