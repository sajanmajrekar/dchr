<?php
ob_start();
ini_set('display_errors', '0');
date_default_timezone_set("Asia/Kolkata");

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
require_once __DIR__ . '/../mail/lib.php';

if (!isset($conn) && isset($connect)) {
    $conn = $connect;
}

function partnerPostValue($key)
{
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
}

function partnerLimitText($value, $maxLength)
{
    $value = trim((string) $value);
    $maxLength = max(1, (int) $maxLength);

    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        return mb_strlen($value, 'UTF-8') > $maxLength ? mb_substr($value, 0, $maxLength, 'UTF-8') : $value;
    }

    return strlen($value) > $maxLength ? substr($value, 0, $maxLength) : $value;
}

function partnerColumnExists($connect, $columnName)
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

function partnerMaybeColumn($connect, $columnName, $value, &$columns, &$values, &$updates)
{
    if (!partnerColumnExists($connect, $columnName)) {
        return;
    }

    $safeValue = $connect->real_escape_string((string) $value);
    $columns[] = "`" . $columnName . "`";
    $values[] = "'" . $safeValue . "'";
    $updates[] = "`" . $columnName . "` = '" . $safeValue . "'";
}

function partnerRequiredColumn($connect, $columnName, $value, &$columns, &$values, &$updates)
{
    $safeValue = $connect->real_escape_string((string) $value);
    $columns[] = "`" . $columnName . "`";
    $values[] = "'" . $safeValue . "'";
    $updates[] = "`" . $columnName . "` = '" . $safeValue . "'";
}

function partnerHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function partnerResponse($success, $message)
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

function partnerSyncGoogleSheet($payload)
{
    $sheetUrl = 'https://script.google.com/macros/s/AKfycbxXY5IYVWdmAZGKKzWxRiY1wmEdJ0zcBaOgJIxnBvgT0qEdhnkpxO-48DWvGNNV54yp9w/exec';
    $jsonPayload = json_encode($payload, JSON_UNESCAPED_SLASHES);

    if ($jsonPayload === false) {
        return false;
    }

    if (function_exists('curl_init')) {
        $curl = curl_init($sheetUrl);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($curl);
        $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return $response !== false && $httpCode >= 200 && $httpCode < 300;
    }

    $context = stream_context_create(array(
        'http' => array(
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => $jsonPayload,
            'timeout' => 10
        )
    ));

    return @file_get_contents($sheetUrl, false, $context) !== false;
}

