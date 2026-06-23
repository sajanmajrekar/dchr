<?php 	

require_once 'core.php';

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {	
	$name = addslashes($_POST['name']);
	$email = addslashes($_POST['email']);
	$phone = addslashes($_POST['phone']);
	$source = addslashes($_POST['source']);
	$street = addslashes($_POST['street']);
	$country = addslashes($_POST['country']);
	$city = addslashes($_POST['city']);
	$pincode = addslashes($_POST['pincode']);
	$experience = addslashes($_POST['experience']);
	$qualification = addslashes($_POST['qualification']);
	$cjob = addslashes($_POST['cjob']);
	$cemployer = addslashes($_POST['cemployer']);
	$expected = addslashes($_POST['expected']);
	$csalary = addslashes($_POST['csalary']);
	$skillset = addslashes($_POST['skillset']);
	$info = addslashes($_POST['info']);
	$selectedOption = "";
	
	if(isset($_POST['example-chosen-multiple'])){
		$selectedOption = implode(",",$_POST['example-chosen-multiple']);
	}
	// if(isset($_POST['example-chosen-multiple'])){
	// 	$main=$_POST['example-chosen-multiple'];
	//     foreach ($main as $selectedOption) {
 //    		$selectedOption .= $selectedOption;
	// 	}
	// }
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
					$sql ="INSERT INTO `tblleads`(`name`, `country`, `zip`, `city`, `street`,`dateadded`, `status`, `source`,  `email`, `phonenumber`, `experiance`, `qualification`, `cjtitle`, `cemployer`, `esalary`, `csalary`, `skillset`, `ainfo`, `roles`, `nperiod`, `resume`) VALUES ('$name','$country','$pincode','$city','$street','$date','20','$source','$email','$phone','$experience','$qualification','$cjob','$cemployer','$expected','$csalary','$skillset','$info','$selectedOption','$nperiod','$img_name')";
						if($connect->query($sql) === TRUE) {
							$valid['success'] = true;
							$valid['messages'] = "Candidate created successfully.";	
						} else {
							$valid['success'] = false;
							$valid['messages'] = "Error while adding the Candidate" . $connect->error;
						}
					}
				}
			}
		}else{
			$sql ="INSERT INTO `tblleads`(`name`, `country`, `zip`, `city`, `street`,`dateadded`, `status`, `source`,  `email`, `phonenumber`, `experiance`, `qualification`, `cjtitle`, `cemployer`, `esalary`, `csalary`, `skillset`, `ainfo`, `roles`, `nperiod`, `resume`) VALUES ('$name','$country','$pincode','$city','$street','$date','20','$source','$email','$phone','$experience','$qualification','$cjob','$cemployer','$expected','$csalary','$skillset','$info','$selectedOption','$nperiod','$img_name')";
						if($connect->query($sql) === TRUE) {
							$valid['success'] = true;
							$valid['messages'] = "Candidate created successfully.";	
						} else {
							$valid['success'] = false;
							$valid['messages'] = "Error while adding the Candidate" . $connect->error;
						}
		}
	}
		else{
				$valid['success'] = false;
				$valid['messages'] = "Duplicate record.";
	}
	$connect->close();
}// /if $_POST
echo json_encode($valid);