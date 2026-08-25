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

function profileUploadResponse($success, $message, $data = array(), $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode(array(
        'success' => $success,
        'message' => $message,
        'data' => $data
    ), JSON_UNESCAPED_SLASHES);
    exit;
}

$staffId = apiRequireStaffId($connect);

if (empty($_FILES['profile_image']) || !isset($_FILES['profile_image']['tmp_name'])) {
    profileUploadResponse(false, 'Please choose a profile image.', array(), 422);
}

$file = $_FILES['profile_image'];
if (!empty($file['error'])) {
    profileUploadResponse(false, 'Could not upload this image. Please try again.', array(), 422);
}

if ((int) $file['size'] > 3 * 1024 * 1024) {
    profileUploadResponse(false, 'Profile image must be 3 MB or smaller.', array(), 422);
}

$imageInfo = @getimagesize($file['tmp_name']);
if ($imageInfo === false || empty($imageInfo['mime'])) {
    profileUploadResponse(false, 'Please upload a valid image file.', array(), 422);
}

$allowedTypes = array(
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp'
);

if (!isset($allowedTypes[$imageInfo['mime']])) {
    profileUploadResponse(false, 'Only JPG, PNG and WEBP images are allowed.', array(), 422);
}

$uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'profile';
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    profileUploadResponse(false, 'Could not create profile upload folder.', array(), 500);
}

$randomSuffix = function_exists('random_bytes') ? bin2hex(random_bytes(4)) : substr(sha1(uniqid('', true)), 0, 8);
$fileName = 'profile_' . $staffId . '_' . time() . '_' . $randomSuffix . '.' . $allowedTypes[$imageInfo['mime']];
$targetPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
$relativePath = 'profile/' . $fileName;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    profileUploadResponse(false, 'Could not save profile image.', array(), 500);
}

$stmt = $connect->prepare("UPDATE tblstaff SET profile_image = ? WHERE staffid = ? LIMIT 1");
if (!$stmt) {
    @unlink($targetPath);
    profileUploadResponse(false, 'Could not prepare profile image update.', array(), 500);
}

$stmt->bind_param('si', $relativePath, $staffId);
$ok = $stmt->execute();
$stmt->close();

if (!$ok) {
    @unlink($targetPath);
    profileUploadResponse(false, 'Could not update profile image.', array(), 500);
}

$connect->close();

profileUploadResponse(true, 'Profile image updated.', array(
    'profile_image' => $relativePath
));
?>