if (!isset($conn) || !($conn instanceof mysqli)) {
    partnerResponse(false, 'Something went wrong while submitting the form. Please try again.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    partnerResponse(false, 'Only POST requests are allowed.');
}

$companyNameRaw = partnerLimitText(partnerPostValue('company_name'), 300);
$yourNameRaw = partnerLimitText(partnerPostValue('your_name'), 300);
$phoneRaw = partnerLimitText(partnerPostValue('phone'), 100);
$emailRaw = partnerLimitText(partnerPostValue('email'), 255);
$ratecardRaw = partnerLimitText(partnerPostValue('ratecard'), 1500);
$portfolioRaw = partnerLimitText(partnerPostValue('portfolio'), 250);
$companyLocationRaw = partnerLimitText(partnerPostValue('company_location'), 300);
$minimumValueRaw = partnerLimitText(partnerPostValue('minimum_value'), 300);
$teamSizeRaw = partnerLimitText(partnerPostValue('team_size'), 100);
$companyYearsRaw = partnerLimitText(partnerPostValue('company_years'), 100);

$servicesSelected = isset($_POST['services']) && is_array($_POST['services'])
    ? array_values(array_filter(array_map('trim', $_POST['services'])))
    : array();
$servicesCount = count($servicesSelected);
$servicesSelected = array_slice($servicesSelected, 0, 3);
$servicesRaw = partnerLimitText(implode(', ', $servicesSelected), 1000);

if ($companyNameRaw === '' || $yourNameRaw === '' || $phoneRaw === '' || $emailRaw === '' || $servicesRaw === '' || $companyLocationRaw === '' || $teamSizeRaw === '' || $companyYearsRaw === '') {
    partnerResponse(false, 'Please fill all required fields.');
}

if (!filter_var($emailRaw, FILTER_VALIDATE_EMAIL)) {
    partnerResponse(false, 'Please enter a valid email address.');
}

if ($servicesCount > 3) {
    partnerResponse(false, 'Please select maximum 3 services.');
}

$date = date('Y-m-d H:i:s');
$status = 20;
$source = 30;
$descriptionRaw =
    "Partner inquiry\n" .
    "Company name: " . $companyNameRaw . "\n" .
    "Contact person: " . $yourNameRaw . "\n" .
    "Phone: " . $phoneRaw . "\n" .
    "Email: " . $emailRaw . "\n" .
    "Services: " . $servicesRaw . "\n" .
    "Ratecard / Commercials: " . ($ratecardRaw !== '' ? $ratecardRaw : 'Not provided') . "\n" .
    "Portfolio: " . ($portfolioRaw !== '' ? $portfolioRaw : 'Not provided') . "\n" .
    "Company location: " . $companyLocationRaw . "\n" .
    "Minimum project / retainer value: " . ($minimumValueRaw !== '' ? $minimumValueRaw : 'Not provided') . "\n" .
    "Team size: " . $teamSizeRaw . "\n" .
    "Years since company founded: " . $companyYearsRaw;

$columns = array();
$values = array();
$updates = array();

partnerRequiredColumn($conn, 'name', $yourNameRaw, $columns, $values, $updates);
partnerRequiredColumn($conn, 'company', $companyNameRaw, $columns, $values, $updates);
partnerRequiredColumn($conn, 'description', $descriptionRaw, $columns, $values, $updates);
partnerRequiredColumn($conn, 'dateadded', $date, $columns, $values, $updates);
partnerRequiredColumn($conn, 'status', $status, $columns, $values, $updates);
partnerRequiredColumn($conn, 'source', $source, $columns, $values, $updates);
partnerRequiredColumn($conn, 'email', $emailRaw, $columns, $values, $updates);
partnerRequiredColumn($conn, 'phonenumber', $phoneRaw, $columns, $values, $updates);
partnerMaybeColumn($conn, 'city', $companyLocationRaw, $columns, $values, $updates);
partnerMaybeColumn($conn, 'website', $portfolioRaw, $columns, $values, $updates);
partnerMaybeColumn($conn, 'title', 'Partner Inquiry', $columns, $values, $updates);
partnerMaybeColumn($conn, 'modified', $date, $columns, $values, $updates);
partnerMaybeColumn($conn, 'leadtype', 'partner', $columns, $values, $updates);
partnerMaybeColumn($conn, 'partner_services', $servicesRaw, $columns, $values, $updates);
partnerMaybeColumn($conn, 'partner_ratecard', $ratecardRaw, $columns, $values, $updates);
partnerMaybeColumn($conn, 'partner_portfolio', $portfolioRaw, $columns, $values, $updates);
partnerMaybeColumn($conn, 'partner_minimum_value', $minimumValueRaw, $columns, $values, $updates);
partnerMaybeColumn($conn, 'partner_team_size', $teamSizeRaw, $columns, $values, $updates);
partnerMaybeColumn($conn, 'partner_company_years', $companyYearsRaw, $columns, $values, $updates);

$emailSafe = $conn->real_escape_string($emailRaw);
$emailCheck = false;
try {
    $emailCheck = $conn->query("SELECT id FROM tblleads WHERE email = '" . $emailSafe . "' LIMIT 1");
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
    $internalSubject = 'Digichefs || Partner inquiry updated';
} else {
    $sql = "INSERT INTO tblleads (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
    $internalSubject = 'Digichefs || Partner inquiry received';
}

$mailBody = '<table width="650" border="0" cellspacing="0" cellpadding="0" style="border:solid 1px #cccccc;border-radius:10px;font-family:Arial,Helvetica,sans-serif;font-size:14px;margin:30px 20px">
    <tr><td colspan="3" style="font-weight:bold;color:#000;padding:15px 10px;">New partner inquiry received.</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Company Name</td><td>:</td><td style="padding:10px;">' . partnerHtml($companyNameRaw) . '</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Your Name</td><td>:</td><td style="padding:10px;">' . partnerHtml($yourNameRaw) . '</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Number</td><td>:</td><td style="padding:10px;">' . partnerHtml($phoneRaw) . '</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Email ID</td><td>:</td><td style="padding:10px;">' . partnerHtml($emailRaw) . '</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Services</td><td>:</td><td style="padding:10px;">' . partnerHtml($servicesRaw) . '</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Ratecard</td><td>:</td><td style="padding:10px;">' . nl2br(partnerHtml($ratecardRaw !== '' ? $ratecardRaw : 'Not provided')) . '</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Portfolio</td><td>:</td><td style="padding:10px;">' . partnerHtml($portfolioRaw !== '' ? $portfolioRaw : 'Not provided') . '</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Company Location</td><td>:</td><td style="padding:10px;">' . partnerHtml($companyLocationRaw) . '</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Minimum Value</td><td>:</td><td style="padding:10px;">' . partnerHtml($minimumValueRaw !== '' ? $minimumValueRaw : 'Not provided') . '</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Team Size</td><td>:</td><td style="padding:10px;">' . partnerHtml($teamSizeRaw) . '</td></tr>
    <tr><td style="font-weight:bold;padding:10px;">Years Since Founded</td><td>:</td><td style="padding:10px;">' . partnerHtml($companyYearsRaw) . '</td></tr>
</table>';

$candidateBody = "Hey " . partnerHtml($yourNameRaw) . ",<br><br>Thank you for sharing your partner details with DigiChefs. Our team will review your company profile and reach out if there is a relevant collaboration fit.<br><br>Regards,<br>Team DigiChefs";

$saveOk = false;
try {
    $saveOk = $conn->query($sql) === true;
} catch (Throwable $e) {
    $saveOk = false;
} catch (Exception $e) {
    $saveOk = false;
}

if ($saveOk) {
    partnerSyncGoogleSheet(array(
        'company_name' => $companyNameRaw,
        'your_name' => $yourNameRaw,
        'phone' => $phoneRaw,
        'email' => $emailRaw,
        'services' => $servicesRaw,
        'ratecard' => $ratecardRaw,
        'portfolio' => $portfolioRaw,
        'company_location' => $companyLocationRaw,
        'minimum_value' => $minimumValueRaw,
        'team_size' => $teamSizeRaw,
        'company_years' => $companyYearsRaw,
        'date' => $date
    ));

    if (function_exists('SendMailHTML')) {
        SendMailHTML('careers@digichefs.com,contact@digichefs.com', $internalSubject, $mailBody, '', '');
        SendMailHTML($emailRaw, 'DigiChefs || Partner details received', $candidateBody, '', '');
    }

    partnerResponse(true, 'Thank you! We have received your partner details.');
}

partnerResponse(false, 'Something went wrong while submitting the form. Please try again.');
?>
