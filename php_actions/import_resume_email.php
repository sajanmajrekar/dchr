<?php

ob_start();
@ini_set('display_errors', '0');
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once 'core.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'resume_intelligence.php';

$localConfigPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'local_config.php';
if (is_file($localConfigPath)) {
    include $localConfigPath;
}

function careersImportJson($payload)
{
    while (ob_get_level() > 0) {
        @ob_end_clean();
    }

    echo resumeJsonEncode($payload, JSON_UNESCAPED_SLASHES);
    exit();
}

function careersImportLog($message, $context = array())
{
    resumeIntelligenceLog('careers_email_import', $message, $context);
}

function careersImportHeaderValue($name)
{
    $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
    return isset($_SERVER[$key]) ? trim((string) $_SERVER[$key]) : '';
}

function careersImportProvidedToken($payload)
{
    $authorization = careersImportHeaderValue('Authorization');
    if (stripos($authorization, 'Bearer ') === 0) {
        return trim(substr($authorization, 7));
    }

    if (isset($payload['token'])) {
        return trim((string) $payload['token']);
    }

    return '';
}

function careersImportCleanText($value)
{
    $value = trim((string) $value);
    $value = preg_replace('/\s+/', ' ', $value);
    return trim((string) $value);
}

function careersImportExtractEmail($value)
{
    if (preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', (string) $value, $matches)) {
        return strtolower($matches[0]);
    }

    return '';
}

function careersImportExtractPhone($value)
{
    if (preg_match('/(?:\+?\d[\d\-\s()]{8,}\d)/', (string) $value, $matches)) {
        return careersImportCleanText($matches[0]);
    }

    return '';
}

function careersImportNormalizeName($fromName, $email)
{
    $name = careersImportCleanText($fromName);
    $name = preg_replace('/[<>"]+/', '', (string) $name);
    if ($name !== '' && stripos($name, '@') === false) {
        return $name;
    }

    $localPart = preg_replace('/@.*$/', '', (string) $email);
    $localPart = preg_replace('/[^a-zA-Z0-9]+/', ' ', (string) $localPart);
    $localPart = trim((string) $localPart);

    return $localPart !== '' ? ucwords(strtolower($localPart)) : 'Careers Applicant';
}

function careersImportSafeExtension($filename)
{
    $extension = strtolower(pathinfo((string) $filename, PATHINFO_EXTENSION));
    $allowed = array('pdf', 'doc', 'docx', 'rtf', 'txt');

    return in_array($extension, $allowed, true) ? $extension : '';
}

function careersImportSaveAttachment($attachment)
{
    $filename = isset($attachment['filename']) ? (string) $attachment['filename'] : '';
    $extension = careersImportSafeExtension($filename);
    if ($extension === '') {
        return array('ok' => false, 'message' => 'Unsupported attachment type: ' . $filename);
    }

    $data = isset($attachment['data_base64']) ? (string) $attachment['data_base64'] : '';
    $data = preg_replace('/^data:[^;]+;base64,/', '', $data);
    $data = str_replace(array("\r", "\n", ' '), '', (string) $data);
    $bytes = base64_decode($data, true);
    if ($bytes === false || strlen($bytes) < 1) {
        return array('ok' => false, 'message' => 'Attachment data could not be decoded: ' . $filename);
    }

    $resumeDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resume';
    if (!is_dir($resumeDir) && !@mkdir($resumeDir, 0777, true) && !is_dir($resumeDir)) {
        return array('ok' => false, 'message' => 'Resume directory could not be created.');
    }

    $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '', pathinfo($filename, PATHINFO_FILENAME));
    if ($safeName === '') {
        $safeName = 'resume';
    }

    $storedName = uniqid(mt_rand(), true) . '_' . substr($safeName, 0, 40) . '.' . $extension;
    $storedPath = $resumeDir . DIRECTORY_SEPARATOR . $storedName;

    if (@file_put_contents($storedPath, $bytes) === false) {
        return array('ok' => false, 'message' => 'Resume attachment could not be saved.');
    }

    return array(
        'ok' => true,
        'stored_name' => $storedName,
        'stored_path' => $storedPath
    );
}

function careersImportFindLeadByEmail($connect, $email)
{
    $stmt = $connect->prepare("SELECT id, name, email, phonenumber, resume FROM tblleads WHERE email = ? LIMIT 1");
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($id, $name, $leadEmail, $phone, $resume);
    $row = null;
    if ($stmt->fetch()) {
        $row = array(
            'id' => $id,
            'name' => $name,
            'email' => $leadEmail,
            'phonenumber' => $phone,
            'resume' => $resume
        );
    }
    $stmt->close();

    return $row;
}

function careersImportCreateLead($connect, $name, $email, $phone, $sourceId, $statusId, $resumeName, $note)
{
    $date = date('Y-m-d H:i:s');
    $stmt = $connect->prepare("INSERT INTO tblleads (name, dateadded, status, source, email, phonenumber, ainfo, resume) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        return 0;
    }

    $stmt->bind_param('ssiissss', $name, $date, $statusId, $sourceId, $email, $phone, $note, $resumeName);
    $ok = $stmt->execute();
    $leadId = $ok ? (int) $stmt->insert_id : 0;
    $stmt->close();

    return $leadId;
}

