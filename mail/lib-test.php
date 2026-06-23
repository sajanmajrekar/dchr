<?php

	use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
	
	require 'PHPMailer/src/PHPMailer.php';
	require 'PHPMailer/src/SMTP.php';
	require 'PHPMailer/src/Exception.php';
	
	$name = "shoaib";
	$phone = "9090";
	$email = "shoaib@gmail.com";
	$selected_option_users = "";
	
	
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
	
	$mail = new PHPMailer();
    //Set PHPMailer to use the sendmail transport
    $mail->isSendmail();
    //Set who the message is to be sent from
    $mail->setFrom('hr@digichefs.in', 'Admin');
    //Set an alternative reply-to address
    $mail->addReplyTo('', '');
    //Set who the message is to be sent to
    $mail->addAddress('shoaib.b@digichefs.com', 'shoaib');
    //Set the subject line
    $mail->Subject = 'PHPMailer sendmail test';
    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    $mail->msgHTML($body);
    //Replace the plain text body with one created manually
    $mail->AltBody = '';
    //Attach an image file
    $mail->addAttachment('../resume/344901861645e088c9d2f9.pdf');
    
    //send the message, check for errors
    if (!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message sent!';
    }
	
	
	
    /*
	function SendMailHTML($ToEmail, $subject, $body,$bcc,$path)
	{
		
		$mail = new PHPMailer();
    //Set PHPMailer to use the sendmail transport
    $mail->isSendmail();
    //Set who the message is to be sent from
    $mail->setFrom('hr@digichefs.in', 'Admin');
    //Set an alternative reply-to address
    $mail->addReplyTo('', '');
    //Set who the message is to be sent to
    $mail->addAddress('shoaib.b@digichefs.com', 'shoaib');
    //Set the subject line
    $mail->Subject = 'PHPMailer sendmail test';
    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    $mail->msgHTML($body);
    //Replace the plain text body with one created manually
    $mail->AltBody = '';
    //Attach an image file
    $mail->addAttachment('../resume/344901861645e088c9d2f9.pdf');
    
    //send the message, check for errors
    if (!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message sent!';
    }
        
		$mail = new PHPMailer;

		$mail->SMTPDebug = 0;                                 // Enable verbose debug output
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'digichefs.in';  					  // Specify main and backup SMTP servers   smtp.gmail.com
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'hr@digichefs.in';                // SMTP username  
		$mail->Password = 'Digi@1234';                       // SMTP password  
		$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                                    // TCP port to connect to
		$mail->From = 'hr@digichefs.in'; 
		$mail->FromName = 'Admin';
		//Set an alternative reply-to address
		$mail->isHTML(false);                                  // Set email format to HTML
		$Recieptant=array();
		$Recieptant=explode(',',$ToEmail);
		$Recieptantbcc=explode(',',$bcc);
		foreach($Recieptant as $EmailTo)
		{
			$mail->AddAddress($EmailTo, '');
		}
		
		foreach($Recieptantbcc as $EmailTo2)
		{
			$mail->addBcc($EmailTo2, '');
		}
		
		//Set the subject line
		$mail->Subject = $subject;
		//Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
		$mail->MsgHTML($body);
		//Replace the plain text body with one created manually
		$mail->AltBody = '';
		$mail->AddAttachment($path); 
		//Send the message, check for errors
		if(!$mail->Send())
		{
			//return $mail->ErrorInfo;
			return false;
		}
		else
		{
			return true;
		}
	}
	*/

?>
