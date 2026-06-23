<?php 	

require_once 'core.php';


$valid['success'] = array('success' => false, 'messages' => array());

$userid = $_POST['userid'];
if($userid) { 

 $sql1 = "select * from `tblstaff` WHERE staffid = {$userid}";
 $result = $connect->query($sql1);
 $row = $result->fetch_object();
 if(!$row->admin){
 	$sql = "delete from `tblstaff` WHERE staffid = {$userid}";
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
 	$valid['messages'] = "You can't remove the admin account.";
 }
 $connect->close();
 echo json_encode($valid);
 
} // /if $_POST