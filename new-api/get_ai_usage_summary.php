<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/php_actions/db_connect.php';

if (!($connect instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(array('success' => false, 'message' => 'Database connection is not available.'));
    exit;
}

$tableExists = $connect->query("SHOW TABLES LIKE 'hr_ai_usage_logs'");
if (!$tableExists || $tableExists->num_rows === 0) {
    echo json_encode(array(
        'success' => true,
        'data' => array(
            'total' => array('input_tokens' => 0, 'output_tokens' => 0, 'total_tokens' => 0, 'input_cost' => 0, 'output_cost' => 0, 'total_cost' => 0, 'currency' => 'USD', 'runs' => 0),
            'today' => array('input_tokens' => 0, 'output_tokens' => 0, 'total_tokens' => 0, 'input_cost' => 0, 'output_cost' => 0, 'total_cost' => 0, 'currency' => 'USD', 'runs' => 0),
            'current_user' => array('input_tokens' => 0, 'output_tokens' => 0, 'total_tokens' => 0, 'input_cost' => 0, 'output_cost' => 0, 'total_cost' => 0, 'currency' => 'USD', 'runs' => 0),
            'users' => array()
        )
    ), JSON_UNESCAPED_SLASHES);
    if ($tableExists) {
        $tableExists->free();
    }
    $connect->close();
    exit;
}
$tableExists->free();

$staffId = isset($_GET['staff_id']) ? (int) $_GET['staff_id'] : 0;

function usageColumnExists($connect, $column)
{
    $result = $connect->query("SHOW COLUMNS FROM hr_ai_usage_logs LIKE '" . $connect->real_escape_string((string) $column) . "'");
    if (!$result) {
        return false;
    }

    $exists = $result->num_rows > 0;
    $result->free();
    return $exists;
}

$hasCostColumns = usageColumnExists($connect, 'total_cost') && usageColumnExists($connect, 'currency');

function usageSummaryRow($connect, $whereSql)
{
    $hasCostColumns = usageColumnExists($connect, 'total_cost') && usageColumnExists($connect, 'currency');
    $costSql = $hasCostColumns
        ? ", COALESCE(SUM(input_cost), 0) AS input_cost, COALESCE(SUM(output_cost), 0) AS output_cost, COALESCE(SUM(total_cost), 0) AS total_cost, COALESCE(MAX(currency), 'USD') AS currency"
        : ", 0 AS input_cost, 0 AS output_cost, 0 AS total_cost, 'USD' AS currency";

    $sql = "SELECT
            COALESCE(SUM(input_tokens), 0) AS input_tokens,
            COALESCE(SUM(output_tokens), 0) AS output_tokens,
            COALESCE(SUM(total_tokens), 0) AS total_tokens,
            COUNT(*) AS runs" . $costSql . "
        FROM hr_ai_usage_logs
        WHERE " . $whereSql;
    $result = $connect->query($sql);
    if (!$result) {
        return array('input_tokens' => 0, 'output_tokens' => 0, 'total_tokens' => 0, 'runs' => 0);
    }

    $row = $result->fetch_assoc();
    $result->free();

    return array(
        'input_tokens' => (int) $row['input_tokens'],
        'output_tokens' => (int) $row['output_tokens'],
        'total_tokens' => (int) $row['total_tokens'],
        'runs' => (int) $row['runs'],
        'input_cost' => (float) $row['input_cost'],
        'output_cost' => (float) $row['output_cost'],
        'total_cost' => (float) $row['total_cost'],
        'currency' => (string) $row['currency']
    );
}

$users = array();
$userCostSql = $hasCostColumns
    ? ", COALESCE(SUM(u.input_cost), 0) AS input_cost, COALESCE(SUM(u.output_cost), 0) AS output_cost, COALESCE(SUM(u.total_cost), 0) AS total_cost, COALESCE(MAX(u.currency), 'USD') AS currency"
    : ", 0 AS input_cost, 0 AS output_cost, 0 AS total_cost, 'USD' AS currency";
$userResult = $connect->query("SELECT
        u.staff_id,
        TRIM(CONCAT(COALESCE(s.firstname, ''), ' ', COALESCE(s.lastname, ''))) AS staff_name,
        COALESCE(SUM(u.input_tokens), 0) AS input_tokens,
        COALESCE(SUM(u.output_tokens), 0) AS output_tokens,
        COALESCE(SUM(u.total_tokens), 0) AS total_tokens,
        COUNT(*) AS runs,
        MAX(u.created_at) AS last_used_at" . $userCostSql . "
    FROM hr_ai_usage_logs u
    LEFT JOIN tblstaff s ON s.staffid = u.staff_id
    GROUP BY u.staff_id
    ORDER BY total_tokens DESC
    LIMIT 20");

if ($userResult) {
    while ($row = $userResult->fetch_assoc()) {
        $users[] = array(
            'staff_id' => (int) $row['staff_id'],
            'staff_name' => trim((string) $row['staff_name']) !== '' ? trim((string) $row['staff_name']) : 'Unknown user',
            'input_tokens' => (int) $row['input_tokens'],
            'output_tokens' => (int) $row['output_tokens'],
            'total_tokens' => (int) $row['total_tokens'],
            'input_cost' => (float) $row['input_cost'],
            'output_cost' => (float) $row['output_cost'],
            'total_cost' => (float) $row['total_cost'],
            'currency' => (string) $row['currency'],
            'runs' => (int) $row['runs'],
            'last_used_at' => $row['last_used_at']
        );
    }
    $userResult->free();
}

echo json_encode(array(
    'success' => true,
    'data' => array(
        'total' => usageSummaryRow($connect, '1=1'),
        'today' => usageSummaryRow($connect, "DATE(created_at) = CURDATE()"),
        'current_user' => $staffId > 0 ? usageSummaryRow($connect, 'staff_id = ' . $staffId) : array('input_tokens' => 0, 'output_tokens' => 0, 'total_tokens' => 0, 'input_cost' => 0, 'output_cost' => 0, 'total_cost' => 0, 'currency' => 'USD', 'runs' => 0),
        'users' => $users
    )
), JSON_UNESCAPED_SLASHES);

$connect->close();
