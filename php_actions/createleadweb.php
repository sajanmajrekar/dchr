<?php
// header('Access-Control-Allow-Origin: *');  

// Allow the .com site to call this endpoint
header("Access-Control-Allow-Origin: https://digichefs.com");

// Allow the methods you need
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Allow headers typically used in AJAX
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

// Optional: handle preflight OPTIONS requests automatically
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}



include('../mail/lib.php');
include("../includes/dbcon.php");
require_once 'core.php';
include("../includes/function.php");
// require ("../vendor/autoload.php");

function getServerSecret($key)
{
	if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
		return $_ENV[$key];
	}

	$value = getenv($key);
	if ($value !== false && $value !== '') {
		return $value;
	}

	return '';
}

function syncApplicantToMailerLite($email, $name, &$valid)
{
	$apiKey = getServerSecret('MAILERLITE_API_KEY');
	if ($apiKey === '') {
		return;
	}

	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://api.mailerlite.com/api/v2/groups/111515776/subscribers",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => "{\"email\":\"$email\", \"name\": \"$name\", \"fields\": {\"company\": \"\"}}",
	  CURLOPT_HTTPHEADER => array(
	    "content-type: application/json",
	    "x-mailerlite-apikey: " . $apiKey
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);

	if ($err) {
		$valid['merror'] = "MailerLite cURL Error #:" . $err;
		return;
	}

	$valid['user'] = $response;
}

function syncApplicantToBrevo($email, &$valid)
{
	$apiKey = getServerSecret('BREVO_API_KEY');
	if ($apiKey === '') {
		return;
	}

	$apiUrl = 'https://api.brevo.com/v3/contacts';
	$data = array(
	  'updateEnabled' => false,
	  'email' => $email,
	  'listIds' => array(8)
	);

	$headers = array(
	  'accept: application/json',
	  'content-type: application/json',
	  'api-key: ' . $apiKey
	);

	$ch = curl_init($apiUrl);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$response = curl_exec($ch);

	if (curl_errno($ch)) {
		$valid['merror'] = "Brevo cURL Error #:" . curl_error($ch);
		curl_close($ch);
		return;
	}

	$responseData = json_decode($response, true);
	$valid['user'] = $responseData;
	curl_close($ch);
}

