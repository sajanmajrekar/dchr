<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once '../php_actions/db_connect.php';

$valid = ['success' => false, 'messages' => ''];

// Parse JSON body
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data) {
    // Basic session validation logic from original could go here if cookies are active
    $fname = $connect->real_escape_string($data['firstname']);
    $lname = $connect->real_escape_string($data['lastname']);
    $password = md5($data['password']); // Using md5 to match legacy system
    $email = $connect->real_escape_string($data['email']);
    $phone = $connect->real_escape_string($data['phone']);
    $rights = (int) $data['rights']; // 0 = User, 1 = Admin, 2 = Superadmin
    $status = (int) $data['status']; // 1 = Active, 0 = Inactive
    $date = date('Y-m-d H:i:s');

    $sql1 = "SELECT * FROM `tblstaff` WHERE email='" . $email . "'";
    $result = $connect->query($sql1);

    if ($result && $result->num_rows == 0) {
        $sql = "INSERT INTO tblstaff (firstname, lastname, phonenumber, password, email, admin, active, datecreated) 
                VALUES ('$fname', '$lname', '$phone', '$password', '$email', $rights, $status, '$date')";

        if ($connect->query($sql) === TRUE) {
            $valid['success'] = true;
            $valid['messages'] = "New User created successfully!";
        } else {
            $valid['success'] = false;
            $valid['messages'] = "Error while adding the user: " . $connect->error;
        }
    } else {
        $valid['success'] = false;
        $valid['messages'] = "Email address already exists!";
    }
} else {
    $valid['messages'] = "Invalid payload.";
}

echo json_encode($valid);
$connect->close();
