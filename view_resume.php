<?php
require_once 'php_actions/db_connect.php';
require_once 'includes/resume_intelligence.php';

$resumeParam = isset($_GET['file']) ? trim((string) $_GET['file']) : '';

if ($resumeParam === '') {
    http_response_code(404);
    exit('Resume not found.');
}

$parsedPath = parse_url($resumeParam, PHP_URL_PATH);
if (is_string($parsedPath) && $parsedPath !== '') {
    $resumeParam = $parsedPath;
}

$resumeFile = basename(str_replace('\\', '/', urldecode($resumeParam)));

if ($resumeFile === '' || $resumeFile === '.' || $resumeFile === '..') {
    http_response_code(404);
    exit('Resume not found.');
}

$filePath = resolveResumeAbsolutePath($resumeFile);

if (($filePath === '' || !is_file($filePath) || !is_readable($filePath)) && isset($connect) && $connect instanceof mysqli) {
    $stmt = $connect->prepare("SELECT file_path, original_resume_name FROM resume_documents WHERE original_resume_name = ? ORDER BY id DESC LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('s', $resumeFile);
        if ($stmt->execute()) {
            $dbFilePath = '';
            $dbOriginalName = '';
            $stmt->bind_result($dbFilePath, $dbOriginalName);
            if ($stmt->fetch()) {
                $filePath = resolveResumeAbsolutePath(
                    $dbOriginalName !== '' ? (string) $dbOriginalName : $resumeFile,
                    $dbFilePath !== '' ? (string) $dbFilePath : ''
                );
            }
        }
        $stmt->close();
    }
}

if (!is_file($filePath) || !is_readable($filePath)) {
    http_response_code(404);
    exit('Resume not found.');
}

$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$contentTypes = array(
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'rtf' => 'application/rtf',
);

$contentType = isset($contentTypes[$extension]) ? $contentTypes[$extension] : 'application/octet-stream';

header('Content-Type: ' . $contentType);
header('Content-Length: ' . filesize($filePath));
header('Content-Disposition: inline; filename="' . rawurlencode($resumeFile) . '"');
header('X-Content-Type-Options: nosniff');

readfile($filePath);
exit;
