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
        error_log('fetchByLastDays fatal: ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line']);
        sendJsonResponse(array(
            'draw' => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => array(),
            'error' => 'fetchByLastDays fatal: ' . $error['message']
        ));
    }
});

function buildIntervalWhereClause($interval)
{
    if ($interval === "last-seven") {
        return " WHERE tblleads.dateadded >= CURRENT_DATE - INTERVAL 7 DAY";
    }

    if ($interval === "last-thirty") {
        return " WHERE tblleads.dateadded >= CURRENT_DATE - INTERVAL 30 DAY";
    }

    if ($interval === "last-month") {
        return " WHERE tblleads.dateadded >= CURRENT_DATE - INTERVAL 3 MONTH";
    }

    return '';
}

function buildIntervalOrderBy()
{
    $columnMap = array(
        2 => 'tblleads.name',
        3 => 'tblleads.email',
        4 => 'tblleads.phonenumber',
        5 => 'tblleads.dateadded',
        7 => 'tblleadssources.name',
        8 => 'tblleadsstatus.name'
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
    $interval = isset($_POST["interval"]) ? $_POST["interval"] : '';

    if ($length < 1) {
        $length = 10;
    }

    $baseFrom = " FROM tblleads INNER JOIN tblleadssources ON tblleads.source = tblleadssources.id INNER JOIN tblleadsstatus ON tblleads.status = tblleadsstatus.id";
    $whereClause = buildIntervalWhereClause($interval);
    $orderClause = buildIntervalOrderBy();

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

    $sql = "SELECT tblleads.id, tblleads.name, tblleads.email, tblleads.phonenumber, tblleads.dateadded, tblleads.lastcontact, tblleadsstatus.name AS status_name, tblleadssources.name AS source_name" .
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
        $leadid = $row['id'];
        $button = '<center><a data-target="#editMyLeadModal" onclick="editMyLead(' . $leadid . ')" data-toggle="modal" title="Edit User" class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-pencil"></i></a>';
        $output['data'][] = array(
            '<center><label class="csscheckbox csscheckbox-primary"><input type="checkbox" class="checkmark" name="leadcheckbox" value="' . $leadid . '"><span></span></label></center>',
            $button,
            '<a data-target="#editMyLeadModal" onclick="editMyLead(' . $leadid . ')" href="#" data-toggle="modal" title="' . $row['name'] . '">' . $row['name'] . '</a>',
            $row['email'],
            $row['phonenumber'],
            !empty($row['dateadded']) ? date("d M, Y", strtotime($row['dateadded'])) : '',
            time_ago($row['lastcontact']),
            $row['source_name'],
            $row['status_name']
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
    error_log('fetchByLastDays exception: ' . $e->getMessage());
    sendJsonResponse(array(
        'draw' => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => array(),
        'error' => 'fetchByLastDays exception: ' . $e->getMessage(),
        'debug' => $e->getMessage()
    ));
}
