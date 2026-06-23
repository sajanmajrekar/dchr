<?php 


require_once 'core.php';

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {	
	$name = $_POST['fullname'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$source = 5;
	$status=20;
	if(isset($_POST['leadassigned'])){
		$assigned = $_POST['leadassigned'];
	}
	$dateadded = date('Y-m-d H:i:s');
  	$sql1 = "SELECT * FROM `tblleads` WHERE email='".$email."'";
  	$sql = "";
	$result = $connect->query($sql1);
	if($result->num_rows == 0) { 
	
			$sql = "INSERT INTO tblleads(name, email, phonenumber, dateadded, source, status) VALUES('$name','$email','$phone','$dateadded','$source', '$status')";
		}
		if($connect->query($sql) === TRUE) {
			$valid['success'] = true;
			$valid['messages'] = "Lead created successfully.";	
		} else {
			$valid['success'] = false;
			$valid['messages'] = "Error while adding the Source" . $connect->error;
		}
	}
	else{
		$valid['success'] = false;
		$valid['messages'] = "Duplicate lead.";
	}
	$connect->close();
 // /if $_POST
echo json_encode($valid);

?>