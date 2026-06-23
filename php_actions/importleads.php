<?php 	

require_once 'core.php';
$valid['success'] = array('success' => false, 'messages' => array());
if($_POST){
    
    $total_imported = 1;
    $rows = [];
    $sql ="";
    $date = date('Y-m-d H:i:s');
    $source = $_POST['import-leadsource'];
    if(isset($_POST['import-leadassigned'])){
    	$assigned = $_POST['import-leadassigned'];
	}
    $requiredHeaders = array('Name', 'Email', 'Phone','Street',	'City',	'Country', 'Pincode', 'Experience',	'Current Job',	'Expected Salary', 'Skill Set','Roles','Qualification',	'Current Employer','Current Salary', 'Additional Info',	'Notice Period', 'Status');
    
   	if (($_FILES['leadfile']['name']!="")){
		// Where the file is going to be stored
			$target_dir = "upload/";
			$file = $_FILES['leadfile']['name'];
			$path = pathinfo($file);
			$filename = $path['filename'];
			$ext = $path['extension'];
			$temp_name = $_FILES['leadfile']['tmp_name'];
			$path_filename_ext = $target_dir.$filename.".".$ext;
		 
		// Check if file already exists
		if (file_exists($path_filename_ext)) {
		 	echo "Sorry, file already exists.";
		 }
		 else
		 {
		 if(move_uploaded_file($temp_name,$path_filename_ext)){
		     	
		 	$import_result = true;
            $fd            = fopen($path_filename_ext, 'r');
            $rows          = [];
            $firstLine = fgets($fd);
            $foundHeaders = str_getcsv(trim($firstLine), ',', '"');
            if ($foundHeaders == $requiredHeaders) {
            	while ($row = fgetcsv($fd)) {
                    	$rows[] = $row;
                    	$roles = getrole(addslashes($row[11]));
                    	$status=trim(addslashes($row[17]));
                    	$status = getstatus($status);
                	if (count($rows) < 1) {
		            	$valid['success'] = FALSE;
						$valid['messages'] = "Not enough rows for importing";
	            	}else{
	            		if($row[0] != null && CheckEmail($row[1]) &&  $row[2] != null){
	            			//$sql = "INSERT INTO tblleads(name, email, phonenumber, dateadded, source, status) values('$row[0]','$row[1]','$row[2]', '$date',  '$source', '20')";
	            			$name= addslashes($row[0]);
	            			$country = addslashes($row[5]);
	            			$zip = addslashes($row[6]);
	            			$city = addslashes($row[4]);
	            			$steet = addslashes($row[3]);
	            			$email = addslashes($row[1]);
	            			$phone = addslashes($row[2]);
	            			$exp = addslashes($row[7]);
	            			$qua = addslashes($row[12]);
	            			$cjtitle = addslashes($row[8]);
	            			$cemp = addslashes($row[13]);
	            			$esal = addslashes($row[9]);
	            			$csal = addslashes($row[14]);
	            			$skill = addslashes($row[10]);
	            			$ainfo = addslashes($row[15]);
	            			$nperiod = addslashes($row[16]);

	            			$sql ="INSERT INTO `tblleads`(`name`, `country`, `zip`, `city`, `street`,`dateadded`, `status`, `source`,  `email`, `phonenumber`, `experiance`, `qualification`, `cjtitle`, `cemployer`, `esalary`, `csalary`, `skillset`, `ainfo`, `roles`, `nperiod`, `resume`) VALUES ('$name','$country','$zip','$city','$steet','$date','$status','$source','$email','$phone','$exp','$qua','$cjtitle','$cemp','$esal','$csal','$skill','$skill','$roles','$skill','')";
	            			if($connect->query($sql) === TRUE) {
								$valid['success'] = true;
								$valid['messages'] = "Total leads imported: ".$total_imported++;	
							} else {
								$valid['success'] = false;
								$valid['messages'] = "Error while importing the leads" . $connect->error;
							}
	            		}else{
	            		   	$valid['success'] = false;
							$valid['messages'] = "Duplicate Leads"; 
	            		}
	            	}
                }
			} 
			else{
		        	$valid['success'] = false;
					$valid['messages'] = "CSV file is invalid. Please check sample file and try again.";
	        }
	            fclose($fd);
	            
	        }
	       
		   unlink($path_filename_ext) or die("Couldn't delete file");
	}
}
} 
echo json_encode($valid);