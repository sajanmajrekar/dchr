<?php

require_once 'core.php';

$response = array(
    'success' => false,
    'viewer_name' => ''
);

if (!isset($_SESSION['id']) || !isset($_POST['id'])) {
    echo json_encode($response);
    exit;
}

$leadId = (int) $_POST['id'];
$staffId = (int) $_SESSION['id'];

if ($leadId <= 0 || $staffId <= 0) {
    echo json_encode($response);
    exit;
}

$viewerName = 'A team member';
$viewerQuery = $connect->query("SELECT firstname, lastname FROM tblstaff WHERE staffid = {$staffId} LIMIT 1");
if ($viewerQuery && $viewerQuery->num_rows > 0) {
    $viewerRow = $viewerQuery->fetch_assoc();
    $viewerName = trim($viewerRow['firstname'] . ' ' . $viewerRow['lastname']);
    if ($viewerName === '') {
        $viewerName = isset($_SESSION['user']) ? trim((string) $_SESSION['user']) : 'A team member';
    }
}

$leadStatus = 20;
$leadQuery = $connect->query("SELECT status FROM tblleads WHERE id = {$leadId} LIMIT 1");
if ($leadQuery && $leadQuery->num_rows > 0) {
    $leadRow = $leadQuery->fetch_assoc();
    $leadStatus = (int) $leadRow['status'];
}

$recentViewQuery = $connect->query("SELECT id FROM tblleadactivitylog WHERE leadid = {$leadId} AND staffid = {$staffId} AND additional_data LIKE 'VIEW_LOG::%' AND date >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) ORDER BY id DESC LIMIT 1");
if ($recentViewQuery && $recentViewQuery->num_rows > 0) {
    $response['success'] = true;
    $response['viewer_name'] = $viewerName;
    echo json_encode($response);
    exit;
}

$date = date('Y-m-d H:i:s');
$viewMessage = $connect->real_escape_string('VIEW_LOG::' . $viewerName);
$insertSql = "INSERT INTO tblleadactivitylog (leadid, description, date, staffid, additional_data) VALUES ({$leadId}, {$leadStatus}, '{$date}', {$staffId}, '{$viewMessage}')";

if ($connect->query($insertSql) === TRUE) {
    $response['success'] = true;
    $response['viewer_name'] = $viewerName;
}

echo json_encode($response);

?>
