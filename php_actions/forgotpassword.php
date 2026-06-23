<?php 	
include('../lib.php');
require_once 'core.php';

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {	
	$email = $_POST['login-email'];
  	$sql1 = "SELECT * FROM `tblstaff` where email='".$email."'";
	$result = $connect->query($sql1);
	if($result->num_rows != 0) { 
				$token = bin2hex(random_bytes(50));
				 // Send email to user with the token in a link they can click on
				    $to = $email;
				    $subject = "Reset your password on digichefs CRM";
				    $msg = "Hi there,</br></br> click on this <a href=\"".$_SERVER['SERVER_NAME']."/hr/forgot.php?token=" . $token . "\">link</a> to reset your password.";
				    //$msg = wordwrap($msg,70);
				    
				    $sql2 = "delete from password_resets WHERE email = '$email'";

					$sql = "INSERT INTO password_resets(email, token) VALUES('$email','$token')";

				if($connect->query($sql2) === True && $connect->query($sql) === TRUE) {
					SendMailHTML($to, $subject, $msg,'','');
					$valid['success'] = true;
					$valid['messages'] = "Email sent!";	
				} else {
					$valid['success'] = false;
					$valid['messages'] = "Error occurs " . $connect->error;
				}
	}else{
		$valid['success'] = false;
		$valid['messages'] = "Sorry, no user exists on our system with that email.";
	}
	$connect->close();
} // /if $_POST
echo json_encode($valid);