<?php 	

require_once 'core.php';

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {	
	$name = addslashes($_POST['addsource-name']);
  	$sql1 = "SELECT * FROM `tblrole` where name='".$name."'";
	$result = $connect->query($sql1);
	if($result->num_rows == 0) { 

				$sql = "INSERT INTO tblrole(name) VALUES('$name')";

				if($connect->query($sql) === TRUE) {
					$valid['success'] = true;
					$valid['messages'] = "New Role created successfully!";	
				} else {
					$valid['success'] = false;
					$valid['messages'] = "Error while adding the Role" . $connect->error;
				}
	}else{
		$valid['success'] = false;
		$valid['messages'] = "Role already exists!";
	}
	$connect->close();
} // /if $_POST
echo json_encode($valid);