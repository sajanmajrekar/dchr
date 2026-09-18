<?php
ob_start();
ini_set('display_errors', '0');
// header('Access-Control-Allow-Origin: *');  

// Allow the .com site to call this endpoint
header("Access-Control-Allow-Origin: https://digichefs.com");

// Allow the methods you need
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Allow headers typically used in AJAX
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header("Content-Type: application/json; charset=utf-8");

if (function_exists('mysqli_report')) {
	mysqli_report(MYSQLI_REPORT_OFF);
}

register_shutdown_function(function () {
	$error = error_get_last();
	if (!$error || !in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true)) {
		return;
	}

	while (ob_get_level() > 0) {
		ob_end_clean();
	}

	if (!headers_sent()) {
		http_response_code(500);
		header("Content-Type: application/json; charset=utf-8");
	}

	echo json_encode(array(
		'success' => false,
		'messages' => 'Something went wrong while submitting the form. Please try again.'
	), JSON_UNESCAPED_SLASHES);
});

// Optional: handle preflight OPTIONS requests automatically
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}



include('../mail/lib.php');
include("../includes/dbcon.php");
require_once 'core.php';
include("../includes/function.php");
require_once __DIR__ . '/../includes/resume_intelligence.php';
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

function normalizeRelocationValue($value)
{
	$value = trim((string) $value);

	if ($value === '13' || strcasecmp($value, 'yes') === 0) {
		return 'Yes';
	}

	if ($value === '11' || strcasecmp($value, 'no') === 0) {
		return 'No';
	}

	return $value;
}

function leadColumnExists($connect, $columnName)
{
	$result = false;
	try {
		$result = $connect->query("SHOW COLUMNS FROM tblleads LIKE '" . $connect->real_escape_string($columnName) . "'");
	} catch (Throwable $e) {
		return false;
	} catch (Exception $e) {
		return false;
	}

	$exists = $result && $result->num_rows > 0;
	if ($result) {
		$result->free();
	}

	return $exists;
}

