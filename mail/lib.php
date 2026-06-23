<?php

	//require 'phpmailer/class.phpmailer.php';
	//require 'phpmailer/PHPMailerAutoload.php';
	
	use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
	
	require 'PHPMailer/src/PHPMailer.php';
	require 'PHPMailer/src/SMTP.php';
	require 'PHPMailer/src/Exception.php';
	

	function SendMailHTML($ToEmail, $subject, $body,$bcc,$path)
	{
		$mail = new PHPMailer();
		$mail->SMTPDebug = 0;
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'digichefs.in';  					  // Specify main and backup SMTP servers   smtp.gmail.com
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'hr@digichefs.in';                // SMTP username  
		$mail->Password = 'Digi@1234';                       // SMTP password  
		$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                                    // TCP port to connect to
		$mail->CharSet = 'UTF-8';
		$mail->setFrom('hr@digichefs.in', 'Admin');
		$mail->isHTML(true);

		$Recieptant=array();
		$Recieptant=explode(',',$ToEmail);
		$Recieptantbcc=explode(',',$bcc);
		foreach($Recieptant as $EmailTo)
		{
			$EmailTo = trim($EmailTo);
			if($EmailTo !== ''){
				$mail->addAddress($EmailTo, '');
			}
		}
		
		foreach($Recieptantbcc as $EmailTo2)
		{
			$EmailTo2 = trim($EmailTo2);
			if($EmailTo2 !== ''){
				$mail->addBcc($EmailTo2, '');
			}
		}
		
		//Set the subject line
		$mail->Subject = $subject;
		$mail->msgHTML($body);
		$mail->AltBody = '';

		if(!empty($path) && file_exists($path)) {
			$mail->addAttachment($path);
		}

		if(!$mail->Send())
		{
			error_log('PHPMailer send failed: ' . $mail->ErrorInfo);
			return false;
		}
		else
		{
			return true;
		}
	}

?>
