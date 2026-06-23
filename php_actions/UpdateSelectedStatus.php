<?php 	

require_once 'core.php';

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {	
	$id=$_POST['Editstatus-id'];
	$name = addslashes($_POST['EditStatus-name']);
	$sql = "UPDATE tblleadsstatus SET name = '$name' WHERE id = '$id'";

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