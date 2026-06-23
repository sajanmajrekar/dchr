<?php 	

require_once 'core.php';
include('../mail/lib.php');

$valid['success'] = array('success' => false, 'messages' => array());

if($_POST) {
// $leadid=$_POST['checkbox'];
$subject=addslashes($_POST['subject']);
$body=addslashes($_POST['mailcontents']);
$i= 0;
 if($_POST["is_date_search"] == "yes")
    {
    	$sql = "SELECT tblleads.id, tblleads.name as mainname, tblleads.email, tblleads.phonenumber, tblleads.roles,tblleads.nperiod,tblleads.experiance, tblleads.dateadded, tblleads.status, tblleads.source, tblleadsstatus.name, tblleadssources.name, tblleads.lastcontact FROM tblleads INNER JOIN tblleadssources on tblleads.source = tblleadssources.id inner join tblleadsstatus on tblleads.status = tblleadsstatus.id where ";
        if(!empty($_POST["roles"])){
		$sql .= "tblleads.roles like '%".$_POST["roles"]."%' ";
    		if(!empty($_POST["nperiod"]) || !empty($_POST["experiance"]) || !empty($_POST["start_date"])){
    			$sql .= "and ";
    		}
    	}
    	if(!empty($_POST["nperiod"])){
    		$sql .= "tblleads.nperiod<='".$_POST["nperiod"]."' ";
    		if(!empty($_POST["experiance"]) || !empty($_POST["start_date"])){
    			$sql .= "and ";
    		}
    	}
     	if(!empty($_POST["experiance"])){
     		$sql .= "tblleads.experiance >= '".$_POST["experiance"]."' ";
     		if(!empty($_POST["start_date"])){
    			$sql .= "and ";
    		}
     	}
     	if(!empty($_POST["start_date"])){
     		$sql .= "tblleads.dateadded BETWEEN '".$_POST["start_date"]."' AND '".$_POST["end_date"]."' + INTERVAL 1 DAY ";
     	}
     	$sql .= " order by tblleads.dateadded desc";
    }
    else{
    $sql = "SELECT tblleads.id, tblleads.name, tblleads.email, tblleads.phonenumber,tblleads.nperiod,tblleads.experiance, tblleads.dateadded, tblleads.status, tblleads.source, tblleadsstatus.name, tblleadssources.name, tblleads.lastcontact, tblleads.city,tblleads.leadtype,tblleads.purpose FROM tblleads INNER JOIN tblleadssources on tblleads.source = tblleadssources.id inner join tblleadsstatus on tblleads.status = tblleadsstatus.id order by id desc";
}
//echo $sql;
//echo $sql;//die();
$result = $connect->query($sql);

$output = array('data' => array());
// $output['data'][] = array( $sql);
    if($result->num_rows > 0) { 
        
    
     // $row = $result->fetch_array();
     $active = ""; 
     
     $sentid=[];
   $body .= '<b>Thanks!</b>';
         while($row = $result->fetch_array()) {
        
        // $mailaddress = []; 
        // foreach ($leadid as $key => $value) {
        // 	array_push($mailaddress, getemail($value));
        // }
        // $bcc = implode(",",$mailaddress);
             array_push($sentid, $row['id']);
              $sql2 = "SELECT SUM(totalemailsent) as total FROM `emaillogs` where Date(senttime)= CURDATE()";
              $result2 = $connect->query($sql2);
              if($result2->num_rows > 0) { 
                   while($row2 = $result2->fetch_array()) {
                   if($row2['total']+$i>=250){
                       $valid['success'] = true;
                       $valid['messages'] = $i." Emails sent. Task terminate due to daily limit exceeded";	
                   }else{
                        if(SendMailHTML($row['email'],$subject,$body,'',''))
                          {
                                $i++;
                        		$valid['success'] = true;
                        		$valid['messages'] = "Emails sent successfully.";	
                          }
                        else{
                        		$valid['success'] = false;
                        		$valid['messages'] = "Something went wrong.";	
                        }
                   }
                   }
              }
             // /if $_POST
        }
       
       	
    }
        $date = date('Y-m-d H:i:s');
       	$finalstring=implode(",",$sentid);
       	$user=$_SESSION['id'];
        $sql1 ="INSERT INTO `emaillogs`(`subject`, `mailcontent`, `mailsentto`, `senttime`,`sentby`,`totalemailsent`) VALUES ('$subject','$body','$finalstring','$date','$user','$i' )";
		if($connect->query($sql1) === TRUE) {
		    
		} 
		else {					
	    }
    $connect->close();
}
echo json_encode($valid);

?>