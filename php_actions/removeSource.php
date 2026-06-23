<?php 	

require_once 'core.php';


$valid['success'] = array('success' => false, 'messages' => array());

$sourceid = $_POST['sourceid'];
if($sourceid) { 

 $sql1 = "select * from `tblleads` WHERE source = {$sourceid}";
 $result = $connect->query($sql1);
 $row = $result->fetch_object();
 if($result->num_rows <= 0){
 	$sql = "delete from `tblleadssources` WHERE id = {$sourceid}";
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
 	$valid['messages'] = "The ID of the lead source is already using.";
 }
 $connect->close();
 echo json_encode($valid);
 
} // /if $_POST