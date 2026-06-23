<?php 	

require_once 'core.php';

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {	
	$name = addslashes($_POST['addstatus-name']);
  	$sql1 = "SELECT * FROM `tblleadsstatus` where name='".$name."'";
	$result = $connect->query($sql1);
	if($result->num_rows == 0) { 

				$sql = "INSERT INTO tblleadsstatus(name) VALUES('$name')";

				if($connect->query($sql) === TRUE) {
					$valid['success'] = true;
					$valid['messages'] = "New Status created successfully!";	
				} else {
					$valid['success'] = false;
					$valid['messages'] = "Error while adding the Source" . $connect->error;
				}
	}else{
		$valid['success'] = false;
		$valid['messages'] = "Status already exists!";
	}
	$connect->close();
} // /if $_POST
echo json_encode($valid);