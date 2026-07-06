<?php 	

require_once 'core.php';


$valid['success'] = array('success' => false, 'messages' => array());

$userid = $_POST['userid'];
if($userid) { 
 if(!isset($_SESSION['accounttype']) || $_SESSION['accounttype'] !== 'superadmin'){
 	$valid['success'] = false;
 	$valid['messages'] = "Only Superadmin can remove users.";
 	echo json_encode($valid);
 	exit();
 }

 $sql1 = "select * from `tblstaff` WHERE staffid = {$userid}";
 $result = $connect->query($sql1);
 $row = $result->fetch_object();
 if($row && $row->admin != 2){
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
 	$valid['messages'] = "You can't remove the superadmin account.";
 }
 $connect->close();
 echo json_encode($valid);
 
} // /if $_POST
