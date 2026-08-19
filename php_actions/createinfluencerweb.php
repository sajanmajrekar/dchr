<?php
ob_start();
ini_set('display_errors', '0');

header("Access-Control-Allow-Origin: https://digichefs.com");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
header("Content-Type: application/json; charset=utf-8");

if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_OFF);
}

register_shutdown_function(function () {
    $error = error_get_last();
    if (!$error || !in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true)) {
        return;
    }

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    if (!headers_sent()) {
        http_response_code(500);
        header("Content-Type: application/json; charset=utf-8");
    }

    echo json_encode(array(
        'success' => false,
        'messages' => 'Something went wrong while submitting the form. Please try again.'
    ), JSON_UNESCAPED_SLASHES);
});

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../includes/dbcon.php';

if (!isset($conn) && isset($connect)) {
    $conn = $connect;
}

function influencerPostValue($key)
{
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
}

function influencerSqlValue($connect, $key)
{
    return $connect->real_escape_string(influencerPostValue($key));
}

function influencerLimitText($value, $maxLength)
{
    $value = trim((string) $value);
    $maxLength = max(1, (int) $maxLength);

    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        return mb_strlen($value, 'UTF-8') > $maxLength ? mb_substr($value, 0, $maxLength, 'UTF-8') : $value;
    }

    return strlen($value) > $maxLength ? substr($value, 0, $maxLength) : $value;
}

function influencerColumnExists($connect, $columnName)
{
    $result = false;
    try {
        $result = $connect->query("SHOW COLUMNS FROM tblleads LIKE '" . $connect->real_escape_string($columnName) . "'");
    } catch (Throwable $e) {
        return false;
    } catch (Exception $e) {
        return false;
    }

    $exists = $result && $result->num_rows > 0;
    if ($result) {
        $result->free();
    }

    return $exists;
}

function influencerMaybeColumn($connect, $columnName, $value, &$columns, &$values, &$updates)
{
    if (!influencerColumnExists($connect, $columnName)) {
        return;
    }

    $safeValue = $connect->real_escape_string((string) $value);
    $columns[] = "`" . $columnName . "`";
    $values[] = "'" . $safeValue . "'";
    $updates[] = "`" . $columnName . "` = '" . $safeValue . "'";
}

function influencerRequiredColumn($connect, $columnName, $value, &$columns, &$values, &$updates)
{
    $safeValue = $connect->real_escape_string((string) $value);
    $columns[] = "`" . $columnName . "`";
    $values[] = "'" . $safeValue . "'";
    $updates[] = "`" . $columnName . "` = '" . $safeValue . "'";
}

function influencerResponse($success, $message)
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    if (!headers_sent()) {
        header("Content-Type: application/json; charset=utf-8");
    }

    echo json_encode(array(
        'success' => $success,
        'messages' => $message
    ), JSON_UNESCAPED_SLASHES);
    exit;
}

