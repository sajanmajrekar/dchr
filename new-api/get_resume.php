<?php
// new-api/get_resume.php
if (!isset($_GET['file'])) {
    header("HTTP/1.0 404 Not Found");
    echo "No file provided.";
    exit;
}

$resumeName = basename(str_replace('\\', '/', (string) $_GET['file']));
if ($resumeName === '' || $resumeName === '.' || $resumeName === '..') {
    header("HTTP/1.0 404 Not Found");
    echo "Invalid file name.";
    exit;
}

$root = dirname(__DIR__); // __DIR__ is new-api. dirname is hr
$directories = array(
    $root . DIRECTORY_SEPARATOR . 'resume',
    $root . DIRECTORY_SEPARATOR . 'Resume',
    $root . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'resume',
    $root . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'resumes'
);

$filePath = '';
foreach ($directories as $directory) {
    $path = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $resumeName;
    if (is_file($path) && is_readable($path) && (int) @filesize($path) > 0) {
        $filePath = $path;
        break;
    }
}

if ($filePath === '') {
    header("HTTP/1.0 404 Not Found");
    echo "File not found: " . htmlspecialchars($resumeName);
    exit;
}

$ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$contentType = 'application/octet-stream';
if ($ext === 'pdf') {
    $contentType = 'application/pdf';
} elseif ($ext === 'doc' || $ext === 'docx') {
    $contentType = 'application/msword';
} elseif ($ext === 'png') {
    $contentType = 'image/png';
} elseif ($ext === 'jpg' || $ext === 'jpeg') {
    $contentType = 'image/jpeg';
}

header('Content-Type: ' . $contentType);
header('Content-Length: ' . filesize($filePath));
// We use inline instead of attachment so it opens in the browser if supported
header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
readfile($filePath);
exit;
