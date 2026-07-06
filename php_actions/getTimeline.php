<?php 	
include("../includes/function.php");
include("../includes/dbcon.php");
require_once 'core.php';

$id = $_POST['id'];
$sql = "SELECT tblleadactivitylog.leadid, tblleadactivitylog.description, tblleadactivitylog.date, tblleads.dateadded, tblleadactivitylog.staffid, tblleadsstatus.name, tblleadactivitylog.additional_data, tblstaff.firstname, tblstaff.lastname FROM tblleadactivitylog inner join tblleadsstatus on tblleadactivitylog.description = tblleadsstatus.id inner join tblleads on tblleads.id = tblleadactivitylog.leadid left join tblstaff on tblleadactivitylog.staffid = tblstaff.staffid where tblleadactivitylog.leadid=$id order by tblleadactivitylog.date desc";
$result = $connect->query($sql);
$output = array('data' => array());
if($result->num_rows > 0) { 
 while($row = $result->fetch_assoc()) {
		$additionalData = trim((string) $row['additional_data']);
		$actorName = trim($row['firstname'] . ' ' . $row['lastname']);

		if (strpos($additionalData, 'VIEW_LOG::') === 0) {
			$viewerName = trim(substr($additionalData, strlen('VIEW_LOG::')));
			if ($viewerName === '') {
				$viewerName = $actorName !== '' ? $actorName : 'A team member';
			}

			$output['data'][] = '<li class="timeline-item-viewed"><div class="timeline-time">'.time_ago($row['date']).'</div><div class="timeline-icon themed-background"><i class="fa fa-eye"></i></div><div class="timeline-content"><p class="push-bit"><strong>Candidate viewed by '.htmlspecialchars($viewerName, ENT_QUOTES, 'UTF-8').'</strong></p></div></li>';
			continue;
		}

		$commentHtml = '';
		if($additionalData !== '') {
			$commentHtml = '<p>'.htmlspecialchars($additionalData, ENT_QUOTES, 'UTF-8').'</p>';
		}

		$actorHtml = '';
		if ($actorName !== '') {
			$actorHtml = '<span class="candidate-activity-user">Updated by '.htmlspecialchars($actorName, ENT_QUOTES, 'UTF-8').'</span>';
		}

		$output['data'][] = '<li><div class="timeline-time">'.time_ago($row['date']).'</div><div class="timeline-icon themed-background "><i class="fa fa-check"></i></div> <div class="timeline-content"> <p class="push-bit"><strong>Status change to '.htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8').'</strong></p>'.$actorHtml.$commentHtml.'</div></li>';
 } // /while 
}// if num_rows

$connect->close();
//print_r($output);
echo json_encode($output);
