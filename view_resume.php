<?php

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

$filePath = __DIR__ . DIRECTORY_SEPARATOR . 'resume' . DIRECTORY_SEPARATOR . $resumeFile;

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
