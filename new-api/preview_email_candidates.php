<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once '../php_actions/db_connect.php';
require_once '../php_actions/core.php';
require_once 'email_helpers.php';

$filters = emailApiReadFilters($_POST);
$sql = emailApiCandidateSql($connect, $filters, 1000);
$result = $connect->query($sql);
$rows = array();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = emailApiMapCandidate($row);
    }
    $result->free();
}

echo json_encode(array(
    'success' => true,
    'data' => array(
        'count' => count($rows),
        'candidates' => array_slice($rows, 0, 100)
    )
), JSON_UNESCAPED_SLASHES);

$connect->close();