function storeApplicantResume($fieldName)
{
	if (!isset($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
		return array('success' => false, 'message' => 'Please attach your resume before submitting.');
	}

	$file = $_FILES[$fieldName];
	$error = isset($file['error']) ? (int) $file['error'] : UPLOAD_ERR_NO_FILE;
	if ($error !== UPLOAD_ERR_OK) {
		$messages = array(
			UPLOAD_ERR_INI_SIZE => 'The resume is larger than the server upload limit.',
			UPLOAD_ERR_FORM_SIZE => 'The resume is too large.',
			UPLOAD_ERR_PARTIAL => 'The resume upload was interrupted. Please try again.',
			UPLOAD_ERR_NO_FILE => 'Please attach your resume before submitting.',
			UPLOAD_ERR_NO_TMP_DIR => 'The server upload folder is unavailable.',
			UPLOAD_ERR_CANT_WRITE => 'The server could not save the resume.',
			UPLOAD_ERR_EXTENSION => 'The resume upload was blocked by the server.'
		);

		return array(
			'success' => false,
			'message' => isset($messages[$error]) ? $messages[$error] : 'The resume could not be uploaded.'
		);
	}

	$tmpName = isset($file['tmp_name']) ? (string) $file['tmp_name'] : '';
	$originalName = isset($file['name']) ? (string) $file['name'] : '';
	$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
	if (!in_array($extension, array('pdf', 'doc', 'docx', 'rtf'), true)) {
		return array('success' => false, 'message' => 'Please upload a PDF, DOC, DOCX or RTF resume.');
	}

	if (!is_uploaded_file($tmpName)) {
		return array('success' => false, 'message' => 'The resume upload could not be verified. Please try again.');
	}

	$uploadDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resume';
	if (!is_dir($uploadDirectory) && !@mkdir($uploadDirectory, 0775, true) && !is_dir($uploadDirectory)) {
		return array('success' => false, 'message' => 'The resume storage folder is unavailable.');
	}

	try {
		$randomPart = bin2hex(random_bytes(8));
	} catch (Throwable $e) {
		$randomPart = str_replace('.', '', uniqid('', true));
	} catch (Exception $e) {
		$randomPart = str_replace('.', '', uniqid('', true));
	}

	$fileName = 'resume_' . date('YmdHis') . '_' . $randomPart . '.' . $extension;
	$absolutePath = $uploadDirectory . DIRECTORY_SEPARATOR . $fileName;
	if (!move_uploaded_file($tmpName, $absolutePath) || !is_file($absolutePath) || (int) @filesize($absolutePath) < 1) {
		return array('success' => false, 'message' => 'The server could not store the resume. Please try again.');
	}

	return array(
		'success' => true,
		'file_name' => $fileName,
		'absolute_path' => $absolutePath
	);
}

function syncApplicantToMailerLite($email, $name, &$valid)
{
	$apiKey = getServerSecret('MAILERLITE_API_KEY');
	if ($apiKey === '' || !function_exists('curl_init')) {
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
	if ($apiKey === '' || !function_exists('curl_init')) {
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

$valid = array('success' => false, 'messages' => 'Something went wrong while submitting the form. Please try again.');
if($_POST) {	
	$name = addslashes(isset($_POST['name']) ? $_POST['name'] : '');
	$email = addslashes(isset($_POST['email']) ? $_POST['email'] : '');
	$phone = addslashes(isset($_POST['phone']) ? $_POST['phone'] : '');
	$source = addslashes(isset($_POST['source']) ? $_POST['source'] : '');
	$willing_to_relocate = isset($_POST['willing_to_relocate']) ? addslashes(normalizeRelocationValue($_POST['willing_to_relocate'])) : '';
	$street = '';
	$country = '';
	$city = addslashes(isset($_POST['city']) ? $_POST['city'] : '');
	$pincode = '';
	$experience = addslashes(isset($_POST['experience']) ? $_POST['experience'] : '');
	$qualification = '';
	$cjob = '';
	$cemployer = '';
	$expected = addslashes(isset($_POST['expected']) ? $_POST['expected'] : '');
	$csalary = addslashes(isset($_POST['csalary']) ? $_POST['csalary'] : '');
	$skillset = addslashes(isset($_POST['skillset']) ? $_POST['skillset'] : '');
	$refer = addslashes(isset($_POST['refer']) ? $_POST['refer'] : '');
	$info = '';
	$portfolio_link = isset($_POST['portfolio']) ? addslashes($_POST['portfolio']) : '';
	$joining_date = isset($_POST['joining_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['joining_date']) ? addslashes($_POST['joining_date']) : '';
	$hasJoiningDateColumn = leadColumnExists($connect, 'joining_date');
	$joiningDateInsertColumn = $hasJoiningDateColumn ? ",`joining_date`" : "";
	$joiningDateInsertValue = $hasJoiningDateColumn ? ",'$joining_date'" : "";
	$joiningDateUpdate = $hasJoiningDateColumn ? ", joining_date='$joining_date'" : "";
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
      <td width="100" style="font-weight:bold;color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">Willing to Relocate</td>
      <td style="font-weight:bold;color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">:</td>
      <td style="color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">' . $willing_to_relocate . '</td>
    </tr>
    <tr>
      <td width="100" style="font-weight:bold;color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">Tentative Joining Date</td>
      <td style="font-weight:bold;color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">:</td>
      <td style="color:#000;padding:15px 10px;border-bottom:dotted 1px #cccccc">' . ($joining_date !== '' ? $joining_date : 'Not provided') . '</td>
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
	$nperiod = addslashes(isset($_POST['notice']) ? $_POST['notice'] : '');
	$resumeFieldName = isset($_FILES['example-file-input']) ? 'example-file-input' : 'resume';
	$resumeUpload = storeApplicantResume($resumeFieldName);
	if (!$resumeUpload['success']) {
		$valid['messages'] = $resumeUpload['message'];
		$connect->close();
		echo json_encode($valid);
		exit;
	}

	$img_name = $connect->real_escape_string($resumeUpload['file_name']);
	$url = $resumeUpload['absolute_path'];


  	$sql1 = "SELECT * FROM `tblleads` WHERE email='".$email."'";
  	$sql = "";
	$result = $connect->query($sql1);
	if (!$result) {
		@unlink($url);
		$valid['success'] = false;
		$valid['messages'] = "Something went wrong while submitting the form. Please try again.";
	} else if($result->num_rows == 0) {
		$sql ="INSERT INTO `tblleads`(`name`, `country`, `zip`, `city`, `street`,`dateadded`, `status`, `source`, `willing_to_relocate`, `email`, `phonenumber`, `experiance`, `qualification`, `cjtitle`, `cemployer`, `esalary`, `csalary`, `skillset`, `ainfo`, `roles`, `nperiod`, `resume`,`referral`,`portfolio`$joiningDateInsertColumn) VALUES ('$name','$country','$pincode','$city','$street','$date','20','$source','$willing_to_relocate','$email','$phone','$experience','$qualification','$cjob','$cemployer','$expected','$csalary','$skillset','$info','$selectedOption','$nperiod','$img_name','$refer','$portfolio_link'$joiningDateInsertValue)";
		if($connect->query($sql) === TRUE) {
			$leadId = (int) $connect->insert_id;
			$valid['success'] = true;
			$valid['messages'] = "Thank you! We have received your application at DigiChefs, We shall get back to you soon.";
			SendMailHTML("careers@digichefs.com,contact@digichefs.com", 'Digichefs || Job Enquiry received', $body, '', $url);
			SendMailHTML("$email", 'DigiChefs || Your Job Application is Received', $receivedbody, '', '');
			syncApplicantToMailerLite($email, $name, $valid);
			syncApplicantToBrevo($email, $valid);
			processResumeLead($connect, array(
				'id' => $leadId,
				'name' => stripslashes($name),
				'email' => stripslashes($email),
				'phonenumber' => stripslashes($phone),
				'resume' => $img_name
			));
		} else {
			@unlink($url);
			$valid['messages'] = "Error while adding the Candidate" . $connect->error;
		}
	}
	else{
        $existingLead = $result->fetch_assoc();
        $leadId = isset($existingLead['id']) ? (int) $existingLead['id'] : 0;
        $existingResume = isset($existingLead['resume']) ? (string) $existingLead['resume'] : '';
		$finalResume = $img_name;
		$mailAttachment = $url;

        $finalResume = $connect->real_escape_string($finalResume);
        $sql = "UPDATE tblleads SET name='$name', country='$country', zip='$pincode', city='$city', street='$street', source='$source', willing_to_relocate='$willing_to_relocate', email='$email', phonenumber='$phone', experiance='$experience', qualification='$qualification', cjtitle='$cjob', cemployer='$cemployer', esalary='$expected', csalary='$csalary', skillset='$skillset', ainfo='$info', roles='$selectedOption', nperiod='$nperiod', resume='$finalResume', referral='$refer', portfolio='$portfolio_link'$joiningDateUpdate, modified='$date' WHERE id='$leadId'";

		if ($leadId > 0 && $connect->query($sql) === TRUE) {
			$valid['success'] = true;
			$valid['messages'] = "Thank you! We have updated your application at DigiChefs.";
			SendMailHTML('careers@digichefs.com,contact@digichefs.com', 'Digichefs || Job Application updated', $body, '', $mailAttachment);
            SendMailHTML("$email", 'DigiChefs || Your Job Application is Updated', $receivedbody, '', '');
            syncApplicantToMailerLite($email, $name, $valid);
            syncApplicantToBrevo($email, $valid);
            processResumeLead($connect, array(
                'id' => $leadId,
                'name' => stripslashes($name),
                'email' => stripslashes($email),
                'phonenumber' => stripslashes($phone),
                'resume' => $finalResume
            ));
		} else {
			@unlink($url);
            $valid['success'] = false;
            $valid['messages'] = "Error while updating your application" . $connect->error;
        }
	}
	$connect->close();
}// /if $_POST
echo json_encode($valid);



