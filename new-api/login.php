<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, X-HR-SESSION, Authorization');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once '../php_actions/db_connect.php';
require_once 'auth_helper.php';

if (!($connect instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(array('success' => false, 'message' => 'Database connection is not available.'));
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    $payload = $_POST;
}

$email = isset($payload['email']) ? trim((string) $payload['email']) : '';
$password = isset($payload['password']) ? (string) $payload['password'] : '';

if ($email === '' || $password === '') {
    http_response_code(422);
    echo json_encode(array('success' => false, 'message' => 'Email and password are required.'));
    exit;
}

$stmt = $connect->prepare("SELECT staffid, firstname, lastname, email, phonenumber, profile_image, admin, active
    FROM tblstaff
    WHERE email = ? AND password = ? AND active = 1
    LIMIT 1");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(array('success' => false, 'message' => 'Could not prepare login request.'));
    exit;
}

$stmt->bind_param('ss', $email, $password);
$stmt->execute();
$result = $stmt->get_result();
$user = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$user) {
    http_response_code(401);
    echo json_encode(array('success' => false, 'message' => 'Invalid email or password.'));
    exit;
}

$role = $user['admin'] == 2 ? 'Superadmin' : ($user['admin'] == 1 ? 'Admin' : 'User');
$sessionToken = apiIssueReactSession($connect, (int) $user['staffid']);

echo json_encode(array(
    'success' => true,
    'data' => array(
        'id' => (int) $user['staffid'],
        'name' => trim((string) $user['firstname'] . ' ' . (string) $user['lastname']),
        'email' => $user['email'],
        'phone' => $user['phonenumber'],
        'profile_image' => $user['profile_image'],
        'role' => $role,
        'session_token' => $sessionToken
    )
), JSON_UNESCAPED_SLASHES);

$connect->close();
