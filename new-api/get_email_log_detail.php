<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../php_actions/db_connect.php';
require_once '../php_actions/core.php';
require_once 'email_helpers.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    echo json_encode(array('success' => false, 'message' => 'Invalid email log id.'));
    exit;
}

$stmt = $connect->prepare("SELECT e.*, TRIM(CONCAT(COALESCE(s.firstname, ''), ' ', COALESCE(s.lastname, ''))) AS sent_by_name
    FROM emaillogs e
    LEFT JOIN tblstaff s ON s.staffid = e.sentby
    WHERE e.id = ?
    LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$log = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$log) {
    echo json_encode(array('success' => false, 'message' => 'Email log not found.'));
    exit;
}

$candidateIds = array_values(array_filter(array_map('intval', explode(',', (string) $log['mailsentto']))));
$candidates = array();
if (!empty($candidateIds)) {
    $candidateResult = $connect->query("SELECT tblleads.id, tblleads.name, tblleads.email, tblleads.phonenumber, tblleads.roles, tblleads.experiance, tblleads.nperiod, tblleads.dateadded, tblleads.lastcontact, tblleads.city, tblleads.csalary, tblleads.esalary, tblleadsstatus.name AS status_name, tblleadssources.name AS source_name
        FROM tblleads
        INNER JOIN tblleadssources ON tblleads.source = tblleadssources.id
        INNER JOIN tblleadsstatus ON tblleads.status = tblleadsstatus.id
        WHERE tblleads.id IN (" . implode(',', $candidateIds) . ")
        ORDER BY tblleads.dateadded DESC");
    if ($candidateResult) {
        while ($row = $candidateResult->fetch_assoc()) {
            $candidates[] = emailApiMapCandidate($row);
        }
        $candidateResult->free();
    }
}

echo json_encode(array(
    'success' => true,
    'data' => array(
        'id' => (int) $log['id'],
        'subject' => $log['subject'],
        'mailcontent' => $log['mailcontent'],
        'sent_time' => $log['senttime'],
        'sent_by' => $log['sent_by_name'],
        'total_sent' => (int) $log['totalemailsent'],
        'candidates' => $candidates
    )
), JSON_UNESCAPED_SLASHES);

$connect->close();
