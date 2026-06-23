<?php 	
include("../includes/function.php");
include("../includes/dbcon.php");
require_once 'core.php';

$id = $_POST['id'];
$sql = "SELECT tblleadactivitylog.leadid, tblleadactivitylog.description,  tblleadactivitylog.date,tblleads.dateadded, tblleadactivitylog.staffid, tblleadsstatus.name, tblleadactivitylog.additional_data FROM tblleadactivitylog inner join tblleadsstatus on tblleadactivitylog.description = tblleadsstatus.id inner join tblleads on tblleads.id = tblleadactivitylog.leadid where tblleadactivitylog.leadid=$id order by tblleadactivitylog.date desc";
$result = $connect->query($sql);
$output = array('data' => array());
if($result->num_rows > 0) { 
 while($row = $result->fetch_array()) {
		$commentHtml = '';
		if(!empty(trim((string) $row[6]))) {
			$commentHtml = '<p>'.htmlspecialchars($row[6], ENT_QUOTES, 'UTF-8').'</p>';
		}

		$output['data'][] = '<li><div class="timeline-time">'.time_ago($row[2]).'</div><div class="timeline-icon themed-background "><i class="fa fa-check"></i></div> <div class="timeline-content"> <p class="push-bit"><strong>Status change to '.$row[5].'</strong></p>'.$commentHtml.'</div></li>';
 } // /while 
}// if num_rows

$connect->close();
//print_r($output);
echo json_encode($output);
