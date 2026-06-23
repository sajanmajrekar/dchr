<?php

ob_start();
ini_set('display_errors', '0');
error_reporting(E_ALL);
header('Content-Type: application/json');
mysqli_report(MYSQLI_REPORT_OFF);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
        if (ob_get_length()) {
            ob_clean();
        }
        error_log('fetchMyLeads fatal: ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line']);
        echo json_encode(array('data' => array(), 'error' => 'fetchMyLeads fatal: ' . $error['message'], 'debug' => $error['message']));
    }
});

try {
    require_once 'core.php';
    if (!($connect instanceof mysqli)) {
        throw new Exception('Database connection failed.');
    }

    $id = isset($_SESSION['id']) ? $_SESSION['id'] : '';
    $sql ="";
    $isDateSearch = isset($_POST["is_date_search"]) && $_POST["is_date_search"] === "yes";
    if($isDateSearch)
    {
    	$sql = "SELECT tblleads.*, tblleadsstatus.name AS lead_status_name, tblleadssources.name AS lead_source_name FROM tblleads INNER JOIN tblleadssources on tblleads.source = tblleadssources.id inner join tblleadsstatus on tblleads.status = tblleadsstatus.id where ";

    	if(!empty($_POST["roles"])){
    		$sql .= "tblleads.roles like '%".$_POST["roles"]."%' ";
    		if(!empty($_POST["leadsource"]) || !empty($_POST["leadstatus"]) || !empty($_POST["experiance"])){
    			$sql .= "and ";
    		}
    	}
    	if(!empty($_POST["leadsource"])){
    		$sql .= "tblleads.source='".$_POST["leadsource"]."' ";
    		if(!empty($_POST["leadstatus"]) || !empty($_POST["experiance"])){
    			$sql .= "and ";
    		}
    	}
    	if(!empty($_POST["leadstatus"])){
     		$sql .= "tblleads.status=".$_POST["leadstatus"]." ";
     		if(!empty($_POST["experiance"])){
    			$sql .= "and ";
    		}
     	}
    	if(!empty($_POST["experiance"])){
     		$sql .= "tblleads.experiance like '%".$_POST["experiance"]."%' ";
     	}
     	$sql .= "order by tblleads.dateadded desc";
    }
    else{
    $sql = "SELECT tblleads.*, tblleadsstatus.name AS lead_status_name, tblleadssources.name AS lead_source_name FROM tblleads INNER JOIN tblleadssources on tblleads.source = tblleadssources.id inner join tblleadsstatus on tblleads.status = tblleadsstatus.id order by tblleads.dateadded desc";
    }
    $result = $connect->query($sql);

    if ($result === false) {
        throw new Exception('Database query failed: ' . $connect->error . ' | SQL: ' . $sql);
    }

    $output = array('data' => array());

    if($result->num_rows > 0) {
     while($row = $result->fetch_array()) {
        $leadid = isset($row['id']) ? $row['id'] : '';
        $leadName = isset($row['name']) ? $row['name'] : '';
        $leadEmail = isset($row['email']) ? $row['email'] : '';
        $leadPhone = isset($row['phonenumber']) ? $row['phonenumber'] : '';
        $leadCity = isset($row['city']) ? $row['city'] : '';
        $leadRoles = isset($row['roles']) ? $row['roles'] : '';
        $leadExperience = isset($row['experiance']) ? $row['experiance'] : '';
        $leadCurrentSalary = isset($row['csalary']) ? $row['csalary'] : '';
        $leadExpectedSalary = isset($row['esalary']) ? $row['esalary'] : '';
        $leadNoticePeriod = isset($row['nperiod']) ? $row['nperiod'] : '';
        $leadDateAdded = isset($row['dateadded']) ? $row['dateadded'] : '';
        $leadLastContact = isset($row['lastcontact']) ? $row['lastcontact'] : '';
        $leadStatusName = isset($row['lead_status_name']) ? $row['lead_status_name'] : '';
        $leadResume = isset($row['resume']) ? $row['resume'] : '';

    	if(!empty($leadResume)){
    	$resumeUrl = 'view_resume.php?file=' . rawurlencode($leadResume);
    	$button = '<center><a href="'.$resumeUrl.'" title="View Resume" target="_blank" class="btn btn-effect-ripple btn-xs btn-success"><i class="fa  fa-eye"></i></a><a data-target="#editMyLeadModal" onclick="editMyLead('.$leadid.')" data-toggle="modal" title="Edit User" class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-pencil"></i></a>';
    	}else{
     	 	$button = '<center><a data-target="#editMyLeadModal" onclick="editMyLead('.$leadid.')" data-toggle="modal" title="Edit User" class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-pencil"></i></a>';
    	}
     	$output['data'][] = array(
     		'<center><label class="csscheckbox csscheckbox-primary"><input type="checkbox" class="checkmark" name="leadcheckbox" value="'.$leadid.'"><span></span></label></center>',
     		$button,
     		'<a data-target="#editMyLeadModal" onclick="editMyLead('.$leadid.')" href="#" data-toggle="modal" title="'. $leadName.'">'. $leadName.'</a>',
     		$leadEmail,
     		$leadPhone,
     		$leadCity,
     		getroletext($leadRoles),
     		$leadExperience,
     		$leadCurrentSalary,
     		$leadExpectedSalary,
     		$leadNoticePeriod,
     		!empty($leadDateAdded) ? date("d M, Y",strtotime($leadDateAdded)) : '',
     		time_ago($leadLastContact),
     		$leadStatusName
     	);
     }
    }

    $connect->close();
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode($output);
} catch (Throwable $e) {
    if (isset($connect) && $connect instanceof mysqli) {
        $connect->close();
    }
    if (ob_get_length()) {
        ob_clean();
    }
    error_log('fetchMyLeads exception: ' . $e->getMessage());
    echo json_encode(array('data' => array(), 'error' => 'fetchMyLeads exception: ' . $e->getMessage(), 'debug' => $e->getMessage()));
}
