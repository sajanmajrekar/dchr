<?php

function emailApiLeadColumnExists($connect, $columnName)
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

function emailApiAnnualSalarySql($columnName)
{
    $cleanValue = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(COALESCE(" . $columnName . ", ''))), ',', ''), ' ', ''), 'rs.', ''), 'rs', ''), 'lakhs', ''), 'lakh', ''), 'lacs', ''), 'lac', ''), 'lpa', ''), 'lps', ''), 'l', '')";

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

function emailApiNormalizeSalaryThreshold($value)
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

function emailApiNormalizedExperienceSql($columnName)
{
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

function emailApiNormalizeExperienceThreshold($value)
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

function emailApiBuildCandidateWhere($connect, $filters)
{
    $conditions = array("tblleads.email IS NOT NULL", "TRIM(tblleads.email) <> ''");

    if (!empty($filters['role']) && emailApiLeadColumnExists($connect, 'roles')) {
        $conditions[] = "FIND_IN_SET('" . $connect->real_escape_string($filters['role']) . "', tblleads.roles)";
    }

    if (!empty($filters['experience']) && emailApiLeadColumnExists($connect, 'experiance')) {
        $experience = emailApiNormalizeExperienceThreshold($filters['experience']);
        if ($experience !== null) {
            $conditions[] = emailApiNormalizedExperienceSql('tblleads.experiance') . " >= " . (float) $experience;
        }
    }

    if (!empty($filters['notice_period']) && emailApiLeadColumnExists($connect, 'nperiod')) {
        $conditions[] = "tblleads.nperiod <= '" . $connect->real_escape_string($filters['notice_period']) . "'";
    }

    if (!empty($filters['status']) && emailApiLeadColumnExists($connect, 'status')) {
        $conditions[] = "tblleads.status = '" . $connect->real_escape_string($filters['status']) . "'";
    }

    if (!empty($filters['source']) && emailApiLeadColumnExists($connect, 'source')) {
        $conditions[] = "tblleads.source = '" . $connect->real_escape_string($filters['source']) . "'";
    }

    if (!empty($filters['city']) && emailApiLeadColumnExists($connect, 'city')) {
        $conditions[] = "tblleads.city LIKE '%" . $connect->real_escape_string($filters['city']) . "%'";
    }

    if (!empty($filters['relocate']) && emailApiLeadColumnExists($connect, 'willing_to_relocate')) {
        $conditions[] = "tblleads.willing_to_relocate = '" . $connect->real_escape_string($filters['relocate']) . "'";
    }

    if (!empty($filters['current_ctc']) && emailApiLeadColumnExists($connect, 'csalary')) {
        $salary = emailApiNormalizeSalaryThreshold($filters['current_ctc']);
        if ($salary !== null) {
            $conditions[] = emailApiAnnualSalarySql('tblleads.csalary') . " <= " . (float) $salary;
        }
    }

    if (!empty($filters['expected_ctc']) && emailApiLeadColumnExists($connect, 'esalary')) {
        $salary = emailApiNormalizeSalaryThreshold($filters['expected_ctc']);
        if ($salary !== null) {
            $conditions[] = emailApiAnnualSalarySql('tblleads.esalary') . " <= " . (float) $salary;
        }
    }

    if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
        $conditions[] = "DATE(tblleads.dateadded) BETWEEN '" . $connect->real_escape_string($filters['start_date']) . "' AND '" . $connect->real_escape_string($filters['end_date']) . "'";
    }

    if (!empty($filters['interval'])) {
        if ($filters['interval'] === 'last-seven') {
            $conditions[] = "DATE(tblleads.dateadded) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        } elseif ($filters['interval'] === 'last-thirty') {
            $conditions[] = "DATE(tblleads.dateadded) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($filters['interval'] === 'last-month') {
            $conditions[] = "DATE(tblleads.dateadded) >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
        }
    }

    return implode(' AND ', $conditions);
}

function emailApiCandidateSql($connect, $filters, $limit = 1000)
{
    $where = emailApiBuildCandidateWhere($connect, $filters);
    $limit = max(1, min(1000, (int) $limit));

    return "SELECT tblleads.id, tblleads.name, tblleads.email, tblleads.phonenumber, tblleads.roles, tblleads.experiance, tblleads.nperiod, tblleads.dateadded, tblleads.lastcontact, tblleads.city, tblleads.csalary, tblleads.esalary, tblleadsstatus.name AS status_name, tblleadssources.name AS source_name
        FROM tblleads
        INNER JOIN tblleadssources ON tblleads.source = tblleadssources.id
        INNER JOIN tblleadsstatus ON tblleads.status = tblleadsstatus.id
        WHERE " . $where . "
        ORDER BY tblleads.dateadded DESC
        LIMIT " . $limit;
}

function emailApiReadFilters($source)
{
    return array(
        'role' => isset($source['role']) ? trim((string) $source['role']) : '',
        'experience' => isset($source['experience']) ? trim((string) $source['experience']) : '',
        'notice_period' => isset($source['notice_period']) ? trim((string) $source['notice_period']) : '',
        'status' => isset($source['status']) ? trim((string) $source['status']) : '',
        'source' => isset($source['source']) ? trim((string) $source['source']) : '',
        'city' => isset($source['city']) ? trim((string) $source['city']) : '',
        'relocate' => isset($source['relocate']) ? trim((string) $source['relocate']) : '',
        'current_ctc' => isset($source['current_ctc']) ? trim((string) $source['current_ctc']) : '',
        'expected_ctc' => isset($source['expected_ctc']) ? trim((string) $source['expected_ctc']) : '',
        'start_date' => isset($source['start_date']) ? trim((string) $source['start_date']) : '',
        'end_date' => isset($source['end_date']) ? trim((string) $source['end_date']) : '',
        'interval' => isset($source['interval']) ? trim((string) $source['interval']) : ''
    );
}

function emailApiDailySentCount($connect)
{
    $result = $connect->query("SELECT COALESCE(SUM(totalemailsent), 0) AS total FROM emaillogs WHERE DATE(senttime) = CURDATE()");
    if (!$result) {
        return 0;
    }
    $row = $result->fetch_assoc();
    $result->free();
    return isset($row['total']) ? (int) $row['total'] : 0;
}

function emailApiMapCandidate($row)
{
    return array(
        'id' => (int) $row['id'],
        'name' => $row['name'],
        'email' => $row['email'],
        'phone' => $row['phonenumber'],
        'roles' => function_exists('getroletext') ? getroletext($row['roles']) : $row['roles'],
        'experience' => $row['experiance'],
        'notice_period' => $row['nperiod'],
        'city' => $row['city'],
        'current_ctc' => isset($row['csalary']) ? $row['csalary'] : '',
        'expected_ctc' => isset($row['esalary']) ? $row['esalary'] : '',
        'status' => isset($row['status_name']) ? $row['status_name'] : '',
        'source' => isset($row['source_name']) ? $row['source_name'] : '',
        'date_added' => !empty($row['dateadded']) ? date('d M Y', strtotime($row['dateadded'])) : '',
        'last_contact' => function_exists('time_ago') ? time_ago($row['lastcontact']) : $row['lastcontact']
    );
}
