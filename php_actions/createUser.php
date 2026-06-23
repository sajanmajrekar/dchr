<?php 	

require_once 'core.php';

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {	
	$fname = $_POST['adduser-fname'];
	$lname = $_POST['adduser-lname'];
  	$password = md5($_POST['adduser-password']);
  	$email = $_POST['adduser-email'];
  	$phone = $_POST['adduser-phone'];
  	$rights = $_POST['rights'];
  	$status = $_POST['action'];
  	$date = date('Y-m-d H:i:s');
  	$sql1 = "SELECT * FROM `tblstaff` where email='".$email."'";
	$result = $connect->query($sql1);
	if($result->num_rows == 0) { 

				$sql = "INSERT INTO tblstaff(firstname, lastname, phonenumber, password, email, admin, active,datecreated) VALUES('$fname', '$lname','$phone', '$password', '$email', $rights, $status,'$date')";

				if($connect->query($sql) === TRUE) {
					$valid['success'] = true;
					$valid['messages'] = "New User created successfully!";	
				} else {
					$valid['success'] = false;
					$valid['messages'] = "Error while adding the members" . $connect->error;
				}
	}else{
		$valid['success'] = false;
		$valid['messages'] = "Email address already exists!";
	}
	$connect->close();
} // /if $_POST
echo json_encode($valid);