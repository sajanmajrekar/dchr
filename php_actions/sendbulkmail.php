<?php 	

require_once 'core.php';
include('../mail/lib.php');

$valid['success'] = array('success' => false, 'messages' => array());

function sendBulkMailLeadColumnExists($connect, $columnName)
{
    static $leadColumns = null;

    if ($leadColumns === null) {
        $leadColumns = array();
        $columnsResult = $connect->query("SHOW COLUMNS FROM tblleads");

        if ($columnsResult instanceof mysqli_result) {
            while ($column = $columnsResult->fetch_assoc()) {
                $leadColumns[$column['Field']] = true;
            }
            $columnsResult->close();
        }
    }

    return isset($leadColumns[$columnName]);
}

function sendBulkMailAnnualSalarySql($columnName)
{
    $columnName = trim((string) $columnName);
    $cleanValue = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(COALESCE(" . $columnName . ", ''))), ',', ''), ' ', ''), 'rs.', ''), 'rs', ''), 'lakhs', ''), 'lpa', '')";

    return "(CASE
        WHEN TRIM(LOWER(COALESCE(" . $columnName . ", ''))) IN ('', 'na', 'n/a', 'none', 'null', '-') THEN NULL
        WHEN " . $cleanValue . " REGEXP '^[0-9]+(\\\\.[0-9]+)?$' THEN
            CASE
                WHEN CAST(" . $cleanValue . " AS DECIMAL(12,2)) < 100 THEN CAST(" . $cleanValue . " AS DECIMAL(12,2)) * 100000
                WHEN CAST(" . $cleanValue . " AS DECIMAL(12,2)) < 100000 THEN CAST(" . $cleanValue . " AS DECIMAL(12,2)) * 12
                ELSE CAST(" . $cleanValue . " AS DECIMAL(12,2))
            END
        ELSE NULL
    END)";
}

function sendBulkMailNormalizeSalaryThreshold($value)
{
    $value = strtolower(trim((string) $value));
    if ($value === '') {
        return null;
    }

    $value = str_replace(array(',', ' ', 'rs.', 'rs'), '', $value);

    if (preg_match('/^(\d+(?:\.\d+)?)(l|lac|lacs|lakh|lakhs|lpa)$/', $value, $matches)) {
        return (float) $matches[1] * 100000;
    }

    if (preg_match('/^(\d+(?:\.\d+)?)(k)$/', $value, $matches)) {
        return (float) $matches[1] * 1000;
    }

    if (preg_match('/^(\d+(?:\.\d+)?)(cr|crore|crores)$/', $value, $matches)) {
        return (float) $matches[1] * 10000000;
    }

    if (!preg_match('/^\d+(?:\.\d+)?$/', $value)) {
        return null;
    }

    $amount = (float) $value;
    if ($amount < 100) {
        return $amount * 100000;
    }
    if ($amount < 100000) {
        return $amount * 12;
    }

    return $amount;
}

function sendBulkMailNormalizedExperienceSql($columnName)
{
    $columnName = trim((string) $columnName);
    $cleanValue = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(COALESCE(" . $columnName . ", ''))), 'years', ''), 'year', ''), 'yrs', ''), 'yr', ''), ' ', '')";

    return "(CASE
        WHEN TRIM(LOWER(COALESCE(" . $columnName . ", ''))) IN ('', 'na', 'n/a', 'none', 'null', '-') THEN NULL
        WHEN " . $cleanValue . " REGEXP '^[0-9]+(\\\\.[0-9]+)?$' THEN
            CASE
                WHEN " . $cleanValue . " LIKE '%.%' THEN CAST(" . $cleanValue . " AS DECIMAL(12,2))
                WHEN CAST(" . $cleanValue . " AS UNSIGNED) >= 8 THEN CAST(" . $cleanValue . " AS DECIMAL(12,2)) / 12
                ELSE CAST(" . $cleanValue . " AS DECIMAL(12,2))
            END
        ELSE NULL
    END)";
}

function sendBulkMailNormalizeExperienceThreshold($value)
{
    $value = strtolower(trim((string) $value));
    if ($value === '') {
        return null;
    }

    $value = str_replace(array('years', 'year', 'yrs', 'yr', ' '), '', $value);
    if (!preg_match('/^\d+(?:\.\d+)?$/', $value)) {
        return null;
    }

    return (float) $value;
}

