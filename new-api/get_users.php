<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // For Vite dev server CORS

require_once '../php_actions/db_connect.php';

$sql = "SELECT staffid, firstname, lastname, email, phonenumber, profile_image, active, admin FROM `tblstaff` ORDER BY staffid ASC";
$result = $connect->query($sql);
$users = [];

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
$origin = ($isHttps ? 'https://' : 'http://') . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
$scriptDir = str_replace('\\', '/', dirname(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/hr/new-api/get_users.php'));
$appBasePath = rtrim(dirname($scriptDir), '/');
if ($appBasePath === '' || $appBasePath === '.') {
    $appBasePath = '';
}

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $profileImage = trim((string) $row['profile_image']);
        $profileImageUrl = '';
        if ($profileImage !== '') {
            if (preg_match('/^https?:\/\//i', $profileImage)) {
                $profileImageUrl = $profileImage;
            } else {
                $profileImageUrl = $origin . $appBasePath . '/' . ltrim(str_replace('\\', '/', $profileImage), '/');
            }
        }

        $users[] = [
            'id' => $row['staffid'],
            'name' => $row['firstname'] . ' ' . $row['lastname'],
            'email' => $row['email'],
            'phone' => $row['phonenumber'],
            'profile_image' => $profileImage,
            'profile_image_url' => $profileImageUrl,
            'active' => $row['active'] == 1,
            'role' => $row['admin'] == 2 ? 'Superadmin' : ($row['admin'] == 1 ? 'Admin' : 'User')
        ];
    }
}

echo json_encode(['success' => true, 'data' => $users]);
$connect->close();
