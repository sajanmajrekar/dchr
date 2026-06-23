<?php 	



require_once 'core.php';

$sql = "SELECT staffid, firstname, lastname, email, phonenumber, active, admin FROM `tblstaff` order by staffid asc";
$accesstype="";
$result = $connect->query($sql);

$output = array('data' => array());

if($result->num_rows > 0) { 

 // $row = $result->fetch_array();
 $active = ""; 
 $i= 1;

 while($row = $result->fetch_array()) {
 	$userid = $row[0];
 	if($row[5] == 1) {
 		// activate member
 		$active = "<center><span class='label label-success'>Active</span></center>";
 	} else {
 		// deactivate member
 		$active = "<center><span class='label label-danger'>Deactive</span></center>";
 	} // /else
 	if($row[6] == 0){
 		$accesstype="Users";
 	}else if($row[6] == 1){
 		$accesstype="Admin";
 	}else if($row[6] == 2){
		$accesstype="Superadmin";	
 	}
 	if($_SESSION['accounttype'] == "superadmin"){
	 	if($row[6] =='2'){
	 		$button="";
	 	}else{
	 	$button = '<center><a data-target="#editUserModal" onclick="editUser('.$userid.')" data-toggle="modal" title="Edit User" class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-pencil"></i></a><a data-target="#removeUserModal" onclick="removeUser('.$userid.')" data-toggle="tooltip" title="Delete User" class="btn btn-effect-ripple btn-xs btn-danger"><i class="fa fa-times"></i></a></center>';
	 	}
	 }else{
	 	$button="";
	 }
 	$output['data'][] = array( 
 		//sr no.		
 		$i++,
 		// Username
 		$row[1].' '.$row[2], 
 		// Email
 		$row[3],
 		// phone
 		$row[4],
 		// active
 		$accesstype,

 		$active,
 		// button
 		$button 		
 		); 	
 } // /while 

}// if num_rows

$connect->close();
//print_r($output);
echo json_encode($output);