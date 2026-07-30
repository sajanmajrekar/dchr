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
    echo json_encode(array(
        'success' => false,
        'message' => 'Database connection is not available.'
    ));
    exit;
}

function usersApiColumnExists($connect, $table, $column)
{
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $table);
    $column = $connect->real_escape_string((string) $column);
    $result = $connect->query("SHOW COLUMNS FROM `" . $table . "` LIKE '" . $column . "'");
    if (!$result) {
        return false;
    }

    $exists = $result->num_rows > 0;
    $result->free();
    return $exists;
}

function usersApiAppBaseUrl()
{
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $origin = ($isHttps ? 'https://' : 'http://') . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
    $scriptDir = str_replace('\\', '/', dirname(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/hr/new-api/get_users.php'));
    $appBasePath = rtrim(dirname($scriptDir), '/');
    if ($appBasePath === '' || $appBasePath === '.') {
        $appBasePath = '';
    }

    return $origin . $appBasePath;
}

$hasProfileImage = usersApiColumnExists($connect, 'tblstaff', 'profile_image');
$profileSelect = $hasProfileImage ? 'profile_image' : "'' AS profile_image";
$sql = "SELECT staffid, firstname, lastname, email, phonenumber, " . $profileSelect . ", active, admin
    FROM tblstaff
    ORDER BY staffid ASC";

$result = $connect->query($sql);
if (!$result) {
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'message' => 'Could not load users: ' . $connect->error
    ));
    $connect->close();
    exit;
}

$users = array();
$appBaseUrl = usersApiAppBaseUrl();

while ($row = $result->fetch_assoc()) {
    $profileImage = trim((string) $row['profile_image']);
    $profileImageUrl = '';
    if ($profileImage !== '') {
        if (preg_match('/^https?:\/\//i', $profileImage)) {
            $profileImageUrl = $profileImage;
        } else {
            $profileImageUrl = $appBaseUrl . '/' . ltrim(str_replace('\\', '/', $profileImage), '/');
        }
    }

    $users[] = array(
        'id' => (int) $row['staffid'],
        'name' => trim((string) $row['firstname'] . ' ' . (string) $row['lastname']),
        'firstname' => (string) $row['firstname'],
        'lastname' => (string) $row['lastname'],
        'email' => (string) $row['email'],
        'phone' => (string) $row['phonenumber'],
        'profile_image' => $profileImage,
        'profile_image_url' => $profileImageUrl,
        'active' => (int) $row['active'] === 1,
        'role' => (int) $row['admin'] === 2 ? 'Superadmin' : ((int) $row['admin'] === 1 ? 'Admin' : 'User')
    );
}

$result->free();

echo json_encode(array(
    'success' => true,
    'data' => $users
), JSON_UNESCAPED_SLASHES);

$connect->close();
