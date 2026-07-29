<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once '../php_actions/db_connect.php';

if (!($connect instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(array('success' => false, 'message' => 'Database connection is not available.'));
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    $payload = $_POST;
}

$staffId = isset($payload['id']) ? (int) $payload['id'] : 0;
$firstname = isset($payload['firstname']) ? trim((string) $payload['firstname']) : '';
$lastname = isset($payload['lastname']) ? trim((string) $payload['lastname']) : '';
$email = isset($payload['email']) ? trim((string) $payload['email']) : '';
$phone = isset($payload['phone']) ? trim((string) $payload['phone']) : '';
$profileImage = isset($payload['profile_image']) ? trim((string) $payload['profile_image']) : '';

if ($staffId <= 0 || $firstname === '' || $email === '') {
    http_response_code(422);
    echo json_encode(array('success' => false, 'message' => 'Name and email are required.'));
    exit;
}

$stmt = $connect->prepare("UPDATE tblstaff
    SET firstname = ?, lastname = ?, email = ?, phonenumber = ?, profile_image = ?
    WHERE staffid = ?
    LIMIT 1");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(array('success' => false, 'message' => 'Could not prepare profile update.'));
    exit;
}

$stmt->bind_param('sssssi', $firstname, $lastname, $email, $phone, $profileImage, $staffId);
$ok = $stmt->execute();
$stmt->close();

if (!$ok) {
    http_response_code(500);
    echo json_encode(array('success' => false, 'message' => 'Could not update profile.'));
    exit;
}

echo json_encode(array(
    'success' => true,
    'message' => 'Profile updated.',
    'data' => array(
        'id' => $staffId,
        'name' => trim($firstname . ' ' . $lastname),
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'phone' => $phone,
        'profile_image' => $profileImage
    )
), JSON_UNESCAPED_SLASHES);

$connect->close();
