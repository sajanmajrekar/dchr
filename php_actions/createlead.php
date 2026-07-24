<?php

require_once 'core.php';
require_once __DIR__ . '/../includes/resume_intelligence.php';

$valid = array('success' => false, 'messages' => array());

function createLeadPostValue($key)
{
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
}

function createLeadSqlValue($connect, $key)
{
    return $connect->real_escape_string(createLeadPostValue($key));
}

function createLeadSelectedRoles($connect)
{
    if (empty($_POST['example-chosen-multiple']) || !is_array($_POST['example-chosen-multiple'])) {
        return '';
    }

    $roles = array_map('trim', $_POST['example-chosen-multiple']);
    $roles = array_filter($roles, function ($role) {
        return $role !== '';
    });

    return $connect->real_escape_string(implode(',', $roles));
}

function createLeadUploadedResume()
{
    if (empty($_FILES['example-file-input']['name'])) {
        return array('ok' => true, 'file_name' => '', 'message' => '');
    }

    $originalName = (string) $_FILES['example-file-input']['name'];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($extension, array('docx', 'doc', 'pdf', 'rtf'), true)) {
        return array('ok' => false, 'file_name' => '', 'message' => 'File format is not valid. Please use pdf or doc format.');
    }

    if (empty($_FILES['example-file-input']['tmp_name']) || !is_uploaded_file($_FILES['example-file-input']['tmp_name'])) {
        return array('ok' => false, 'file_name' => '', 'message' => 'Resume upload failed.');
    }

    $fileName = uniqid((string) rand(), true) . '.' . $extension;
    $targetPath = __DIR__ . '/../resume/' . $fileName;
    if (!move_uploaded_file($_FILES['example-file-input']['tmp_name'], $targetPath)) {
        return array('ok' => false, 'file_name' => '', 'message' => 'Could not save uploaded resume.');
    }

    return array('ok' => true, 'file_name' => $fileName, 'message' => '');
}

function createLeadIndexResume($connect, $leadId, $name, $email, $phone, $resume)
{
    if ((int) $leadId <= 0 || trim((string) $resume) === '') {
        return;
    }

    processResumeLead($connect, array(
        'id' => (int) $leadId,
        'name' => $name,
        'email' => $email,
        'phonenumber' => $phone,
        'resume' => $resume
    ));
}

if ($_POST) {
    $name = createLeadSqlValue($connect, 'name');
    $email = createLeadSqlValue($connect, 'email');
    $phone = createLeadSqlValue($connect, 'phone');
    $source = createLeadSqlValue($connect, 'source');
    $street = createLeadSqlValue($connect, 'street');
    $country = createLeadSqlValue($connect, 'country');
    $city = createLeadSqlValue($connect, 'city');
    $pincode = createLeadSqlValue($connect, 'pincode');
    $experience = createLeadSqlValue($connect, 'experience');
    $qualification = createLeadSqlValue($connect, 'qualification');
    $cjob = createLeadSqlValue($connect, 'cjob');
    $cemployer = createLeadSqlValue($connect, 'cemployer');
    $expected = createLeadSqlValue($connect, 'expected');
    $csalary = createLeadSqlValue($connect, 'csalary');
    $skillset = createLeadSqlValue($connect, 'skillset');
    $info = createLeadSqlValue($connect, 'info');
    $nperiod = createLeadSqlValue($connect, 'notice');
    $selectedOption = createLeadSelectedRoles($connect);
    $date = date('Y-m-d H:i:s');

    $upload = createLeadUploadedResume();
    if (!$upload['ok']) {
        $valid['success'] = false;
        $valid['messages'] = $upload['message'];
        echo json_encode($valid);
        exit;
    }

    $existingLead = null;
    $result = $connect->query("SELECT id, resume FROM tblleads WHERE email = '" . $email . "' LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $existingLead = $result->fetch_assoc();
    }
    if ($result) {
        $result->free();
    }

    $resumeFileName = $upload['file_name'];
    if ($existingLead && $resumeFileName === '') {
        $resumeFileName = isset($existingLead['resume']) ? (string) $existingLead['resume'] : '';
    }
    $resumeSql = $connect->real_escape_string($resumeFileName);

    if ($existingLead) {
        $leadId = (int) $existingLead['id'];
        $sql = "UPDATE tblleads SET
            name = '$name',
            country = '$country',
            zip = '$pincode',
            city = '$city',
            street = '$street',
            source = '$source',
            email = '$email',
            phonenumber = '$phone',
            experiance = '$experience',
            qualification = '$qualification',
            cjtitle = '$cjob',
            cemployer = '$cemployer',
            esalary = '$expected',
            csalary = '$csalary',
            skillset = '$skillset',
            ainfo = '$info',
            roles = '$selectedOption',
            nperiod = '$nperiod',
            resume = '$resumeSql',
            modified = '$date'
            WHERE id = '$leadId'";
        $message = 'Existing candidate updated successfully.';
    } else {
        $sql = "INSERT INTO `tblleads`(`name`, `country`, `zip`, `city`, `street`, `dateadded`, `status`, `source`, `email`, `phonenumber`, `experiance`, `qualification`, `cjtitle`, `cemployer`, `esalary`, `csalary`, `skillset`, `ainfo`, `roles`, `nperiod`, `resume`) VALUES ('$name', '$country', '$pincode', '$city', '$street', '$date', '20', '$source', '$email', '$phone', '$experience', '$qualification', '$cjob', '$cemployer', '$expected', '$csalary', '$skillset', '$info', '$selectedOption', '$nperiod', '$resumeSql')";
        $message = 'Candidate created successfully.';
    }

    if ($connect->query($sql) === true) {
        $leadId = $existingLead ? (int) $existingLead['id'] : (int) $connect->insert_id;
        createLeadIndexResume($connect, $leadId, stripslashes($name), stripslashes($email), stripslashes($phone), $resumeFileName);
        $valid['success'] = true;
        $valid['messages'] = $message;
    } else {
        $valid['success'] = false;
        $valid['messages'] = 'Error while saving the candidate: ' . $connect->error;
    }

    $connect->close();
}

echo json_encode($valid);

?>
