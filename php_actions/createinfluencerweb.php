<?php
header("Access-Control-Allow-Origin: https://digichefs.com");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include('../mail/lib.php');
include("../includes/dbcon.php");

function influencerPostValue($key)
{
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
}

function influencerSqlValue($connect, $key)
{
    return $connect->real_escape_string(influencerPostValue($key));
}

function influencerColumnExists($connect, $columnName)
{
    $result = $connect->query("SHOW COLUMNS FROM tblleads LIKE '" . $connect->real_escape_string($columnName) . "'");
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

function influencerResponse($success, $message)
{
    echo json_encode(array(
        'success' => $success,
        'messages' => $message
    ), JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    influencerResponse(false, 'Only POST requests are allowed.');
}

$name = influencerSqlValue($conn, 'name');
$channelLink = influencerSqlValue($conn, 'channel_link');
$phone = influencerSqlValue($conn, 'phone');
$email = influencerSqlValue($conn, 'email');
$mediaKit = influencerSqlValue($conn, 'media_kit');
$reelCost = influencerSqlValue($conn, 'reel_cost');
$pastCollabs = influencerSqlValue($conn, 'past_collabs');
$verticals = isset($_POST['verticals']) && is_array($_POST['verticals'])
    ? $conn->real_escape_string(implode(', ', array_map('trim', $_POST['verticals'])))
    : influencerSqlValue($conn, 'verticals');

if ($name === '' || $channelLink === '' || $phone === '' || $email === '' || $verticals === '' || $reelCost === '') {
    influencerResponse(false, 'Please fill all required fields.');
}

if (!filter_var(stripslashes($email), FILTER_VALIDATE_EMAIL)) {
    influencerResponse(false, 'Please enter a valid email address.');
}

$date = date('Y-m-d H:i:s');
$status = 20;
$source = 30;
$roles = '23';
$ainfo = $conn->real_escape_string(
    "Influencer lead\n" .
    "Channel / Handle: " . stripslashes($channelLink) . "\n" .
    "Preferred verticals: " . stripslashes($verticals) . "\n" .
    "Media kit / portfolio: " . stripslashes($mediaKit) . "\n" .
    "Ball park cost per reel: " . stripslashes($reelCost) . "\n" .
    "Past brand collaborations: " . stripslashes($pastCollabs)
);

$columns = array(
    '`name`',
    '`website`',
    '`dateadded`',
    '`status`',
    '`source`',
    '`email`',
    '`phonenumber`',
    '`skillset`',
    '`ainfo`',
    '`roles`',
    '`portfolio`'
);
$values = array(
    "'" . $name . "'",
    "'" . $channelLink . "'",
    "'" . $date . "'",
    "'" . $status . "'",
    "'" . $source . "'",
    "'" . $email . "'",
    "'" . $phone . "'",
    "'" . $verticals . "'",
    "'" . $ainfo . "'",
    "'" . $roles . "'",
    "'" . $mediaKit . "'"
);
$updates = array(
    "`name` = '" . $name . "'",
    "`website` = '" . $channelLink . "'",
    "`phonenumber` = '" . $phone . "'",
    "`skillset` = '" . $verticals . "'",
    "`ainfo` = '" . $ainfo . "'",
    "`roles` = '" . $roles . "'",
    "`portfolio` = '" . $mediaKit . "'",
    "`modified` = '" . $date . "'"
);

influencerMaybeColumn($conn, 'leadtype', 'influencer', $columns, $values, $updates);
influencerMaybeColumn($conn, 'influencer_channel_link', stripslashes($channelLink), $columns, $values, $updates);
influencerMaybeColumn($conn, 'influencer_verticals', stripslashes($verticals), $columns, $values, $updates);
influencerMaybeColumn($conn, 'influencer_media_kit', stripslashes($mediaKit), $columns, $values, $updates);
influencerMaybeColumn($conn, 'influencer_reel_cost', stripslashes($reelCost), $columns, $values, $updates);
influencerMaybeColumn($conn, 'influencer_brand_collabs', stripslashes($pastCollabs), $columns, $values, $updates);

$emailCheck = $conn->query("SELECT id FROM tblleads WHERE email = '" . $email . "' LIMIT 1");
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

if ($conn->query($sql) === true) {
    SendMailHTML('careers@digichefs.com', $internalSubject, $mailBody, '', '');
    SendMailHTML(stripslashes($email), 'DigiChefs || Influencer profile received', $candidateBody, '', '');
    influencerResponse(true, 'Thank you! We have received your influencer profile.');
}

influencerResponse(false, 'Error while saving influencer details: ' . $conn->error);
?>
