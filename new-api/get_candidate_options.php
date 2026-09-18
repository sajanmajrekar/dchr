<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../php_actions/db_connect.php';

$statuses = array();
$statusResult = $connect->query("SELECT id, name FROM tblleadsstatus ORDER BY name ASC");
if ($statusResult) {
    while ($row = $statusResult->fetch_assoc()) {
        $statuses[] = array(
            'id' => (int) $row['id'],
            'name' => $row['name']
        );
    }
    $statusResult->free();
}

$roles = array();
$roleIds = array();
$roleResult = $connect->query("SELECT id, name FROM tblrole ORDER BY name ASC");
if ($roleResult) {
    while ($row = $roleResult->fetch_assoc()) {
		$roleId = (int) $row['id'];
        $roles[] = array(
			'id' => $roleId,
            'name' => $row['name']
        );
		$roleIds[$roleId] = true;
    }
    $roleResult->free();
}

$requiredRoles = array(
	21 => 'Social Media Videographer',
	22 => 'Social Media Creator'
);
foreach ($requiredRoles as $roleId => $roleName) {
	if (!isset($roleIds[$roleId])) {
		$roles[] = array('id' => $roleId, 'name' => $roleName);
	}
}

usort($roles, function ($left, $right) {
	return strcasecmp($left['name'], $right['name']);
});

echo json_encode(array(
    'success' => true,
    'data' => array(
        'statuses' => $statuses,
        'roles' => $roles
    )
));

$connect->close();
