<?php 	

require_once 'core.php';


$valid['success'] = array('success' => false, 'messages' => array());

$statusid = $_POST['statusid'];
if($statusid) { 

 $sql1 = "select * from `tblleads` WHERE status = {$statusid}";
 $result = $connect->query($sql1);
 $row = $result->fetch_object();
 if($result->num_rows <= 0){
 	$sql = "delete from `tblleadsstatus` WHERE id = {$statusid}";
	 if($connect->query($sql) === TRUE) {
	 	$valid['success'] = true;
		$valid['messages'] = "Successfully Removed";		
	 } else {
	 	$valid['success'] = false;
	 	$valid['messages'] = "Error while removing the user.";
	 }
}
 else{
 	$valid['success'] = false;
 	$valid['messages'] = "The ID of the lead status is already using.";
 }
 $connect->close();
 echo json_encode($valid);
 
} // /if $_POST