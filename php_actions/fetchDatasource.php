<?php

ob_start();
ini_set('display_errors', '0');
error_reporting(E_ALL);
header('Content-Type: application/json');
mysqli_report(MYSQLI_REPORT_OFF);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
        if (ob_get_length()) {
            ob_clean();
        }
        error_log('fetchDatasource fatal: ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line']);
        echo json_encode(array('data' => array(), 'error' => 'fetchDatasource fatal: ' . $error['message']));
    }
});

try {
    require_once 'core.php';
    if (!($connect instanceof mysqli)) {
        throw new Exception('Database connection failed.');
    }

    function datasourceLeadColumnExists($connect, $columnName)
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

    function datasourceAnnualSalarySql($columnName)
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

    function normalizeDatasourceSalaryThreshold($value)
    {
        $value = strtolower(trim((string) $value));
        if ($value === '') {
            return null;
        }

        $value = str_replace(array(',', ' ', 'rs.', 'rs', 'lakhs', 'lpa'), '', $value);
        if (!is_numeric($value)) {
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

    $isDateSearch = isset($_POST["is_date_search"]) && $_POST["is_date_search"] === "yes";
    $roles = isset($_POST["roles"]) ? trim($_POST["roles"]) : '';
    $nperiod = isset($_POST["nperiod"]) ? trim($_POST["nperiod"]) : '';
    $experiance = isset($_POST["experiance"]) ? trim($_POST["experiance"]) : '';
    $leadStatus = isset($_POST["leadstatus"]) ? trim($_POST["leadstatus"]) : '';
    $leadSource = isset($_POST["leadsource"]) ? trim($_POST["leadsource"]) : '';
    $city = isset($_POST["city"]) ? trim($_POST["city"]) : '';
    $relocate = isset($_POST["relocate"]) ? trim($_POST["relocate"]) : '';
    $currentCtc = isset($_POST["currentctc"]) ? trim($_POST["currentctc"]) : '';
    $expectedCtc = isset($_POST["expectedctc"]) ? trim($_POST["expectedctc"]) : '';
    $interval = isset($_POST["interval"]) ? trim($_POST["interval"]) : '';
    $startDate = isset($_POST["start_date"]) ? trim($_POST["start_date"]) : '';
    $endDate = isset($_POST["end_date"]) ? trim($_POST["end_date"]) : '';

    $sql = "SELECT tblleads.*, tblleadsstatus.name AS statusname, tblleadssources.name AS sourcename FROM tblleads INNER JOIN tblleadssources ON tblleads.source = tblleadssources.id INNER JOIN tblleadsstatus ON tblleads.status = tblleadsstatus.id";

    if ($isDateSearch) {
    	$conditions = array();

    	if ($roles !== '' && datasourceLeadColumnExists($connect, 'roles')) {
    		$conditions[] = "FIND_IN_SET('" . $connect->real_escape_string($roles) . "', tblleads.roles)";
    	}

    	if ($nperiod !== '' && datasourceLeadColumnExists($connect, 'nperiod')) {
    		$conditions[] = "tblleads.nperiod <= '" . $connect->real_escape_string($nperiod) . "'";
    	}

    	if ($experiance !== '' && datasourceLeadColumnExists($connect, 'experiance')) {
    		$conditions[] = "tblleads.experiance >= '" . $connect->real_escape_string($experiance) . "'";
    	}

        if ($leadStatus !== '' && datasourceLeadColumnExists($connect, 'status')) {
            $conditions[] = "tblleads.status = '" . $connect->real_escape_string($leadStatus) . "'";
        }

        if ($leadSource !== '' && datasourceLeadColumnExists($connect, 'source')) {
            $conditions[] = "tblleads.source = '" . $connect->real_escape_string($leadSource) . "'";
        }

        if ($city !== '' && datasourceLeadColumnExists($connect, 'city')) {
            $conditions[] = "tblleads.city LIKE '%" . $connect->real_escape_string($city) . "%'";
        }

        if ($relocate !== '' && datasourceLeadColumnExists($connect, 'willing_to_relocate')) {
            $conditions[] = "tblleads.willing_to_relocate = '" . $connect->real_escape_string($relocate) . "'";
        }

        if ($currentCtc !== '' && datasourceLeadColumnExists($connect, 'csalary')) {
            $normalizedCurrentCtc = normalizeDatasourceSalaryThreshold($currentCtc);
            if ($normalizedCurrentCtc !== null) {
                $conditions[] = datasourceAnnualSalarySql('tblleads.csalary') . " <= " . (float) $normalizedCurrentCtc;
            }
        }

        if ($expectedCtc !== '' && datasourceLeadColumnExists($connect, 'esalary')) {
            $normalizedExpectedCtc = normalizeDatasourceSalaryThreshold($expectedCtc);
            if ($normalizedExpectedCtc !== null) {
                $conditions[] = datasourceAnnualSalarySql('tblleads.esalary') . " <= " . (float) $normalizedExpectedCtc;
            }
        }

    	if ($startDate !== '' && $endDate !== '') {
    		$conditions[] = "DATE(tblleads.dateadded) BETWEEN '" . $connect->real_escape_string($startDate) . "' AND '" . $connect->real_escape_string($endDate) . "'";
    	}

        if ($interval !== '') {
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
    }

    $sql .= " ORDER BY tblleads.dateadded DESC";

    $result = $connect->query($sql);

    if ($result === false) {
        throw new Exception('Database query failed: ' . $connect->error);
    }

    $output = array('data' => array());
    if($result->num_rows > 0) {
     $i= 1;

     while($row = $result->fetch_array()) {
        $leadName = isset($row['name']) ? $row['name'] : '';
        $leadEmail = isset($row['email']) ? $row['email'] : '';
        $leadPhone = isset($row['phonenumber']) ? $row['phonenumber'] : '';
        $leadRoles = isset($row['roles']) ? $row['roles'] : '';
        $leadDateAdded = isset($row['dateadded']) ? $row['dateadded'] : '';
        $leadLastContact = isset($row['lastcontact']) ? $row['lastcontact'] : '';

     	$output['data'][] = array(
     		$i++,
     		$leadName,
     		$leadEmail,
     		$leadPhone,
     		getroletext($leadRoles),
     		!empty($leadDateAdded) ? date("d M, Y",strtotime($leadDateAdded)) : '',
     		time_ago($leadLastContact)
     	);
     }
    }

    $connect->close();
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode($output);
} catch (Throwable $e) {
    if (isset($connect) && $connect instanceof mysqli) {
        $connect->close();
    }
    if (ob_get_length()) {
        ob_clean();
    }
    error_log('fetchDatasource exception: ' . $e->getMessage());
    echo json_encode(array('data' => array(), 'error' => 'fetchDatasource exception: ' . $e->getMessage(), 'debug' => $e->getMessage()));
}
