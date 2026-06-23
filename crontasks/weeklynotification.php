<?php 	

require_once '../php_actions/core.php';
include('../mail/lib.php');


//if( $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'] ){
    $Total=""; 
    $Users=""; 
    $close="";
	$sql = "SELECT * from tblleads";
	$result = $connect->query($sql);
	if($result->num_rows > 0) { 
		$Total = $result->num_rows;
	}
	$sql1 = "SELECT * from tblstaff where admin!=2";
    $result1 = $connect->query($sql1);
    if($result1->num_rows > 0) { 
      $Users = $result1->num_rows;
    }  
    $sql2 = "SELECT * from tblleads where status=18";
    $result2 = $connect->query($sql2);
    if($result2->num_rows > 0) { 
      $close = $result2->num_rows;
    } 
    $body='';    
	
	$body.='<table align="center" border="1" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">
						<tr style="text-align:center;background:#3db4dc;color:#fff">
                            <th style="padding:10px">
								Total Leads
							</th>
							<th>
								Users
							</th>
							<th>
								Leads Closed
							</th>
						</tr>
			<tr style="text-align:center;padding:10px;">
							<td style="padding:10px">
								'.$Total.'
							</td>
							<td>
								'. $Users .'
							</td>
							<td>
								'.$close.'
							</td>
			</tr></table>';

		$body.='<h2 style="text-align:center">LEAD ASSIGNED</h2>
	<table align="center" border="1" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">
						<tr style="text-align:center;background:#3db4dc;color:#fff">
                            <th style="padding:10px">
								Sr no.
							</th>
							<th>
								Name
							</th>
							<th>
								Access Type
							</th>
							<th>
								Email
							</th>
							<th>
								Leads Assigned
							</th></tr>';
			$sql3 = "SELECT staffid, firstname, lastname, tblstaff.email, active, admin, COUNT(tblleads.assigned) AS Total FROM `tblstaff` LEFT JOIN `tblleads` ON tblstaff.staffid = tblleads.assigned GROUP BY tblstaff.staffid";

$accesstype="";
$result3 = $connect->query($sql3);

$output = array('data' => array());

if($result3->num_rows > 0) { 

 // $row = $result->fetch_array();
 $active = ""; 
 $i= 1;

 while($row = $result3->fetch_array()) {
 	$userid = $row[0];
 	if($row[4] == 1) {
 		// activate member
 		$active = "<center><span class='label label-success'>Active</span></center>";
 	} else {
 		// deactivate member
 		$active = "<center><span class='label label-danger'>Deactive</span></center>";
 	} // /else
 	if($row[5] == 0){
 		$accesstype="Users";
 	}else if($row[5] == 1){
 		$accesstype="Admin";
 	}else if($row[5] == 2){
		$accesstype="Superadmin";	
 	}
 	$output['data'][] = array( 
 		//sr no.		
 		
 		// Username
 		$row[1].' '.$row[2], 
 		// Email
 		$accesstype,

 		$row[3],

 		$active,

 		$row[6],
 		// active
 			
 		); 	

			$body.='<tr style="text-align:center">
							<td style="padding:10px">
								'.$i++.'
							</td>
							<td>
								'.$row[1].' '.$row[2].'
							</td>
							<td>
								'.$accesstype.'
							</td>
							<td>
							'.$row[3].'
							</td>
							<td>
 							'.$row[6].'
 							</td>
			</tr>';

}
$body.= '</table></div>';
}	

$body .= '<h2 style="text-align:center">STATUS ASSIGNED</h2>
<table align="center" border="1" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">
						<tr style="text-align:center;background:#3db4dc;color:#fff">
                            <td style="padding:10px">
								Sr no.
							</td>
							<td>
								Status
							</td>
							<td>
								Total Leads
							</td>
			</tr>';
$sql5 = "SELECT tblleadsstatus.id, tblleadsstatus.name, COUNT(tblleads.status) AS Total FROM `tblleadsstatus` LEFT JOIN `tblleads` ON tblleadsstatus.id = tblleads.status GROUP BY tblleadsstatus.id";

$accesstype="";
$result5 = $connect->query($sql5);

$output = array('data' => array());

if($result5->num_rows > 0) { 

 // $row = $result->fetch_array();
 $active = ""; 
 $i= 1;

 while($row = $result5->fetch_array()) {
			$body .= '<tr style="text-align:center">
							<td style="padding:10px">		
 								'.$i++.'
							</div>
							<td>
								'.$row[1].'
							</td>
							<td>
								'.$row[2].'
							</td>
			</tr>';
	} // /while 
	$body .= '</table>';
}// if num_rows
			$body .= '<h2 style="text-align:center">FOLLOW-UP LEADS</h2>
						<table align="center" border="1" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">
						<tr style="text-align:center;background:#3db4dc;color:#fff">
                            <td style="padding:10px"">
								Follow-up no.
							</td>
							<td>
								Total Follow-ups
							</td>
			</div>';
for($i=0; $i<=5;$i++){
$query = "Select * from tblleads where followup='".$i."'";
 $result = $connect->query($query);
 
			$body .= '<tr style="text-align:center;">
							<td style="padding:10px">
								'.$i.'
							</td>
							<td>
								'. $result->num_rows .'
							</td>
						</tr>';
		}
		$body .= '</table>';
		$body .= '<h2 style="text-align:center">SOURCE LEADS</h2>
						<table align="center" border="1" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">
						<tr style="text-align:center;background:#3db4dc;color:#fff">
                            <td style="padding:10px">
								Sr no.
							</td>
							<td>
								Source
							</td>
							<td>
								Total Leads
							</td>
						</tr>';
$sql = "SELECT * FROM `tblleadssources` order by id asc";

$result = $connect->query($sql);

$output = array('data' => array());

if($result->num_rows > 0) { 

 $active = ""; 
 $i= 1;

 while($row = $result->fetch_array()) {
 	$id = $row[0];
 	$sql1 = "SELECT * FROM `tblleads` where source=$id";
	$result1 = $connect->query($sql1);
 	$total=$result1->num_rows;

 	$body .= '<tr style="text-align:center;">
							<td style="padding:10px">
								'.$i++.'
							</td>
							<td>
								'. $row[1] .'
							</td>
							<td>
								'.$total.'
							</td>
						</tr>';

 } // /while 
$body .= '</table>';
}// if num_rows
//echo $body;


if(SendMailHTML('nitesh.w@digichefs.com','Breeze || Weekly Reports',$body))
  {
	echo 'success';
  }
else{
	echo 'fail';
}