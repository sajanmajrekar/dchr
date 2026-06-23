<?php 	



require_once 'core.php';

$sql = "SELECT staffid, firstname, lastname, tblstaff.email, active, admin, COUNT(tblleads.assigned) AS Total FROM `tblstaff` LEFT JOIN `tblleads` ON tblstaff.staffid = tblleads.assigned GROUP BY tblstaff.staffid";

$accesstype="";
$result = $connect->query($sql);

$output = array('data' => array());

if($result->num_rows > 0) { 

 // $row = $result->fetch_array();
 $active = ""; 
 $i= 1;

 while($row = $result->fetch_array()) {
 	$userid = $row[0];
 	if($row[4] == 1) {
 		// activate member
 		$active = "<center><span class='label label-success'>Active</span></center>";
 	} else {
 		// deactivate member
 		$active = "<center><span class='label label-danger'>Deactive</span></center>";
 	} // /else
 	if($row[5] == 0){
 		$accesstype="Users";
 	}else if($row[5] == 1){
 		$accesstype="Admin";
 	}else if($row[5] == 2){
		$accesstype="Superadmin";	
 	}
 	$output['data'][] = array( 
 		//sr no.		
 		$i++,
 		// Username
 		$row[1].' '.$row[2], 
 		// Email
 		$accesstype,

 		$row[3],

 		$active,

 		$row[6],
 		// active
 			
 		); 	
 } // /while 

}// if num_rows

$connect->close();
//print_r($output);
echo json_encode($output);