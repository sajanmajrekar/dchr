<?php
ob_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../php_actions/db_connect.php';
require_once '../php_actions/core.php';
require_once '../includes/resume_intelligence.php';

$roles = fetchResumeRoleOptions($connect);
$statuses = fetchResumeLeadStatusOptions($connect);
$sources = fetchResumeSourceOptions($connect);

while (ob_get_level() > 0) {
    @ob_end_clean();
}

echo json_encode(array(
    'success' => true,
    'data' => array(
        'roles' => $roles,
        'statuses' => $statuses,
        'sources' => $sources,
        'relocate_options' => array('Yes', 'No'),
        'date_intervals' => array(
            array('id' => '', 'name' => 'Please select'),
            array('id' => 'last-seven', 'name' => 'Last 7 days'),
            array('id' => 'last-thirty', 'name' => 'Last 30 days'),
            array('id' => 'last-month', 'name' => 'Last 3 months')
        )
    )
), JSON_UNESCAPED_SLASHES);

$connect->close();
