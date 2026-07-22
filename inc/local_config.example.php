<?php

// Copy this file to inc/local_config.php and fill in local-only secrets.

$gemini_resume_api_key = 'PASTE_NEW_GEMINI_API_KEY_HERE';
$gemini_resume_model = 'gemini-3.5-flash';

// Optional: keep parser paths here too, so they stay out of tracked files.
$resume_tika_java_path = '';
$resume_tika_jar_path = '';

// Careers mailbox importer webhook.
// Use a long random value and keep it private. The Google Apps Script will send this token.
$careers_email_import_token = 'PASTE_LONG_RANDOM_TOKEN_HERE';
$careers_email_import_source_id = 5;
$careers_email_import_status_id = 20;
