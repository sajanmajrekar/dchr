<?php

require_once __DIR__ . '/php_actions/db_connect.php';
require_once __DIR__ . '/includes/resume_intelligence.php';

if (!($connect instanceof mysqli)) {
    fwrite(STDERR, "Database connection failed.\n");
    exit(1);
}

if (!ensureResumeIntelligenceTables($connect)) {
    fwrite(STDERR, "Could not initialize resume intelligence tables.\n");
    exit(1);
}

$limit = 200;
$token = '';

foreach ($argv as $argument) {
    if (strpos($argument, '--limit=') === 0) {
        $limit = (int) substr($argument, 8);
    } elseif (strpos($argument, '--token=') === 0) {
        $token = (string) substr($argument, 8);
    }
}

$limit = max(1, $limit);
if ($token === '') {
    $token = 'resume_worker_' . time();
}

$counts = array(
    'processed_total' => 0,
    'completed_total' => 0,
    'pending_total' => 0,
    'missing_total' => 0,
    'unsupported_total' => 0,
    'error_total' => 0
);

if (!resetResumeWorkerState($connect, $token, $limit)) {
    fwrite(STDERR, "Could not start worker state.\n");
    exit(1);
}

$leads = getNextResumeBatchLeads($connect, $limit);

if (empty($leads)) {
    finishResumeWorkerState($connect, $token, $counts, 'No new resumes were available for this worker run.');
    fwrite(STDOUT, "No new resumes were available.\n");
    exit(0);
}

foreach ($leads as $lead) {
    $result = processResumeLead($connect, $lead);
    $counts['processed_total']++;

    if ($result['status'] === 'completed') {
        $counts['completed_total']++;
    } elseif ($result['status'] === 'pending') {
        $counts['pending_total']++;
    } elseif ($result['status'] === 'missing') {
        $counts['missing_total']++;
    } elseif ($result['status'] === 'unsupported') {
        $counts['unsupported_total']++;
    } else {
        $counts['error_total']++;
    }

    $message = 'Processed ' . $counts['processed_total'] . ' of ' . count($leads) . ' resumes.';
    updateResumeWorkerProgress($connect, $token, $counts, $message, $result['resume']);
}

$finalMessage = 'Worker completed ' . $counts['processed_total'] . ' resumes in this run.';
finishResumeWorkerState($connect, $token, $counts, $finalMessage);
fwrite(STDOUT, $finalMessage . "\n");
exit(0);
