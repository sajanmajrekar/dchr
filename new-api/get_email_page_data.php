<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../php_actions/db_connect.php';
require_once '../php_actions/core.php';
require_once 'email_helpers.php';

function emailPageOptions($connect, $table)
{
    $rows = array();
    $result = $connect->query("SELECT id, name FROM " . $table . " ORDER BY name ASC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = array('id' => (int) $row['id'], 'name' => $row['name']);
        }
        $result->free();
    }
    return $rows;
}

$logs = array();
$logResult = $connect->query("SELECT e.id, e.subject, e.senttime, e.totalemailsent, s.firstname, s.lastname
    FROM emaillogs e
    LEFT JOIN tblstaff s ON s.staffid = e.sentby
    ORDER BY e.senttime DESC
    LIMIT 50");
if ($logResult) {
    while ($row = $logResult->fetch_assoc()) {
        $logs[] = array(
            'id' => (int) $row['id'],
            'subject' => $row['subject'],
            'sent_time' => $row['senttime'],
            'sent_date' => !empty($row['senttime']) ? date('d M Y', strtotime($row['senttime'])) : '',
            'sent_by' => trim((string) $row['firstname'] . ' ' . (string) $row['lastname']),
            'total_sent' => (int) $row['totalemailsent']
        );
    }
    $logResult->free();
}

$templates = array(
    array('id' => 'seo', 'name' => 'JD for SEO', 'subject' => 'Opening for SEO role at DigiChefs', 'body' => "Hello,\n\nWe are looking for a candidate for an SEO role at DigiChefs. Please share your updated resume if interested.\n\nThanks,\nDigiChefs HR"),
    array('id' => 'social', 'name' => 'JD for Social Media', 'subject' => 'Opening for Social Media role at DigiChefs', 'body' => "Hello,\n\nWe are looking for a candidate for a Social Media role at DigiChefs. Please share your updated resume if interested.\n\nThanks,\nDigiChefs HR"),
    array('id' => 'hr', 'name' => 'JD for HR', 'subject' => 'Opening for HR role at DigiChefs', 'body' => "Hello,\n\nWe are looking for a candidate for an HR role at DigiChefs. Please share your updated resume if interested.\n\nThanks,\nDigiChefs HR")
);

echo json_encode(array(
    'success' => true,
    'data' => array(
        'roles' => emailPageOptions($connect, 'tblrole'),
        'statuses' => emailPageOptions($connect, 'tblleadsstatus'),
        'sources' => emailPageOptions($connect, 'tblleadssources'),
        'daily_sent' => emailApiDailySentCount($connect),
        'daily_limit' => 250,
        'logs' => $logs,
        'templates' => $templates
    )
), JSON_UNESCAPED_SLASHES);

$connect->close();
