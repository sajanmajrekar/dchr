<?php

ob_start();
ini_set('display_errors', '0');
error_reporting(E_ALL);
header('Content-Type: application/json');
mysqli_report(MYSQLI_REPORT_OFF);

function sendJsonResponse($payload)
{
    $json = json_encode($payload, JSON_INVALID_UTF8_SUBSTITUTE);

    if ($json === false) {
        echo json_encode(array(
            'draw' => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => array(),
            'error' => 'JSON encoding failed: ' . json_last_error_msg()
        ));
        return;
    }

    echo $json;
}

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
        if (ob_get_length()) {
            ob_clean();
        }
        error_log('fetchMyLeads fatal: ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line']);
        sendJsonResponse(array(
            'draw' => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => array(),
            'error' => 'fetchMyLeads fatal: ' . $error['message']
        ));
    }
});

function buildAnnualSalarySql($columnName)
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

function normalizeSalaryFilterThreshold($value)
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

function buildNormalizedExperienceSql($columnName)
{
    $columnName = trim((string) $columnName);
    $cleanValue = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(COALESCE(" . $columnName . ", ''))), 'years', ''), 'year', ''), 'yrs', ''), 'yr', ''), ' ', '')";

    return "(CASE
        WHEN TRIM(LOWER(COALESCE(" . $columnName . ", ''))) IN ('', 'na', 'n/a', 'none', 'null', '-') THEN NULL
        WHEN " . $cleanValue . " REGEXP '^[0-9]+(\\\\.[0-9]+)?$' THEN
            CASE
                WHEN " . $cleanValue . " LIKE '%.%' THEN CAST(" . $cleanValue . " AS DECIMAL(12,2))
                WHEN CAST(" . $cleanValue . " AS UNSIGNED) >= 10 THEN CAST(" . $cleanValue . " AS DECIMAL(12,2)) / 12
                WHEN CAST(" . $cleanValue . " AS UNSIGNED) >= 8 THEN CAST(" . $cleanValue . " AS DECIMAL(12,2)) / 12
                ELSE CAST(" . $cleanValue . " AS DECIMAL(12,2))
            END
        ELSE NULL
    END)";
}

function normalizeExperienceFilterThreshold($value)
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

function normalizeExperienceValueToYears($value)
{
    $value = strtolower(trim((string) $value));
    if ($value === '' || in_array($value, array('na', 'n/a', 'none', 'null', '-'))) {
        return null;
    }

    $value = str_replace(array('years', 'year', 'yrs', 'yr', ' '), '', $value);
    if (!preg_match('/^\d+(?:\.\d+)?$/', $value)) {
        return null;
    }

    $amount = (float) $value;
    if (strpos($value, '.') !== false) {
        return $amount;
    }
    if ($amount >= 10 || $amount >= 8) {
        return $amount / 12;
    }

    return $amount;
}

function formatNormalizedExperienceDisplay($value)
{
    $years = normalizeExperienceValueToYears($value);
    if ($years === null) {
        return trim((string) $value) !== '' ? trim((string) $value) : 'Not available';
    }

    $formatted = rtrim(rtrim(number_format($years, 1, '.', ''), '0'), '.');
    return $formatted;
}

