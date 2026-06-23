<?php 	

require_once 'core.php';
include('../mail/lib.php');

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {
$leadid=$_POST['checkbox'];
$subject=addslashes($_POST['subject']);
$body=addslashes($_POST['mailcontents']);
$mailaddress = []; 
foreach ($leadid as $key => $value) {
	array_push($mailaddress, getemail($value));
}
$bcc = implode(",",$mailaddress);
if(SendMailHTML('nitesh.w@digichefs.com',$subject,$body,$bcc,''))
  {
		$valid['success'] = true;
		$valid['messages'] = "Email sent successfully.";	
  }
else{
		$valid['success'] = false;
		$valid['messages'] = "Something went wrong.";	
}
	$connect->close();
 // /if $_POST
}
echo json_encode($valid);

?>