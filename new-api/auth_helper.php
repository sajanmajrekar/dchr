<?php

function apiEnsureReactSessionsTable($connect)
{
    if (!($connect instanceof mysqli)) {
        return false;
    }

    return (bool) $connect->query("CREATE TABLE IF NOT EXISTS hr_react_sessions (
        id int(11) NOT NULL AUTO_INCREMENT,
        staff_id int(11) NOT NULL,
        token_hash char(64) NOT NULL,
        expires_at datetime NOT NULL,
        created_at datetime NOT NULL DEFAULT current_timestamp(),
        last_seen_at datetime DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY uniq_hr_react_session_token (token_hash),
        KEY idx_hr_react_session_staff (staff_id),
        KEY idx_hr_react_session_expiry (expires_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function apiRandomToken()
{
    if (function_exists('random_bytes')) {
        return bin2hex(random_bytes(32));
    }

    return hash('sha256', uniqid('', true) . mt_rand() . microtime(true));
}

function apiIssueReactSession($connect, $staffId)
{
    $staffId = (int) $staffId;
    if ($staffId <= 0 || !apiEnsureReactSessionsTable($connect)) {
        return '';
    }

    $token = apiRandomToken();
    $tokenHash = hash('sha256', $token);
    $expiresAt = date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 30));

    $stmt = $connect->prepare("INSERT INTO hr_react_sessions (staff_id, token_hash, expires_at, last_seen_at) VALUES (?, ?, ?, NOW())");
    if (!$stmt) {
        return '';
    }

    $stmt->bind_param('iss', $staffId, $tokenHash, $expiresAt);
    $ok = $stmt->execute();
    $stmt->close();

    return $ok ? $token : '';
}

function apiRequestSessionToken()
{
    if (!empty($_SERVER['HTTP_X_HR_SESSION'])) {
        return trim((string) $_SERVER['HTTP_X_HR_SESSION']);
    }

    if (!empty($_SERVER['HTTP_AUTHORIZATION']) && preg_match('/Bearer\s+(.+)/i', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
        return trim((string) $matches[1]);
    }

    return '';
}

function apiCurrentStaffIdFromSession($connect)
{
    if (!apiEnsureReactSessionsTable($connect)) {
        return 0;
    }

    $token = apiRequestSessionToken();
    if ($token === '') {
        return 0;
    }

    $tokenHash = hash('sha256', $token);
    $stmt = $connect->prepare("SELECT staff_id FROM hr_react_sessions WHERE token_hash = ? AND expires_at > NOW() LIMIT 1");
    if (!$stmt) {
        return 0;
    }

    $stmt->bind_param('s', $tokenHash);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if (!$row) {
        return 0;
    }

    $connect->query("UPDATE hr_react_sessions SET last_seen_at = NOW() WHERE token_hash = '" . $connect->real_escape_string($tokenHash) . "'");
    return (int) $row['staff_id'];
}

function apiRequireStaffId($connect)
{
    $staffId = apiCurrentStaffIdFromSession($connect);
    if ($staffId <= 0) {
        http_response_code(401);
        echo json_encode(array('success' => false, 'message' => 'Unauthorized'));
        exit;
    }

    return $staffId;
}
