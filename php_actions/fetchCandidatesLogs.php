<?php 	



require_once 'core.php';

$id = $_POST['Id'];
$sql = "SELECT * from emaillogs where id=$id";
$row = 0;
$candiates;
$result = $connect->query($sql);

if($result->num_rows > 0) { 
 $row = $result->fetch_array();
 $candiates = $row["mailsentto"];
} // if num_rows


$sql1 = "SELECT * FROM tblleads where id in ($candiates)";

$result = $connect->query($sql1);

$output = array('data' => array());

if($result->num_rows > 0) { 

 // $row = $result->fetch_array();
 $active = ""; 
 $i= 1;

 while($row = $result->fetch_array()) {
 	$leadid = $row[0];
 	$button = '<center><a data-target="#editMyLeadModal" onclick="editUser('.$leadid.')" data-toggle="modal" title="Edit User" class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-eye"></i></a></center>';
 	$output['data'][] = array( 
 	    $i++,
 		$row['name'],
 		getroletext($row['roles']),
 		// Email
 		date("d-m-Y",strtotime($row['dateadded'])),
 		// phone
 		time_ago($row['lastcontact']),
 		); 	
 } // /while 

}// if num_rows
//	$output['data'][] = array( $sql1);
$connect->close();
//print_r($output);
echo json_encode($output);