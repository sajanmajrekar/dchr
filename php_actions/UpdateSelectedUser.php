<?php 	

require_once 'core.php';

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {	
	if(!isset($_SESSION['accounttype']) || $_SESSION['accounttype'] !== 'superadmin'){
		$valid['success'] = false;
		$valid['messages'] = "Only Superadmin can manage users.";
		echo json_encode($valid);
		exit();
	}

	$id=$_POST['Edituser-id'];
	$emailtoupdate=$_POST['Edituser-email'];
	$firstname = $_POST['Edituser-fname'];
  	$lastname = $_POST['Edituser-lname']; 
  	$phonenumber = $_POST['Edituser-phone'];
  	$password = md5($_POST['Edituser-password']);
  	$rights = (int) $_POST['edit-rights'];
  	$status = $_POST['edit-action'];
	date_default_timezone_set('Asia/Kolkata');
  	$date = date('Y-m-d H:i:s');
  	if(!empty($_POST['Edituser-password'])){
	$sql = "UPDATE tblstaff SET firstname = '$firstname', lastname ='$lastname', phonenumber='$phonenumber',email='$emailtoupdate', password = '$password', admin ='$rights', active ='$status'  WHERE staffid = '$id'";
	}else{
		$sql = "UPDATE tblstaff SET firstname = '$firstname', lastname ='$lastname', phonenumber='$phonenumber',email='$emailtoupdate', admin ='$rights', active ='$status'  WHERE staffid = '$id'";
	}

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
