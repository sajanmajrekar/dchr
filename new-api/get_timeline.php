<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../php_actions/db_connect.php';

if (!isset($_GET['id'])) {
    echo json_encode(array('success' => false, 'messages' => 'Missing Candidate ID'));
    exit();
}

$id = (int) $_GET['id'];
$stmt = $connect->prepare("SELECT
        al.leadid,
        al.description,
        al.date,
        al.staffid,
        al.additional_data,
        ls.name AS status_name,
        st.firstname,
        st.lastname
    FROM tblleadactivitylog al
    LEFT JOIN tblleadsstatus ls ON CAST(al.description AS UNSIGNED) = ls.id
    LEFT JOIN tblstaff st ON al.staffid = st.staffid
    WHERE al.leadid = ?
    ORDER BY al.date DESC");

if (!$stmt) {
    echo json_encode(array('success' => false, 'messages' => 'Could not prepare timeline query.'));
    exit();
}

$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$timeline = array();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $additionalData = trim((string) $row['additional_data']);
        $actorName = trim((string) $row['firstname'] . ' ' . (string) $row['lastname']);
        $isViewLog = strpos($additionalData, 'VIEW_LOG::') === 0;

        $timeline[] = array(
            'date' => $row['date'],
            'description' => $row['description'],
            'status_name' => $row['status_name'],
            'actor_name' => $actorName,
            'additional_data' => $additionalData,
            'is_view_log' => $isViewLog
        );
    }
}

$stmt->close();

echo json_encode(array('success' => true, 'data' => $timeline), JSON_UNESCAPED_SLASHES);
$connect->close();
