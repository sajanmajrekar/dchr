<?php 	



require_once 'core.php';

$sql = "SELECT * FROM `tblleadssources` order by id asc";

$result = $connect->query($sql);

$output = array('data' => array());

if($result->num_rows > 0) { 

 $active = ""; 
 $i= 1;

 while($row = $result->fetch_array()) {
 	$id = $row[0];
 	$sql1 = "SELECT * FROM `tblleads` where source=$id";
	$result1 = $connect->query($sql1);
 	$total=$result1->num_rows;
 	$button = '<center><a data-target="#editSourceModal" onclick="editSource('.$id.')" data-toggle="modal" title="Edit User" class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-pencil"></i></a><a data-target="#removeUserModal" onclick="removeSource('.$id.')" data-toggle="tooltip" title="Delete User" class="btn btn-effect-ripple btn-xs btn-danger"><i class="fa fa-times"></i></a></center>';
 	$output['data'][] = array( 
 		//sr no.		
 		$i++,
 		// Source name
 		$row[1],

 		$total		
 		); 	
 } // /while 

}// if num_rows

$connect->close();
//print_r($output);
echo json_encode($output);