$valid['success'] = array('success' => false, 'messages' => array());
if($_POST) {	
	$name = addslashes($_POST['name']);
	$email = addslashes($_POST['email']);
	$phone = addslashes($_POST['phone']);
	$source = addslashes($_POST['source']);
	$street = '';
	$country = '';
	$city = addslashes($_POST['city']);
	$pincode = '';
	$experience = addslashes($_POST['experience']);
	$qualification = '';
	$cjob = '';
	$cemployer = '';
	$expected = addslashes($_POST['expected']);
	$csalary = addslashes($_POST['csalary']);
	$skillset = addslashes($_POST['skillset']);
	$refer = addslashes($_POST['refer']);
	$info = '';
	$portfolio_link= addslashes($_POST['portfolio']);
	$selectedOption = "";
	
	if(isset($_POST['example-chosen-multiple'])){
		$selectedOption = implode(",",$_POST['example-chosen-multiple']);
	}
	
	$selected_option_users = getroletext($selectedOption);
	
	// if(isset($_POST['example-chosen-multiple'])){
	// 	$main=$_POST['example-chosen-multiple'];
	//     foreach ($main as $selectedOption) {
 //    		$selectedOption .= $selectedOption;
	// 	}
	// }
	$body='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<table width="600" border="0" cellspacing="0" cellpadding="0" style="border:solid 1px #cccccc;border-radius:10px;font-family:Arial,Helvetica,sans-serif;font-size:14px;margin:30px 20px">
  <tbody>
   <tr>
      <td width="100" style="font-weight:bold;color:#000;padding:15px 10px;"><img src="https://digichefs.com/wp-content/uploads/2019/11/2019_dc-logo_low.png" width="160px" style="width: 98px;margin: 0px auto;"/></td>
    </tr>
	<tr>
      <td width="100" colspan="3" style="font-weight:bold;color:#000;padding:15px 10px;border-top: 1px solid #ccc;">You have received an inquiry through website.</td>
    </tr>
    <tr>
      <td width="100" style="font-weight:bold;color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">Name:</td>
      <td style="font-weight:bold;color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">:</td>
      <td style="color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">' . $name. '</td>
    </tr> 
    <tr>
      <td width="100" style="font-weight:bold;color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">Mobile no.</td>
      <td style="font-weight:bold;color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">:</td>
      <td style="color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">' . $phone. '</td>
    </tr> 
    <tr>
      <td width="100" style="font-weight:bold;color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">Email</td>
      <td style="font-weight:bold;color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">:</td>
      <td style="color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">' . $email. '</td>
    </tr> 
    <tr>
      <td width="100" style="font-weight:bold;color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">Roles</td>
      <td style="font-weight:bold;color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">:</td>
      <td style="color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">' .$selected_option_users. '</td>
    </tr>  
   <tr>
      <td width="100" colspan="3" style="font-weight:bold;color:#000;padding:15px 10px;border-top: 1px solid #ccc;">Check the CRM for more details.</td>
    </tr>
   
    <tr>
      <td width="100" style="font-weight:bold;color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">Thank you!</td>
    
    </tr>  
  </tbody>
</table>
</body>
</html>';
$receivedbody="Hey $name, 

This is a confirmation email that we have received your job application to join our Kitchen here at Digichefs.<br><br>
<br><br>
Our HR team will do their best to go through each of the applications to see if you're the right fit so they might take some time to review your application. If your resume fits our menu for any of our current openings, you will be surely contacted to proceed with your application, otherwise it will be kept on our shelves for future reference. 
<br><br>
If you have any references for your candidature, please reply to this email with the same. For any other questions, please do not hesitate to contact us on <a href='mailto:careers@digichefs.com'>careers@digichefs.com</a>.
<br><br>
Meanwhile, follow us on <a href='https://www.instagram.com/digi_chefs/'>Instagram</a> or <a href='https://www.linkedin.com/company/digichefs/'>Linkedin</a> to keep up with the latest job openings & get a sneak-peek into agency life at DigiChefs.
<br><br>
You may also want to check out some digital marketing gyaan by Deep Mehta (our co-founder) on <a href='https://www.instagram.com/deepmmehta/'>his Instagram here</a>.
<br><br>
Stay tuned!
<br><br>
Regards,<br>
HR team";
	$date = date('Y-m-d H:i:s');
	$nperiod = addslashes($_POST['notice']);
	if(isset($_FILES['example-file-input']['name'])){
		$img_name = $_FILES['example-file-input']['name'];
	    $img = explode('.', $_FILES['example-file-input']['name']);
	    $type = $img[count($img)-1];
	    $img_name=uniqid(rand()).'.'.$type;
	    $url = '../resume/'.$img_name;
	}else{
		$img_name="";
	}


  	$sql1 = "SELECT * FROM `tblleads` WHERE email='".$email."'";
  	$sql = "";
	$result = $connect->query($sql1);
	if($result->num_rows == 0) { 
		if(isset($_FILES['example-file-input']['name'])){
		if(in_array($type, array('docx', 'doc', 'pdf', 'rtf', 'DOCX', 'DOC', 'PDF', 'RTF'))) {
		    if(is_uploaded_file($_FILES['example-file-input']['tmp_name'])) {
		         if(move_uploaded_file($_FILES['example-file-input']['tmp_name'], $url)) {
					$sql ="INSERT INTO `tblleads`(`name`, `country`, `zip`, `city`, `street`,`dateadded`, `status`, `source`,  `email`, `phonenumber`, `experiance`, `qualification`, `cjtitle`, `cemployer`, `esalary`, `csalary`, `skillset`, `ainfo`, `roles`, `nperiod`, `resume`,`referral`,`portfolio`) VALUES ('$name','$country','$pincode','$city','$street','$date','20','$source','$email','$phone','$experience','$qualification','$cjob','$cemployer','$expected','$csalary','$skillset','$info','$selectedOption','$nperiod','$img_name','$refer','$portfolio_link')";
						if($connect->query($sql) === TRUE && SendMailHTML("careers@digichefs.com",'Digichefs || Job Enquiry received',$body,'',$url)) {
							$valid['success'] = true;
							$valid['messages'] = "Thank you! We have received your application at DigiChefs, We shall get back to you soon.";
							
							SendMailHTML("$email",'DigiChefs || Your Job Application is Received',$receivedbody,'','');
							syncApplicantToMailerLite($email, $name, $valid);
							syncApplicantToBrevo($email, $valid);
						} else {
							$valid['success'] = false;
							$valid['messages'] = "Error while adding the Candidate" . $connect->error;
						}
					}
				}
			}
		}else{
			$sql ="INSERT INTO `tblleads`(`name`, `country`, `zip`, `city`, `street`,`dateadded`, `status`, `source`,  `email`, `phonenumber`, `experiance`, `qualification`, `cjtitle`, `cemployer`, `esalary`, `csalary`, `skillset`, `ainfo`, `roles`, `nperiod`, `resume`,`referral`,`portfolio`) VALUES ('$name','$country','$pincode','$city','$street','$date','20','$source','$email','$phone','$experience','$qualification','$cjob','$cemployer','$expected','$csalary','$skillset','$info','$selectedOption','$nperiod','$img_name','$refer','$portfolio_link')";
						if($connect->query($sql) === TRUE && SendMailHTML('careers@digichefs.com','Digichefs || Job Enquiry received',$body,'','')) {
						    
						    $valid['success'] = true;
							$valid['messages'] = "Thank you! We have received your application at DigiChefs, We shall get back to you soon.";	
							SendMailHTML("$email",'Digichefs || Application received',$receivedbody,'','');
						    syncApplicantToMailerLite($email, $name, $valid);
						    syncApplicantToBrevo($email, $valid);
						} else {
							$valid['success'] = false;
							$valid['messages'] = "Error while adding the Candidate" . $connect->error;
						}
		}
	}
		else{
				$valid['success'] = false;
				$valid['messages'] = "Your application is already with us. We will get back to you incase of any opening.";
	}
	$connect->close();
}// /if $_POST
echo json_encode($valid);





