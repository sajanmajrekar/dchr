<?php   

require_once 'core.php';
require_once '../includes/dbcon.php';
require_once '../includes/function.php';

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {    
    $password = md5($_POST['login-password']);
    $query = query("Select * from password_resets where token='".$_SESSION['token']."'");
    $row = $query->fetch_object(); 
    $sql = "UPDATE tblstaff SET password = '$password' WHERE email = '".$row->Email."'";
    if($connect->query($sql) === TRUE) {
        $sql1 = "delete from `password_resets` WHERE token='".$_SESSION['token']."'";
        if($connect->query($sql1) === TRUE) {
        $valid['success'] = true;
        $valid['messages'] = "Password Updated Successfully.";   
        } 
    } else {
        $valid['success'] = false;
        $valid['messages'] = "Error while adding the members" . $connect->error;
    }
     
    $connect->close();

    echo json_encode($valid);
 
} // /if $_POST