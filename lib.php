<?php

	require 'phpmailer/class.phpmailer.php';
	require 'phpmailer/PHPMailerAutoload.php';

	function SendMailHTML($ToEmail, $subject, $body,$bcc,$path)
	{
		

		$mail = new PHPMailer;

		$mail->SMTPDebug = 0;                                 // Enable verbose debug output
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'breezelms.in';  					  // Specify main and backup SMTP servers   smtp.gmail.com
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'donotreply@breezelms.in';                // SMTP username  
		$mail->Password = '{U]Jj}]Ie07O';                       // SMTP password  
		$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                                    // TCP port to connect to
		$mail->From = 'donotreply@breezelms.in'; 
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

?>
