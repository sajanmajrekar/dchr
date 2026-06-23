

<?php 
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);



	require "../includes/dbcon.php";
	require "../includes/function.php";
	
	
	
	
	$username = $_POST['login-email'];
// 	$pass = md5($_POST['login-password']);
	$pass =     $_POST['login-password'];
	$sql = $sql = $conn->query("SELECT * FROM tblstaff WHERE email='$username' AND password='$pass' AND active=1");
$test="SELECT * FROM tblstaff WHERE email='$username' AND password='$pass' AND active=1";

// echo $test; 
// die();
	$rowcount = mysqli_num_rows($sql);
	if($rowcount!=0){

		$r = $sql->fetch_object();
		$_SESSION['id']=$r->staffid;
		$_SESSION['user'] = $_POST['login-email'];
		if($r->admin==1){
			$_SESSION['accounttype'] = 'admin';
		}else if($r->admin==2){
			$_SESSION['accounttype'] = 'superadmin';
		}
		else{
			$_SESSION['accounttype'] = 'user';
		}
		echo '<script>window.location.href="../index.php"</script>';        
	}
	else{
		echo '<script>alert("Username or Password Invalid");window.location.href="../login.php";</script>';
	}


?>