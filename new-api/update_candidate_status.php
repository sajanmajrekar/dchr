<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once '../php_actions/core.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array('success' => false, 'message' => 'Only POST is allowed.'));
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    $payload = $_POST;
}

$leadId = isset($payload['lead_id']) ? (int) $payload['lead_id'] : 0;
$statusId = isset($payload['status_id']) ? (int) $payload['status_id'] : 0;
$comment = isset($payload['comment']) ? trim((string) $payload['comment']) : '';
$staffId = isset($_SESSION['id']) ? (int) $_SESSION['id'] : (isset($_SESSION['staffid']) ? (int) $_SESSION['staffid'] : 0);
if ($staffId <= 0 && isset($payload['staff_id'])) {
    $staffId = (int) $payload['staff_id'];
}

if ($leadId <= 0 || $statusId <= 0) {
    http_response_code(422);
    echo json_encode(array('success' => false, 'message' => 'Lead and status are required.'));
    exit;
}

$leadStmt = $connect->prepare("SELECT status FROM tblleads WHERE id = ? LIMIT 1");
if (!$leadStmt) {
    http_response_code(500);
    echo json_encode(array('success' => false, 'message' => 'Could not prepare lead lookup.'));
    exit;
}

$leadStmt->bind_param('i', $leadId);
$leadStmt->execute();
$leadResult = $leadStmt->get_result();
$lead = $leadResult ? $leadResult->fetch_assoc() : null;
$leadStmt->close();

if (!$lead) {
    http_response_code(404);
    echo json_encode(array('success' => false, 'message' => 'Candidate not found.'));
    exit;
}

$statusStmt = $connect->prepare("SELECT name FROM tblleadsstatus WHERE id = ? LIMIT 1");
if (!$statusStmt) {
    http_response_code(500);
    echo json_encode(array('success' => false, 'message' => 'Could not prepare status lookup.'));
    exit;
}

$statusStmt->bind_param('i', $statusId);
$statusStmt->execute();
$statusResult = $statusStmt->get_result();
$status = $statusResult ? $statusResult->fetch_assoc() : null;
$statusStmt->close();

if (!$status) {
    http_response_code(422);
    echo json_encode(array('success' => false, 'message' => 'Selected status is not valid.'));
    exit;
}

$oldStatusId = isset($lead['status']) ? (int) $lead['status'] : 0;
$statusChanged = $oldStatusId !== $statusId;
$hasComment = $comment !== '';
$date = date('Y-m-d H:i:s');
$hrName = '';
$hrProfileImage = '';
$hrProfileImageUrl = '';

if ($staffId > 0) {
    $staffStmt = $connect->prepare("SELECT TRIM(CONCAT(COALESCE(firstname, ''), ' ', COALESCE(lastname, ''))) AS name, profile_image FROM tblstaff WHERE staffid = ? LIMIT 1");
    if ($staffStmt) {
        $staffStmt->bind_param('i', $staffId);
        $staffStmt->execute();
        $staffResult = $staffStmt->get_result();
        $staff = $staffResult ? $staffResult->fetch_assoc() : null;
        $staffStmt->close();
        if ($staff && trim((string) $staff['name']) !== '') {
            $hrName = trim((string) $staff['name']);
            $hrProfileImage = trim((string) $staff['profile_image']);
        }
    }
}

if ($hrName === '') {
    $staffId = 0;
}

if ($statusChanged) {
    $updateStmt = $connect->prepare("UPDATE tblleads SET status = ?, lastcontact = ?, followup = followup + 1 WHERE id = ?");
    if (!$updateStmt) {
        http_response_code(500);
        echo json_encode(array('success' => false, 'message' => 'Could not prepare status update.'));
        exit;
    }
    $updateStmt->bind_param('isi', $statusId, $date, $leadId);
} else {
    $updateStmt = $connect->prepare("UPDATE tblleads SET lastcontact = ? WHERE id = ?");
    if (!$updateStmt) {
        http_response_code(500);
        echo json_encode(array('success' => false, 'message' => 'Could not prepare contact update.'));
        exit;
    }
    $updateStmt->bind_param('si', $date, $leadId);
}

$updated = $updateStmt->execute();
$updateStmt->close();

if (!$updated) {
    http_response_code(500);
    echo json_encode(array('success' => false, 'message' => 'Could not update candidate status.'));
    exit;
}

if ($statusChanged || $hasComment) {
    $logStmt = $connect->prepare("INSERT INTO tblleadactivitylog (leadid, description, date, staffid, additional_data) VALUES (?, ?, ?, ?, ?)");
    if ($logStmt) {
        $description = (string) $statusId;
        $logStmt->bind_param('issis', $leadId, $description, $date, $staffId, $comment);
        $logStmt->execute();
        $logStmt->close();
    }
}

if ($hrProfileImage !== '') {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $origin = ($isHttps ? 'https://' : 'http://') . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
    $scriptDir = str_replace('\\', '/', dirname(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/hr/new-api/update_candidate_status.php'));
    $appBasePath = rtrim(dirname($scriptDir), '/');
    if ($appBasePath === '' || $appBasePath === '.') {
        $appBasePath = '';
    }
    $hrProfileImageUrl = preg_match('/^https?:\/\//i', $hrProfileImage)
        ? $hrProfileImage
        : $origin . $appBasePath . '/' . ltrim(str_replace('\\', '/', $hrProfileImage), '/');
}

echo json_encode(array(
    'success' => true,
    'message' => 'Candidate status updated.',
    'data' => array(
        'status_id' => $statusId,
        'status' => $status['name'],
        'last_contact' => $date,
        'latest_hr_name' => $hrName,
        'latest_hr_profile_image' => $hrProfileImage,
        'latest_hr_profile_image_url' => $hrProfileImageUrl,
        'latest_hr_comment' => $comment,
        'latest_hr_comment_date' => ($statusChanged || $hasComment) ? $date : null
    )
));

$connect->close();