function careersImportUpdateLeadResume($connect, $leadId, $phone, $resumeName, $note)
{
    $stmt = $connect->prepare("UPDATE tblleads SET phonenumber = IF(TRIM(COALESCE(phonenumber, '')) = '', ?, phonenumber), resume = ?, ainfo = CONCAT(COALESCE(ainfo, ''), ?) WHERE id = ?");
    if (!$stmt) {
        return false;
    }

    $appendNote = "\n\n" . $note;
    $stmt->bind_param('sssi', $phone, $resumeName, $appendNote, $leadId);
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    careersImportJson(array('ok' => false, 'message' => 'POST request required.'));
}

$rawBody = file_get_contents('php://input');
$payload = json_decode($rawBody, true);
if (!is_array($payload)) {
    careersImportJson(array('ok' => false, 'message' => 'Invalid JSON payload.'));
}

$expectedToken = isset($careers_email_import_token) ? trim((string) $careers_email_import_token) : '';
$providedToken = careersImportProvidedToken($payload);
if ($expectedToken === '' || !hash_equals($expectedToken, $providedToken)) {
    careersImportLog('Rejected careers email import because token was missing or invalid.', array(
        'message_id' => isset($payload['message_id']) ? $payload['message_id'] : ''
    ));
    careersImportJson(array('ok' => false, 'message' => 'Unauthorized.'));
}

$attachments = isset($payload['attachments']) && is_array($payload['attachments']) ? $payload['attachments'] : array();
if (empty($attachments)) {
    careersImportJson(array('ok' => false, 'message' => 'No resume attachment found.'));
}

$savedResume = null;
$attachmentErrors = array();
foreach ($attachments as $attachment) {
    $saveResult = careersImportSaveAttachment($attachment);
    if (!empty($saveResult['ok'])) {
        $savedResume = $saveResult;
        break;
    }
    $attachmentErrors[] = isset($saveResult['message']) ? $saveResult['message'] : 'Attachment skipped.';
}

if (!$savedResume) {
    careersImportJson(array(
        'ok' => false,
        'message' => 'No supported resume attachment could be imported.',
        'attachment_errors' => $attachmentErrors
    ));
}

$bodyText = isset($payload['body_text']) ? (string) $payload['body_text'] : '';
$fromEmail = careersImportExtractEmail(isset($payload['from_email']) ? $payload['from_email'] : '');
if ($fromEmail === '') {
    $fromEmail = careersImportExtractEmail($bodyText);
}
if ($fromEmail === '') {
    careersImportJson(array('ok' => false, 'message' => 'Could not detect applicant email address.'));
}

$fromName = isset($payload['from_name']) ? (string) $payload['from_name'] : '';
$name = careersImportNormalizeName($fromName, $fromEmail);
$phone = careersImportExtractPhone($bodyText);
$subject = careersImportCleanText(isset($payload['subject']) ? $payload['subject'] : '');
$messageId = careersImportCleanText(isset($payload['message_id']) ? $payload['message_id'] : '');
$sourceId = isset($careers_email_import_source_id) ? (int) $careers_email_import_source_id : 5;
$statusId = isset($careers_email_import_status_id) ? (int) $careers_email_import_status_id : 20;
$note = "Imported from careers email.";
if ($subject !== '') {
    $note .= "\nSubject: " . $subject;
}
if ($messageId !== '') {
    $note .= "\nGoogle message ID: " . $messageId;
}

$existingLead = careersImportFindLeadByEmail($connect, $fromEmail);
$created = false;
if ($existingLead) {
    $leadId = (int) $existingLead['id'];
    careersImportUpdateLeadResume($connect, $leadId, $phone, $savedResume['stored_name'], $note);
} else {
    $leadId = careersImportCreateLead($connect, $name, $fromEmail, $phone, $sourceId, $statusId, $savedResume['stored_name'], $note);
    $created = $leadId > 0;
}

if ($leadId <= 0) {
    careersImportJson(array('ok' => false, 'message' => 'Could not create or update CRM candidate.'));
}

$lead = array(
    'id' => $leadId,
    'name' => $existingLead && !empty($existingLead['name']) ? $existingLead['name'] : $name,
    'email' => $fromEmail,
    'phonenumber' => $existingLead && !empty($existingLead['phonenumber']) ? $existingLead['phonenumber'] : $phone,
    'resume' => $savedResume['stored_name']
);
$indexResult = processResumeLead($connect, $lead);

careersImportLog('Careers email imported into CRM.', array(
    'lead_id' => $leadId,
    'email' => $fromEmail,
    'resume' => $savedResume['stored_name'],
    'created' => $created,
    'indexed' => !empty($indexResult['ok'])
));

careersImportJson(array(
    'ok' => true,
    'message' => $created ? 'Candidate created from careers email.' : 'Existing candidate updated from careers email.',
    'lead_id' => $leadId,
    'created' => $created,
    'resume' => $savedResume['stored_name'],
    'indexed' => !empty($indexResult['ok'])
));

?>
