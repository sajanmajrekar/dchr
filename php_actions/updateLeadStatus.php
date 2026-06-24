<?php 	
include("../includes/function.php");
include("../includes/dbcon.php");
require_once 'core.php';

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {	
	$id=$_POST['Editlead-id'];
	$userid= $_SESSION['id'];
	$name = addslashes($_POST['editlead-name']);
	$phone = $_POST['editlead-phone'];
	$email = $_POST['editlead-email'];
	$leadstatus = $_POST['editleadstatus'];
	$leadsource = $_POST['editlead-source'];

	$street = addslashes($_POST['editstreet']);
	$country = addslashes($_POST['editcountry']);
	$city = addslashes($_POST['editcity']);
	$pin = addslashes($_POST['editpincode']);
	$experience = addslashes($_POST['editexperience']);
	$qualification = addslashes($_POST['editqualification']);
	$cjob = addslashes($_POST['editcjob']);
	$cemployer = addslashes($_POST['editcemployer']);
	$expectedsal = addslashes($_POST['editexpected']);
	$csalary = addslashes($_POST['editcsalary']);
	$skill = addslashes($_POST['editskillset']);
	$additional = addslashes($_POST['editinfo']);
	$notice = addslashes($_POST['editnotice']);
	$willingToRelocate = isset($_POST['editwillingtorelocate']) ? addslashes($_POST['editwillingtorelocate']) : '';
	$selectedOption = "";
	
	if(isset($_POST['editexample-chosen-multiple'])){
		$selectedOption = implode(",",$_POST['editexample-chosen-multiple']);
	}
	if(isset($_FILES['editexample-file-input']['name'])){
		$img_name = $_FILES['editexample-file-input']['name'];
	    $img = explode('.', $_FILES['editexample-file-input']['name']);
	    $type = $img[count($img)-1];
	    $img_name=uniqid(rand()).'.'.$type;
	    $url = '../resume/'.$img_name;
	}else{
		$img_name="";
	}

	$comment="";
	if(isset($_POST['editlead-commentbox'])){
		$comment = addslashes($_POST['editlead-commentbox']);
	}
	$hasComment = trim($comment) !== '';
	$statuschange = false;
	$query = query("select * from `tblleads` WHERE id ='".$id."'");
	$row = $query->fetch_object();
	$path_filename_ext="";
	if(!empty($row->resume))
	{
	    $path_filename_ext = '../resume/'.$row->resume;
	}
		if($leadstatus == ($row->status))
		{
			$statuschange = false;
		}else{
			$statuschange = true;
		}
		$shouldLogActivity = $statuschange || $hasComment;
		if($statuschange){
			if(isset($_FILES['editexample-file-input']['name'])){
				if(in_array($type, array('docx', 'doc', 'pdf', 'rtf', 'DOCX', 'DOC', 'PDF', 'RTF'))) {
		    		if(is_uploaded_file($_FILES['editexample-file-input']['tmp_name'])) {
		         		if(move_uploaded_file($_FILES['editexample-file-input']['tmp_name'], $url)) {
		         		    if(!empty($path_filename_ext))
	                        {
		         			    unlink($path_filename_ext) or die("Couldn't delete file");
	                        }
							date_default_timezone_set('Asia/Kolkata');
				        	$date = date('Y-m-d H:i:s');
							$sql = "UPDATE tblleads SET name = '$name', phonenumber = '$phone', email = '$email',  source = '$leadsource', willing_to_relocate = '$willingToRelocate', status = '$leadstatus', street='$street', country='$country',city='$city',zip ='$pin', experiance ='$experience', qualification = '$qualification', cjtitle = '$cjob', cemployer = '$cemployer', esalary ='$expectedsal', csalary = '$csalary', skillset = '$skill', ainfo = '$additional', roles = '$selectedOption', lastcontact='$date', resume = '$img_name', nperiod = '$notice',followup = followup + 1 WHERE id = '$id'";
							$logSuccess = true;
							if($shouldLogActivity){
								$sql1 = "INSERT INTO `tblleadactivitylog`(leadid, description, date, staffid, additional_data) VALUES('$id', '$leadstatus', '$date', '$userid', '$comment')";
								$logSuccess = $connect->query($sql1) === TRUE;
							}
							if($connect->query($sql) === TRUE && $logSuccess) {
							 	$valid['success'] = true;
								$valid['messages'] = "Successfully Updated";	
							} else {
							 	$valid['success'] = false;
							 	$valid['messages'] = "Error while adding the members" . $connect->error;
							}
						}
					}
				}
			}else{
							date_default_timezone_set('Asia/Kolkata');
				        	$date = date('Y-m-d H:i:s');
							$sql = "UPDATE tblleads SET name = '$name', phonenumber = '$phone', email = '$email',  source = '$leadsource', willing_to_relocate = '$willingToRelocate', status = '$leadstatus', street='$street', country='$country',city='$city',zip ='$pin', experiance ='$experience', qualification = '$qualification', cjtitle = '$cjob', cemployer = '$cemployer', esalary ='$expectedsal', csalary = '$csalary', skillset = '$skill', ainfo = '$additional', roles = '$selectedOption', lastcontact='$date',  nperiod = '$notice', followup = followup + 1 WHERE id = '$id'";
							$logSuccess = true;
							if($shouldLogActivity){
								$sql1 = "INSERT INTO `tblleadactivitylog`(leadid, description, date, staffid, additional_data) VALUES('$id', '$leadstatus', '$date', '$userid', '$comment')";
								$logSuccess = $connect->query($sql1) === TRUE;
							}
							if($connect->query($sql) === TRUE && $logSuccess) {
							 	$valid['success'] = true;
								$valid['messages'] = "Successfully Updated";	
							} else {
							 	$valid['success'] = false;
							 	$valid['messages'] = "Error while adding the members" . $connect->error;
							}
			}
		}
		else{
			if(isset($_FILES['editexample-file-input']['name'])){
				if(in_array($type, array('docx', 'doc', 'pdf', 'rtf', 'DOCX', 'DOC', 'PDF', 'RTF'))) {
		    		if(is_uploaded_file($_FILES['editexample-file-input']['tmp_name'])) {
		         		if(move_uploaded_file($_FILES['editexample-file-input']['tmp_name'], $url)) {
		         		    if(!empty($path_filename_ext))
	                        {
		         			    unlink($path_filename_ext) or die("Couldn't delete file");
	                        }
							date_default_timezone_set('Asia/Kolkata');
							$date = date('Y-m-d H:i:s');
							$sql = "UPDATE tblleads SET name = '$name', phonenumber = '$phone', email = '$email',  source = '$leadsource', willing_to_relocate = '$willingToRelocate', status = '$leadstatus', street='$street', country='$country',city='$city',zip ='$pin', experiance ='$experience', qualification = '$qualification', cjtitle = '$cjob', cemployer = '$cemployer', esalary ='$expectedsal', csalary = '$csalary', skillset = '$skill', ainfo = '$additional', roles = '$selectedOption', nperiod = '$notice', resume = '$img_name' WHERE id = '$id'";
							$logSuccess = true;
							if($shouldLogActivity){
								$sql1 = "INSERT INTO `tblleadactivitylog`(leadid, description, date, staffid, additional_data) VALUES('$id', '$leadstatus', '$date', '$userid', '$comment')";
								$logSuccess = $connect->query($sql1) === TRUE;
							}
							if($connect->query($sql) === TRUE && $logSuccess) {
							 	$valid['success'] = true;
								$valid['messages'] = "Successfully Updated";	
							} else {
							 	$valid['success'] = false;
							 	$valid['messages'] = "Error while adding the members" . $connect->error;
							}
						}
					}
				}
			}else{
							date_default_timezone_set('Asia/Kolkata');
							$date = date('Y-m-d H:i:s');
							$sql = "UPDATE tblleads SET name = '$name', phonenumber = '$phone', email = '$email',  source = '$leadsource', willing_to_relocate = '$willingToRelocate', status = '$leadstatus', street='$street', country='$country',city='$city',zip ='$pin', experiance ='$experience', qualification = '$qualification', cjtitle = '$cjob', cemployer = '$cemployer', esalary ='$expectedsal', csalary = '$csalary', skillset = '$skill', ainfo = '$additional', nperiod = '$notice', roles = '$selectedOption' WHERE id = '$id'";
							$logSuccess = true;
							if($shouldLogActivity){
								$sql1 = "INSERT INTO `tblleadactivitylog`(leadid, description, date, staffid, additional_data) VALUES('$id', '$leadstatus', '$date', '$userid', '$comment')";
								$logSuccess = $connect->query($sql1) === TRUE;
							}
							if($connect->query($sql) === TRUE && $logSuccess) {
							 	$valid['success'] = true;
								$valid['messages'] = "Successfully Updated";	
							} else {
							 	$valid['success'] = false;
							 	$valid['messages'] = "Error while adding the members" . $connect->error;
							}
			}
		}
	}
	$connect->close();
	
	echo json_encode($valid);
