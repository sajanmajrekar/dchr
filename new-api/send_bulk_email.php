<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once '../php_actions/db_connect.php';
require_once '../php_actions/core.php';
require_once 'email_helpers.php';

$projectRoot = dirname(__DIR__);
@chdir($projectRoot . '/mail');
require_once $projectRoot . '/mail/lib.php';
@chdir($projectRoot);

$subject = isset($_POST['subject']) ? trim((string) $_POST['subject']) : '';
$body = isset($_POST['body']) ? trim((string) $_POST['body']) : '';
$filters = emailApiReadFilters($_POST);

if ($subject === '' || $body === '') {
    echo json_encode(array('success' => false, 'message' => 'Subject and message are required.'));
    exit;
}

$dailyLimit = 250;
$dailySent = emailApiDailySentCount($connect);
$remaining = max(0, $dailyLimit - $dailySent);
if ($remaining <= 0) {
    echo json_encode(array('success' => false, 'message' => 'Daily email limit already reached.', 'daily_sent' => $dailySent, 'daily_limit' => $dailyLimit));
    exit;
}

$sql = emailApiCandidateSql($connect, $filters, $remaining);
$result = $connect->query($sql);
$sentIds = array();
$sentCount = 0;
$failedCount = 0;
$mailBody = $body . '<br><br><b>Thanks!</b>';

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $email = trim((string) $row['email']);
        if ($email === '') {
            continue;
        }

        if (SendMailHTML($email, $subject, $mailBody, '', '')) {
            $sentCount++;
            $sentIds[] = (int) $row['id'];
        } else {
            $failedCount++;
        }
    }
    $result->free();
}

if ($sentCount > 0) {
    $date = date('Y-m-d H:i:s');
    $sentBy = isset($_SESSION['id']) ? (int) $_SESSION['id'] : 0;
    $sentIdString = implode(',', $sentIds);
    $stmt = $connect->prepare("INSERT INTO emaillogs (subject, mailcontent, mailsentto, senttime, sentby, totalemailsent) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param('ssssii', $subject, $mailBody, $sentIdString, $date, $sentBy, $sentCount);
        $stmt->execute();
        $stmt->close();
    }
}

$message = $sentCount . ' emails sent.';
if ($failedCount > 0) {
    $message .= ' ' . $failedCount . ' failed.';
}
if ($sentCount >= $remaining) {
    $message .= ' Daily limit reached.';
}

echo json_encode(array(
    'success' => $sentCount > 0,
    'message' => $sentCount > 0 ? $message : 'No emails were sent.',
    'sent_count' => $sentCount,
    'failed_count' => $failedCount,
    'daily_sent' => emailApiDailySentCount($connect),
    'daily_limit' => $dailyLimit
), JSON_UNESCAPED_SLASHES);

$connect->close();
