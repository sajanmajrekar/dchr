<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../php_actions/db_connect.php';
require_once '../php_actions/core.php';

function apiCandidateClean($value)
{
    return trim((string) $value);
}

function apiCandidateSalarySql($column)
{
    $cleanValue = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(COALESCE(" . $column . ", ''))), ',', ''), ' ', ''), 'rs.', ''), 'rs', ''), 'lakhs', ''), 'lakh', ''), 'lacs', ''), 'lac', ''), 'lpa', ''), 'lps', ''), 'l', '')";

    return "(CASE
        WHEN TRIM(LOWER(COALESCE(" . $column . ", ''))) IN ('', 'na', 'n/a', 'none', 'null', '-') THEN NULL
        WHEN " . $cleanValue . " REGEXP '^[0-9]+(\\\\.[0-9]+)?$' THEN
            CASE
                WHEN CAST(" . $cleanValue . " AS DECIMAL(12,2)) < 100 THEN CAST(" . $cleanValue . " AS DECIMAL(12,2)) * 100000
                WHEN CAST(" . $cleanValue . " AS DECIMAL(12,2)) < 100000 THEN CAST(" . $cleanValue . " AS DECIMAL(12,2)) * 12
                ELSE CAST(" . $cleanValue . " AS DECIMAL(12,2))
            END
        ELSE NULL
    END)";
}

