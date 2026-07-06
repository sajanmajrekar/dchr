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
            $experience = $connect->real_escape_string($_POST["experiance"]);
            $conditions[] = "tblleads.experiance LIKE '%" . $experience . "%'";
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
        $leadExperience = isset($row['experiance']) ? $row['experiance'] : '';
        $leadCurrentSalary = isset($row['csalary']) ? $row['csalary'] : '';
        $leadExpectedSalary = isset($row['esalary']) ? $row['esalary'] : '';
        $leadNoticePeriod = isset($row['nperiod']) ? $row['nperiod'] : '';
        $leadDateAdded = isset($row['dateadded']) ? $row['dateadded'] : '';
        $leadLastContact = isset($row['lastcontact']) ? $row['lastcontact'] : '';
        $leadStatusName = isset($row['lead_status_name']) ? $row['lead_status_name'] : '';
        $leadResume = isset($row['resume']) ? $row['resume'] : '';

        if (!empty($leadResume)) {
            $resumeUrl = 'view_resume.php?file=' . rawurlencode($leadResume);
            $button = '<center><a href="' . $resumeUrl . '" title="View Resume" target="_blank" class="btn btn-effect-ripple btn-xs btn-success"><i class="fa  fa-eye"></i></a><a data-target="#editMyLeadModal" onclick="editMyLead(' . $leadid . ')" data-toggle="modal" title="Edit User" class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-pencil"></i></a>';
        } else {
            $button = '<center><a data-target="#editMyLeadModal" onclick="editMyLead(' . $leadid . ')" data-toggle="modal" title="Edit User" class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-pencil"></i></a>';
        }

        $output['data'][] = array(
            '<center><label class="csscheckbox csscheckbox-primary"><input type="checkbox" class="checkmark" name="leadcheckbox" value="' . $leadid . '"><span></span></label></center>',
            $button,
            '<a data-target="#editMyLeadModal" onclick="editMyLead(' . $leadid . ')" href="#" data-toggle="modal" title="' . $leadName . '">' . $leadName . '</a>',
            $leadEmail,
            $leadPhone,
            $leadCity,
            $leadWillingToRelocate,
            getroletext($leadRoles),
            $leadExperience,
            $leadCurrentSalary,
            $leadExpectedSalary,
            $leadNoticePeriod,
            !empty($leadDateAdded) ? date("d M, Y", strtotime($leadDateAdded)) : '',
            time_ago($leadLastContact),
            $leadStatusName
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