if($_POST) {
// $leadid=$_POST['checkbox'];
$subject=addslashes($_POST['subject']);
$body=addslashes($_POST['mailcontents']);
$i= 0;
 if($_POST["is_date_search"] == "yes")
    {
        $conditions = array();
        $sql = "SELECT tblleads.id, tblleads.name as mainname, tblleads.email, tblleads.phonenumber, tblleads.roles, tblleads.nperiod, tblleads.experiance, tblleads.dateadded, tblleads.status, tblleads.source, tblleadsstatus.name, tblleadssources.name, tblleads.lastcontact FROM tblleads INNER JOIN tblleadssources on tblleads.source = tblleadssources.id inner join tblleadsstatus on tblleads.status = tblleadsstatus.id";

        if (!empty($_POST["roles"]) && sendBulkMailLeadColumnExists($connect, 'roles')) {
            $conditions[] = "FIND_IN_SET('" . $connect->real_escape_string($_POST["roles"]) . "', tblleads.roles)";
        }

        if (!empty($_POST["nperiod"]) && sendBulkMailLeadColumnExists($connect, 'nperiod')) {
            $conditions[] = "tblleads.nperiod <= '" . $connect->real_escape_string($_POST["nperiod"]) . "'";
        }

        if (!empty($_POST["experiance"]) && sendBulkMailLeadColumnExists($connect, 'experiance')) {
            $normalizedExperience = sendBulkMailNormalizeExperienceThreshold($_POST["experiance"]);
            if ($normalizedExperience !== null) {
                $conditions[] = sendBulkMailNormalizedExperienceSql('tblleads.experiance') . " >= " . (float) $normalizedExperience;
            }
        }

        if (!empty($_POST["leadstatus"]) && sendBulkMailLeadColumnExists($connect, 'status')) {
            $conditions[] = "tblleads.status = '" . $connect->real_escape_string($_POST["leadstatus"]) . "'";
        }

        if (!empty($_POST["leadsource"]) && sendBulkMailLeadColumnExists($connect, 'source')) {
            $conditions[] = "tblleads.source = '" . $connect->real_escape_string($_POST["leadsource"]) . "'";
        }

        if (!empty($_POST["city"]) && sendBulkMailLeadColumnExists($connect, 'city')) {
            $conditions[] = "tblleads.city LIKE '%" . $connect->real_escape_string($_POST["city"]) . "%'";
        }

        if (!empty($_POST["relocate"]) && sendBulkMailLeadColumnExists($connect, 'willing_to_relocate')) {
            $conditions[] = "tblleads.willing_to_relocate = '" . $connect->real_escape_string($_POST["relocate"]) . "'";
        }

        if (!empty($_POST["currentctc"]) && sendBulkMailLeadColumnExists($connect, 'csalary')) {
            $normalizedCurrentCtc = sendBulkMailNormalizeSalaryThreshold($_POST["currentctc"]);
            if ($normalizedCurrentCtc !== null) {
                $conditions[] = sendBulkMailAnnualSalarySql('tblleads.csalary') . " <= " . (float) $normalizedCurrentCtc;
            }
        }

        if (!empty($_POST["expectedctc"]) && sendBulkMailLeadColumnExists($connect, 'esalary')) {
            $normalizedExpectedCtc = sendBulkMailNormalizeSalaryThreshold($_POST["expectedctc"]);
            if ($normalizedExpectedCtc !== null) {
                $conditions[] = sendBulkMailAnnualSalarySql('tblleads.esalary') . " <= " . (float) $normalizedExpectedCtc;
            }
        }

        if (!empty($_POST["start_date"]) && !empty($_POST["end_date"])) {
            $conditions[] = "DATE(tblleads.dateadded) BETWEEN '" . $connect->real_escape_string($_POST["start_date"]) . "' AND '" . $connect->real_escape_string($_POST["end_date"]) . "'";
        }

        if (!empty($_POST["interval"])) {
            $interval = trim((string) $_POST["interval"]);
            if ($interval === 'last-seven') {
                $conditions[] = "DATE(tblleads.dateadded) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            } elseif ($interval === 'last-thirty') {
                $conditions[] = "DATE(tblleads.dateadded) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            } elseif ($interval === 'last-month') {
                $conditions[] = "DATE(tblleads.dateadded) >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
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
