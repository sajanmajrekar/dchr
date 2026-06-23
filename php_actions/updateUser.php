<?php 	

require_once 'core.php';

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {	
	$id=$_POST['id'];
	$emailtoupdate=$_POST['side-profile-email'];
	$firstname = $_POST['side-profile-fname'];
  	$lastname = $_POST['side-profile-lname']; 
  	$phonenumber = $_POST['side-profile-phone'];
  	$password = md5($_POST['side-profile-password']);
	$sql = "UPDATE tblstaff SET firstname = '$firstname', lastname ='$lastname', phonenumber='$phonenumber',email='$emailtoupdate', password = '$password' WHERE staffid = '$id'";

	if($connect->query($sql) === TRUE) {
	 	$valid['success'] = true;
		$valid['messages'] = "Successfully Updated";	
	} else {
	 	$valid['success'] = false;
	 	$valid['messages'] = "Error while adding the members" . $connect->error;
	}
	 
	$connect->close();

	echo json_encode($valid);
 
} // /if $_POST