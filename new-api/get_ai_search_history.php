<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, X-HR-SESSION, Authorization');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once '../php_actions/db_connect.php';
require_once 'auth_helper.php';

function aiSearchHistoryJsonDecode($value, $fallback)
{
    $decoded = json_decode((string) $value, true);
    return is_array($decoded) ? $decoded : $fallback;
}

$staffId = apiRequireStaffId($connect);
$history = array();

$tableCheck = $connect->query("SHOW TABLES LIKE 'hr_ai_search_history'");
if (!$tableCheck || $tableCheck->num_rows < 1) {
    echo json_encode(array('success' => true, 'data' => array()));
    if ($tableCheck) {
        $tableCheck->free();
    }
    $connect->close();
    exit;
}
if ($tableCheck) {
    $tableCheck->free();
}

$stmt = $connect->prepare("SELECT * FROM hr_ai_search_history WHERE staff_id = ? ORDER BY updated_at DESC, id DESC LIMIT 5");
if ($stmt) {
    $stmt->bind_param('i', $staffId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $history[] = array(
                'id' => (int) $row['id'],
                'staff_id' => (int) $row['staff_id'],
                'title' => $row['title'],
                'filters' => aiSearchHistoryJsonDecode($row['filters_json'], array()),
                'filters_used' => aiSearchHistoryJsonDecode($row['filters_used_json'], array()),
                'summary' => (string) $row['summary'],
                'cards' => aiSearchHistoryJsonDecode($row['result_cards_json'], array()),
                'processed_count' => (int) $row['processed_count'],
                'total_limit' => (int) $row['total_limit'],
                'total_available' => (int) $row['total_available'],
                'usage' => array(
                    'input_tokens' => (int) $row['input_tokens'],
                    'output_tokens' => (int) $row['output_tokens'],
                    'total_tokens' => (int) $row['total_tokens']
                ),
                'status' => $row['status'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
            );
        }
    }
    $stmt->close();
}

echo json_encode(array(
    'success' => true,
    'data' => $history
), JSON_UNESCAPED_SLASHES);

$connect->close();
