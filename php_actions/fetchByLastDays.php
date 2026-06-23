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
        error_log('fetchByLastDays fatal: ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line']);
        echo json_encode(array('data' => array(), 'error' => 'fetchByLastDays fatal: ' . $error['message'], 'debug' => $error['message']));
    }
});

try {
    require_once 'core.php';
    if (!($connect instanceof mysqli)) {
        throw new Exception('Database connection failed.');
    }
    $id = isset($_SESSION['id']) ? $_SESSION['id'] : '';
    $interval = isset($_POST["interval"]) ? $_POST["interval"] : '';
    if($interval == "last-seven")
    {
    	$sql = "SELECT tblleads.id, tblleads.name, tblleads.email, tblleads.phonenumber, tblleads.dateadded, tblleads.status, tblleads.source, tblleadsstatus.name, tblleadssources.name, tblleads.lastcontact FROM tblleads INNER JOIN tblleadssources on tblleads.source = tblleadssources.id inner join tblleadsstatus on tblleads.status = tblleadsstatus.id where tblleads.dateadded >= CURRENT_DATE - INTERVAL 7 DAY order by tblleads.dateadded";
    }
    else if($interval == "last-thirty"){
    $sql = "SELECT tblleads.id, tblleads.name, tblleads.email, tblleads.phonenumber, tblleads.dateadded, tblleads.status, tblleads.source, tblleadsstatus.name, tblleadssources.name, tblleads.lastcontact FROM tblleads INNER JOIN tblleadssources on tblleads.source = tblleadssources.id inner join tblleadsstatus on tblleads.status = tblleadsstatus.id where tblleads.dateadded >= CURRENT_DATE - INTERVAL 30 DAY order by tblleads.dateadded";
    }else if($interval == "last-month"){
    $sql = "SELECT tblleads.id, tblleads.name, tblleads.email, tblleads.phonenumber, tblleads.dateadded, tblleads.status, tblleads.source, tblleadsstatus.name, tblleadssources.name, tblleads.lastcontact FROM tblleads INNER JOIN tblleadssources on tblleads.source = tblleadssources.id inner join tblleadsstatus on tblleads.status = tblleadsstatus.id where tblleads.dateadded >= CURRENT_DATE - INTERVAL 3 month order by tblleads.dateadded";
    }else{
    	$sql = "SELECT tblleads.id, tblleads.name, tblleads.email, tblleads.phonenumber, tblleads.dateadded, tblleads.status, tblleads.source, tblleadsstatus.name, tblleadssources.name, tblleads.lastcontact FROM tblleads INNER JOIN tblleadssources on tblleads.source = tblleadssources.id inner join tblleadsstatus on tblleads.status = tblleadsstatus.id order by tblleads.dateadded";
    }
    $result = $connect->query($sql);

    if ($result === false) {
        throw new Exception('Database query failed: ' . $connect->error . ' | SQL: ' . $sql);
    }

    $output = array('data' => array());

    if($result->num_rows > 0) {
     while($row = $result->fetch_array()) {
     	$leadid = $row[0];
     	$button = '<center><a data-target="#editMyLeadModal" onclick="editMyLead('.$leadid.')" data-toggle="modal" title="Edit User" class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-pencil"></i></a>';
     	$output['data'][] = array(
     		'<center><label class="csscheckbox csscheckbox-primary"><input type="checkbox" class="checkmark" name="leadcheckbox" value="'.$leadid.'"><span></span></label></center>',
     		$button,
     		'<a data-target="#editMyLeadModal" onclick="editMyLead('.$leadid.')" href="#" data-toggle="modal" title="'. $row[1].'">'. $row[1].'</a>',
     		$row[2],
     		$row[3],
     		date("d M, Y",strtotime($row[4])),
     		time_ago($row[9]),
     		$row[8],
     		$row[7]
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
    error_log('fetchByLastDays exception: ' . $e->getMessage());
    echo json_encode(array('data' => array(), 'error' => 'fetchByLastDays exception: ' . $e->getMessage(), 'debug' => $e->getMessage()));
}
