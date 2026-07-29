<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../php_actions/db_connect.php';

$statuses = array();
$statusResult = $connect->query("SELECT id, name FROM tblleadsstatus ORDER BY name ASC");
if ($statusResult) {
    while ($row = $statusResult->fetch_assoc()) {
        $statuses[] = array(
            'id' => (int) $row['id'],
            'name' => $row['name']
        );
    }
    $statusResult->free();
}

echo json_encode(array(
    'success' => true,
    'data' => array(
        'statuses' => $statuses
    )
));

$connect->close();
