<?php 	

require_once 'core.php';

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {	
	$leadid=$_POST['checkbox'];
	$id=$_SESSION['id'];

	while ($currentleadid = current($leadid)) {
    	$sql = "UPDATE tblleads SET assigned = '$id' WHERE id = '$currentleadid'";

		if($connect->query($sql) === TRUE) {
		 	$valid['success'] = true;
			$valid['messages'] = "Successfully Updated";	
		} else {
		 	$valid['success'] = false;
		 	$valid['messages'] = "Error while updating the lead" . $connect->error;
		}
    	next($leadid);
	}
	$connect->close();

	echo json_encode($valid);
 
} // /if $_POST