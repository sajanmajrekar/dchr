<?php 	
ini_set('display_errors', 1);
error_reporting(E_ALL);



require_once 'core.php';

$id = $_POST['Id'];
$sql = "SELECT tblleads.id, tblleads.name, tblleads.email, tblleads.phonenumber, tblleads.dateadded, tblleads.status, tblleads.source,tblleads.street,tblleads.country, tblleads.city, tblleads.zip,  tblleads.experiance,tblleads.experiance,tblleads.qualification,tblleads.cjtitle,tblleads.cemployer,tblleads.esalary,tblleads.resume, tblleads.csalary, tblleads.csalary,tblleads.skillset,tblleads.ainfo, tblleads.roles,tblleads.nperiod,tblleadsstatus.id as statusid,tblleadsstatus.name as statusname,tblleadssources.id as sourceid, tblleadssources.name as sourcename, tblleads.lastcontact, tblleads.followup, tblleads.referral FROM tblleads INNER JOIN tblleadssources on tblleads.source = tblleadssources.id inner join tblleadsstatus on tblleads.status = tblleadsstatus.id where tblleads.id=$id";
$row = 0;
$result = $connect->query($sql);

if($result->num_rows > 0) { 
 $row = $result->fetch_array();
} // if num_rows
$connect->close();

echo json_encode($row);

?>