if (!isset($conn) || !($conn instanceof mysqli)) {
    influencerResponse(false, 'Something went wrong while submitting the form. Please try again.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    influencerResponse(false, 'Only POST requests are allowed.');
}

$nameRaw = influencerLimitText(influencerPostValue('name'), 300);
$channelLinkRaw = influencerLimitText(influencerPostValue('channel_link'), 250);
$phoneRaw = influencerLimitText(influencerPostValue('phone'), 100);
$emailRaw = influencerLimitText(influencerPostValue('email'), 255);
$mediaKitRaw = influencerLimitText(influencerPostValue('media_kit'), 250);
$reelCostRaw = influencerLimitText(influencerPostValue('reel_cost'), 100);
$pastCollabsRaw = influencerPostValue('past_collabs');
$verticalsRaw = isset($_POST['verticals']) && is_array($_POST['verticals'])
    ? implode(', ', array_map('trim', $_POST['verticals']))
    : influencerPostValue('verticals');
$verticalsRaw = influencerLimitText($verticalsRaw, 1000);

$name = $conn->real_escape_string($nameRaw);
$channelLink = $conn->real_escape_string($channelLinkRaw);
$phone = $conn->real_escape_string($phoneRaw);
$email = $conn->real_escape_string($emailRaw);
$mediaKit = $conn->real_escape_string($mediaKitRaw);
$reelCost = $conn->real_escape_string($reelCostRaw);
$pastCollabs = $conn->real_escape_string($pastCollabsRaw);
$verticals = isset($_POST['verticals']) && is_array($_POST['verticals'])
    ? $conn->real_escape_string($verticalsRaw)
    : $conn->real_escape_string($verticalsRaw);

if ($name === '' || $channelLink === '' || $phone === '' || $email === '' || $verticals === '' || $reelCost === '') {
    influencerResponse(false, 'Please fill all required fields.');
}

if (!filter_var($emailRaw, FILTER_VALIDATE_EMAIL)) {
    influencerResponse(false, 'Please enter a valid email address.');
}

$date = date('Y-m-d H:i:s');
$status = 20;
$source = 30;
$roles = '23';
$ainfo = $conn->real_escape_string(
    "Influencer lead\n" .
    "Channel / Handle: " . $channelLinkRaw . "\n" .
    "Preferred verticals: " . $verticalsRaw . "\n" .
    "Media kit / portfolio: " . $mediaKitRaw . "\n" .
    "Ball park cost per reel: " . $reelCostRaw . "\n" .
    "Past brand collaborations: " . $pastCollabsRaw
);

$columns = array();
$values = array();
$updates = array();

influencerRequiredColumn($conn, 'name', $nameRaw, $columns, $values, $updates);
influencerMaybeColumn($conn, 'website', $channelLinkRaw, $columns, $values, $updates);
influencerRequiredColumn($conn, 'dateadded', $date, $columns, $values, $updates);
influencerRequiredColumn($conn, 'status', $status, $columns, $values, $updates);
influencerRequiredColumn($conn, 'source', $source, $columns, $values, $updates);
influencerRequiredColumn($conn, 'email', $emailRaw, $columns, $values, $updates);
influencerRequiredColumn($conn, 'phonenumber', $phoneRaw, $columns, $values, $updates);
influencerMaybeColumn($conn, 'skillset', $verticalsRaw, $columns, $values, $updates);
influencerMaybeColumn($conn, 'ainfo', "Influencer lead\nChannel / Handle: " . $channelLinkRaw . "\nPreferred verticals: " . $verticalsRaw . "\nMedia kit / portfolio: " . $mediaKitRaw . "\nBall park cost per reel: " . $reelCostRaw . "\nPast brand collaborations: " . $pastCollabsRaw, $columns, $values, $updates);
influencerMaybeColumn($conn, 'roles', $roles, $columns, $values, $updates);
influencerMaybeColumn($conn, 'portfolio', $mediaKitRaw, $columns, $values, $updates);
influencerMaybeColumn($conn, 'modified', $date, $columns, $values, $updates);

influencerMaybeColumn($conn, 'leadtype', 'influencer', $columns, $values, $updates);
influencerMaybeColumn($conn, 'influencer_channel_link', $channelLinkRaw, $columns, $values, $updates);
influencerMaybeColumn($conn, 'influencer_verticals', $verticalsRaw, $columns, $values, $updates);
influencerMaybeColumn($conn, 'influencer_media_kit', $mediaKitRaw, $columns, $values, $updates);
influencerMaybeColumn($conn, 'influencer_reel_cost', $reelCostRaw, $columns, $values, $updates);
influencerMaybeColumn($conn, 'influencer_brand_collabs', $pastCollabsRaw, $columns, $values, $updates);

$emailCheck = false;
try {
    $emailCheck = $conn->query("SELECT id FROM tblleads WHERE email = '" . $email . "' LIMIT 1");
} catch (Throwable $e) {
    $emailCheck = false;
} catch (Exception $e) {
    $emailCheck = false;
}
$existingLead = $emailCheck ? $emailCheck->fetch_assoc() : null;
if ($emailCheck) {
    $emailCheck->free();
}

if ($existingLead && !empty($existingLead['id'])) {
    $leadId = (int) $existingLead['id'];
    $sql = "UPDATE tblleads SET " . implode(', ', $updates) . " WHERE id = " . $leadId;
    $internalSubject = 'Digichefs || Influencer application updated';
} else {
    $sql = "INSERT INTO tblleads (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
    $internalSubject = 'Digichefs || Influencer application received';
}

$mailBody = '<table width="600" border="0" cellspacing="0" cellpadding="0" style="border:solid 1px #cccccc;border-radius:10px;font-family:Arial,Helvetica,sans-serif;font-size:14px;margin:30px 20px">
    <tr><td colspan="3" style="font-weight:bold;color:#000;padding:15px 10px;">New influencer inquiry received.</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Name</td><td>:</td><td style="padding:10px;">' . $name . '</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Channel / Handle</td><td>:</td><td style="padding:10px;">' . $channelLink . '</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Phone</td><td>:</td><td style="padding:10px;">' . $phone . '</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Email</td><td>:</td><td style="padding:10px;">' . $email . '</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Preferred Verticals</td><td>:</td><td style="padding:10px;">' . $verticals . '</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Media Kit</td><td>:</td><td style="padding:10px;">' . ($mediaKit !== '' ? $mediaKit : 'Not provided') . '</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Cost Per Reel</td><td>:</td><td style="padding:10px;">' . $reelCost . '</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Past Collabs</td><td>:</td><td style="padding:10px;">' . ($pastCollabs !== '' ? nl2br($pastCollabs) : 'Not provided') . '</td></tr>
</table>';

$candidateBody = "Hey " . stripslashes($name) . ",<br><br>Thank you for sharing your influencer profile with DigiChefs. Our team will review your details and reach out if there is a relevant brand collaboration fit.<br><br>Regards,<br>Team DigiChefs";

$saveOk = false;
try {
    $saveOk = $conn->query($sql) === true;
} catch (Throwable $e) {
    $saveOk = false;
} catch (Exception $e) {
    $saveOk = false;
}

if ($saveOk) {
    if (function_exists('SendMailHTML')) {
        SendMailHTML('careers@digichefs.com', $internalSubject, $mailBody, '', '');
        SendMailHTML($emailRaw, 'DigiChefs || Influencer profile received', $candidateBody, '', '');
    }

    influencerResponse(true, 'Thank you! We have received your influencer profile.');
}

influencerResponse(false, 'Something went wrong while submitting the form. Please try again.');
?>