function apiCandidateNormalizeSalaryThreshold($value)
{
    $value = strtolower(trim((string) $value));
    if ($value === '') {
        return null;
    }

    $value = str_replace(array(',', ' ', 'rs.', 'rs'), '', $value);
    if (preg_match('/^(\d+(?:\.\d+)?)(l|lp|lac|lacs|lakh|lakhs|lpa|lps)$/', $value, $matches)) {
        return (float) $matches[1] * 100000;
    }
    if (preg_match('/^(\d+(?:\.\d+)?)(k)$/', $value, $matches)) {
        return (float) $matches[1] * 1000;
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

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;
$limit = max(10, min(100, $limit));
$offset = ($page - 1) * $limit;

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
$origin = ($isHttps ? 'https://' : 'http://') . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
$scriptDir = str_replace('\\', '/', dirname(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/hr/new-api/get_candidates.php'));
$appBasePath = rtrim(dirname($scriptDir), '/');
if ($appBasePath === '' || $appBasePath === '.') {
    $appBasePath = '';
}

$where = array('1=1');

$search = isset($_GET['search']) ? apiCandidateClean($_GET['search']) : '';
if ($search !== '') {
    $escaped = $connect->real_escape_string($search);
    $like = "'%" . $escaped . "%'";
    $where[] = "(l.name LIKE $like OR l.email LIKE $like OR l.phonenumber LIKE $like OR l.city LIKE $like OR l.skillset LIKE $like OR l.resume LIKE $like)";
}

$role = isset($_GET['role']) ? (int) $_GET['role'] : 0;
if ($role > 0) {
    $where[] = "FIND_IN_SET('" . $role . "', COALESCE(l.roles, ''))";
}

$status = isset($_GET['status']) ? (int) $_GET['status'] : 0;
if ($status > 0) {
    $where[] = "l.status = " . $status;
}

$city = isset($_GET['city']) ? apiCandidateClean($_GET['city']) : '';
if ($city !== '') {
    $where[] = "l.city LIKE '%" . $connect->real_escape_string($city) . "%'";
}

$dateFrom = isset($_GET['date_from']) ? apiCandidateClean($_GET['date_from']) : '';
if ($dateFrom !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
    $where[] = "DATE(l.dateadded) >= '" . $connect->real_escape_string($dateFrom) . "'";
}

$dateTo = isset($_GET['date_to']) ? apiCandidateClean($_GET['date_to']) : '';
if ($dateTo !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
    $where[] = "DATE(l.dateadded) <= '" . $connect->real_escape_string($dateTo) . "'";
}

$currentCtc = isset($_GET['current_ctc']) ? apiCandidateClean($_GET['current_ctc']) : '';
if ($currentCtc !== '') {
    $threshold = apiCandidateNormalizeSalaryThreshold($currentCtc);
    if ($threshold !== null) {
        $where[] = apiCandidateSalarySql('l.csalary') . " <= " . (float) $threshold;
    }
}

$expectedCtc = isset($_GET['expected_ctc']) ? apiCandidateClean($_GET['expected_ctc']) : '';
if ($expectedCtc !== '') {
    $threshold = apiCandidateNormalizeSalaryThreshold($expectedCtc);
    if ($threshold !== null) {
        $where[] = apiCandidateSalarySql('l.esalary') . " <= " . (float) $threshold;
    }
}

$whereSql = implode(' AND ', $where);

$countResult = $connect->query("SELECT COUNT(*) AS total FROM tblleads l WHERE " . $whereSql);
$total = 0;
if ($countResult) {
    $countRow = $countResult->fetch_assoc();
    $total = isset($countRow['total']) ? (int) $countRow['total'] : 0;
    $countResult->free();
}

$sql = "SELECT
            l.*,
            s.name AS status_name,
            src.name AS source_name,
            (
                SELECT TRIM(CONCAT(COALESCE(st.firstname, ''), ' ', COALESCE(st.lastname, '')))
                FROM tblleadactivitylog al
                LEFT JOIN tblstaff st ON st.staffid = al.staffid
                WHERE al.leadid = l.id
                  AND al.staffid IS NOT NULL
                  AND al.staffid <> 0
                  AND TRIM(COALESCE(al.additional_data, '')) <> ''
                  AND al.additional_data NOT LIKE 'VIEW_LOG::%'
                ORDER BY al.date DESC
                LIMIT 1
            ) AS latest_hr_name,
            (
                SELECT st.profile_image
                FROM tblleadactivitylog al
                LEFT JOIN tblstaff st ON st.staffid = al.staffid
                WHERE al.leadid = l.id
                  AND al.staffid IS NOT NULL
                  AND al.staffid <> 0
                  AND TRIM(COALESCE(al.additional_data, '')) <> ''
                  AND al.additional_data NOT LIKE 'VIEW_LOG::%'
                ORDER BY al.date DESC
                LIMIT 1
            ) AS latest_hr_profile_image,
            (
                SELECT al.additional_data
                FROM tblleadactivitylog al
                WHERE al.leadid = l.id
                  AND al.staffid IS NOT NULL
                  AND al.staffid <> 0
                  AND TRIM(COALESCE(al.additional_data, '')) <> ''
                  AND al.additional_data NOT LIKE 'VIEW_LOG::%'
                ORDER BY al.date DESC
                LIMIT 1
            ) AS latest_hr_comment,
            (
                SELECT al.date
                FROM tblleadactivitylog al
                WHERE al.leadid = l.id
                  AND al.staffid IS NOT NULL
                  AND al.staffid <> 0
                  AND TRIM(COALESCE(al.additional_data, '')) <> ''
                  AND al.additional_data NOT LIKE 'VIEW_LOG::%'
                ORDER BY al.date DESC
                LIMIT 1
            ) AS latest_hr_comment_date
        FROM tblleads l
        LEFT JOIN tblleadsstatus s ON l.status = s.id
        LEFT JOIN tblleadssources src ON l.source = src.id
        WHERE " . $whereSql . "
        ORDER BY l.dateadded DESC
        LIMIT " . $offset . ", " . $limit;

$result = $connect->query($sql);
$candidates = array();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $mappedRoles = function_exists('getroletext') ? trim((string) getroletext($row['roles'])) : (string) $row['roles'];
        $latestHrProfileImage = trim((string) $row['latest_hr_profile_image']);
        $latestHrProfileImageUrl = '';
        if ($latestHrProfileImage !== '') {
            if (preg_match('/^https?:\/\//i', $latestHrProfileImage)) {
                $latestHrProfileImageUrl = $latestHrProfileImage;
            } else {
                $latestHrProfileImageUrl = $origin . $appBasePath . '/' . ltrim(str_replace('\\', '/', $latestHrProfileImage), '/');
            }
        }

        $candidates[] = array(
            'id' => (int) $row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phonenumber'],
            'title' => $row['title'],
            'company' => $row['company'],
            'description' => $row['description'],
            'country' => $row['country'],
            'pincode' => $row['zip'],
            'zip' => $row['zip'],
            'city' => $row['city'],
            'state' => $row['state'],
            'street' => $row['street'],
            'relocate' => $row['willing_to_relocate'],
            'roles' => $mappedRoles,
            'role_ids' => $row['roles'],
            'experience' => $row['experiance'],
            'qualification' => $row['qualification'],
            'current_job_title' => $row['cjtitle'],
            'current_employer' => $row['cemployer'],
            'current_ctc' => $row['csalary'],
            'expected_ctc' => $row['esalary'],
            'skillset' => $row['skillset'],
            'additional_info' => $row['ainfo'],
            'is_careers_import' => stripos((string) $row['ainfo'], 'Imported from careers email.') !== false,
            'referral' => $row['referral'],
            'notice_period' => $row['nperiod'],
            'date_added' => $row['dateadded'],
            'last_contact' => $row['lastcontact'],
            'status' => $row['status_name'],
            'status_id' => (int) $row['status'],
            'source' => $row['source_name'],
            'source_id' => (int) $row['source'],
            'resume' => $row['resume'],
            'portfolio' => $row['portfolio'],
            'followup' => $row['followup'],
            'latest_hr_name' => trim((string) $row['latest_hr_name']),
            'latest_hr_profile_image' => $latestHrProfileImage,
            'latest_hr_profile_image_url' => $latestHrProfileImageUrl,
            'latest_hr_comment' => trim((string) $row['latest_hr_comment']),
            'latest_hr_comment_date' => $row['latest_hr_comment_date']
        );
    }
    $result->free();
}

echo json_encode(array(
    'success' => true,
    'data' => $candidates,
    'pagination' => array(
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'pages' => max(1, (int) ceil($total / $limit))
    )
), JSON_UNESCAPED_SLASHES);

$connect->close();