function fetchMyLeadsEsc($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function fetchMyLeadsResumeIsAvailable($resumeName)
{
    $resumeName = basename(str_replace('\\', '/', (string) $resumeName));
    if ($resumeName === '' || $resumeName === '.' || $resumeName === '..') {
        return false;
    }

    $root = dirname(__DIR__);
    $directories = array(
        $root . DIRECTORY_SEPARATOR . 'resume',
        $root . DIRECTORY_SEPARATOR . 'Resume',
        $root . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'resume',
        $root . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'resumes'
    );

    foreach ($directories as $directory) {
        $path = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $resumeName;
        if (is_file($path) && is_readable($path) && (int) @filesize($path) > 0) {
            return true;
        }
    }

    return false;
}

function buildLeadWhereClause($connect)
{
    $conditions = array();
    $isDateSearch = isset($_POST["is_date_search"]) && $_POST["is_date_search"] === "yes";

    if ($isDateSearch) {
        if (!empty($_POST["roles"])) {
            $role = $connect->real_escape_string($_POST["roles"]);
            $conditions[] = "tblleads.roles LIKE '%" . $role . "%'";
        }

        if (!empty($_POST["leadsource"])) {
            $leadsource = $connect->real_escape_string($_POST["leadsource"]);
            $conditions[] = "tblleads.source = '" . $leadsource . "'";
        }

        if (!empty($_POST["leadstatus"])) {
            $leadstatus = (int) $_POST["leadstatus"];
            $conditions[] = "tblleads.status = " . $leadstatus;
        }

        if (!empty($_POST["experiance"])) {
            $experience = normalizeExperienceFilterThreshold($_POST["experiance"]);
            if ($experience !== null) {
                $conditions[] = buildNormalizedExperienceSql('tblleads.experiance') . " >= " . (float) $experience;
            }
        }
    }

    if (!empty($_POST["city"])) {
        $city = $connect->real_escape_string(trim((string) $_POST["city"]));
        $conditions[] = "tblleads.city LIKE '%" . $city . "%'";
    }

    if (!empty($_POST["relocate"])) {
        $relocate = $connect->real_escape_string(trim((string) $_POST["relocate"]));
        $conditions[] = "tblleads.willing_to_relocate = '" . $relocate . "'";
    }

    if (!empty($_POST["currentctc"])) {
        $currentCtc = normalizeSalaryFilterThreshold($_POST["currentctc"]);
        if ($currentCtc !== null) {
            $conditions[] = buildAnnualSalarySql('tblleads.csalary') . " <= " . (float) $currentCtc;
        }
    }

    if (!empty($_POST["expectedctc"])) {
        $expectedCtc = normalizeSalaryFilterThreshold($_POST["expectedctc"]);
        if ($expectedCtc !== null) {
            $conditions[] = buildAnnualSalarySql('tblleads.esalary') . " <= " . (float) $expectedCtc;
        }
    }

    if (!empty($_POST["noticeperiod"])) {
        $noticePeriod = $connect->real_escape_string(trim((string) $_POST["noticeperiod"]));
        $conditions[] = "tblleads.nperiod LIKE '%" . $noticePeriod . "%'";
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

    if (isset($_POST['search']['value']) && trim($_POST['search']['value']) !== '') {
        $search = $connect->real_escape_string(trim($_POST['search']['value']));
        $conditions[] = "(tblleads.name LIKE '%" . $search . "%' OR tblleads.email LIKE '%" . $search . "%' OR tblleads.phonenumber LIKE '%" . $search . "%' OR tblleads.city LIKE '%" . $search . "%')";
    }

    if (empty($conditions)) {
        return '';
    }

    return ' WHERE ' . implode(' AND ', $conditions);
}

function buildLeadOrderBy()
{
    $columnMap = array(
        2 => 'tblleads.name',
        3 => 'tblleads.email',
        4 => 'tblleads.phonenumber',
        5 => 'tblleads.city',
        6 => 'tblleads.willing_to_relocate',
        8 => 'tblleads.experiance',
        9 => 'tblleads.csalary',
        10 => 'tblleads.esalary',
        11 => 'tblleads.nperiod',
        12 => 'tblleads.dateadded',
        14 => 'tblleadsstatus.name'
    );

    if (!isset($_POST['order'][0]['column'])) {
        return ' ORDER BY tblleads.dateadded DESC';
    }

    $columnIndex = (int) $_POST['order'][0]['column'];
    $direction = isset($_POST['order'][0]['dir']) && strtolower($_POST['order'][0]['dir']) === 'asc' ? 'ASC' : 'DESC';

    if (!isset($columnMap[$columnIndex])) {
        return ' ORDER BY tblleads.dateadded DESC';
    }

    return ' ORDER BY ' . $columnMap[$columnIndex] . ' ' . $direction;
}

try {
    require_once 'core.php';
    if (!($connect instanceof mysqli)) {
        throw new Exception('Database connection failed.');
    }

    $draw = isset($_POST['draw']) ? (int) $_POST['draw'] : 0;
    $start = isset($_POST['start']) ? max(0, (int) $_POST['start']) : 0;
    $length = isset($_POST['length']) ? (int) $_POST['length'] : 10;

    if ($length < 1) {
        $length = 10;
    }

    $baseFrom = " FROM tblleads INNER JOIN tblleadssources ON tblleads.source = tblleadssources.id INNER JOIN tblleadsstatus ON tblleads.status = tblleadsstatus.id";
    $whereClause = buildLeadWhereClause($connect);
    $orderClause = buildLeadOrderBy();

    $totalResult = $connect->query("SELECT COUNT(*) AS total FROM tblleads");
    if ($totalResult === false) {
        throw new Exception('Total count query failed: ' . $connect->error);
    }
    $totalRow = $totalResult->fetch_assoc();
    $recordsTotal = isset($totalRow['total']) ? (int) $totalRow['total'] : 0;
    $totalResult->close();

    $filteredResult = $connect->query("SELECT COUNT(*) AS total" . $baseFrom . $whereClause);
    if ($filteredResult === false) {
        throw new Exception('Filtered count query failed: ' . $connect->error);
    }
    $filteredRow = $filteredResult->fetch_assoc();
    $recordsFiltered = isset($filteredRow['total']) ? (int) $filteredRow['total'] : 0;
    $filteredResult->close();

    $sql = "SELECT tblleads.*, tblleadsstatus.name AS lead_status_name, tblleadssources.name AS lead_source_name" .
        $baseFrom .
        $whereClause .
        $orderClause .
        " LIMIT " . $start . ", " . $length;

    $result = $connect->query($sql);
    if ($result === false) {
        throw new Exception('Database query failed: ' . $connect->error . ' | SQL: ' . $sql);
    }

    $output = array(
        'draw' => $draw,
        'recordsTotal' => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data' => array()
    );

    while ($row = $result->fetch_assoc()) {
        $leadid = isset($row['id']) ? $row['id'] : '';
        $leadName = isset($row['name']) ? $row['name'] : '';
        $leadEmail = isset($row['email']) ? $row['email'] : '';
        $leadPhone = isset($row['phonenumber']) ? $row['phonenumber'] : '';
        $leadCity = isset($row['city']) ? $row['city'] : '';
        $leadWillingToRelocate = isset($row['willing_to_relocate']) ? $row['willing_to_relocate'] : '';
        $leadRoles = isset($row['roles']) ? $row['roles'] : '';
        $leadExperience = formatNormalizedExperienceDisplay(isset($row['experiance']) ? $row['experiance'] : '');
        $leadCurrentSalary = isset($row['csalary']) ? $row['csalary'] : '';
        $leadExpectedSalary = isset($row['esalary']) ? $row['esalary'] : '';
        $leadNoticePeriod = isset($row['nperiod']) ? $row['nperiod'] : '';
        $leadDateAdded = isset($row['dateadded']) ? $row['dateadded'] : '';
        $leadLastContact = isset($row['lastcontact']) ? $row['lastcontact'] : '';
        $leadStatusName = isset($row['lead_status_name']) ? $row['lead_status_name'] : '';
        $leadResume = isset($row['resume']) ? $row['resume'] : '';
        $leadAdditionalInfo = isset($row['ainfo']) ? $row['ainfo'] : '';
        $isCareersImport = stripos((string) $leadAdditionalInfo, 'Imported from careers email.') !== false;

        if (!empty($leadResume) && fetchMyLeadsResumeIsAvailable($leadResume)) {
            $resumeUrl = 'view_resume.php?file=' . rawurlencode($leadResume);
            $button = '<center><a href="' . $resumeUrl . '" title="View Resume" target="_blank" class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-eye"></i></a><a data-target="#editMyLeadModal" onclick="editMyLead(' . $leadid . ')" data-toggle="modal" title="Edit User" class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-pencil"></i></a></center>';
        } else {
            $button = '<center><span title="Resume not available" class="btn btn-effect-ripple btn-xs btn-default disabled resume-unavailable-action"><i class="fa fa-eye-slash"></i> Resume not available</span><a data-target="#editMyLeadModal" onclick="editMyLead(' . $leadid . ')" data-toggle="modal" title="Edit User" class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-pencil"></i></a></center>';
        }

        $nameCell = '<a data-target="#editMyLeadModal" onclick="editMyLead(' . $leadid . ')" href="#" data-toggle="modal" title="' . fetchMyLeadsEsc($leadName) . '">' . fetchMyLeadsEsc($leadName) . '</a>';
        if ($isCareersImport) {
            $nameCell .= '<div><span class="label label-info careers-import-pill">Careers Import</span> <small class="text-muted">resume-only email</small></div>';
        }

        $output['data'][] = array(
            '<center><label class="csscheckbox csscheckbox-primary"><input type="checkbox" class="checkmark" name="leadcheckbox" value="' . $leadid . '"><span></span></label></center>',
            $button,
            $nameCell,
            fetchMyLeadsEsc($leadEmail),
            fetchMyLeadsEsc($leadPhone),
            fetchMyLeadsEsc($leadCity),
            fetchMyLeadsEsc($leadWillingToRelocate),
            getroletext($leadRoles),
            fetchMyLeadsEsc($leadExperience),
            fetchMyLeadsEsc($leadCurrentSalary),
            fetchMyLeadsEsc($leadExpectedSalary),
            fetchMyLeadsEsc($leadNoticePeriod),
            !empty($leadDateAdded) ? date("d M, Y", strtotime($leadDateAdded)) : '',
            time_ago($leadLastContact),
            fetchMyLeadsEsc($leadStatusName)
        );
    }

    $result->close();
    $connect->close();

    if (ob_get_length()) {
        ob_clean();
    }

    sendJsonResponse($output);
} catch (Throwable $e) {
    if (isset($connect) && $connect instanceof mysqli) {
        $connect->close();
    }
    if (ob_get_length()) {
        ob_clean();
    }
    error_log('fetchMyLeads exception: ' . $e->getMessage());
    sendJsonResponse(array(
        'draw' => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => array(),
        'error' => 'fetchMyLeads exception: ' . $e->getMessage(),
        'debug' => $e->getMessage()
    ));
}
