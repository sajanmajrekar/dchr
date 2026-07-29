<?php

function resumeIntelligenceConfigPath()
{
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'resume_intelligence_config.json';
}

function loadResumeIntelligenceConfig()
{
    global $resume_tika_java_path, $resume_tika_jar_path;

    $default = array(
        'java_path' => isset($resume_tika_java_path) ? trim((string) $resume_tika_java_path) : '',
        'tika_jar_path' => isset($resume_tika_jar_path) ? trim((string) $resume_tika_jar_path) : ''
    );

    $configPath = resumeIntelligenceConfigPath();
    if (!is_file($configPath)) {
        return $default;
    }

    $contents = @file_get_contents($configPath);
    if ($contents === false || trim($contents) === '') {
        return $default;
    }

    $decoded = json_decode($contents, true);
    if (!is_array($decoded)) {
        return $default;
    }

    $merged = array_merge($default, $decoded);

    if ($default['java_path'] !== '') {
        $merged['java_path'] = $default['java_path'];
    }
    if ($default['tika_jar_path'] !== '') {
        $merged['tika_jar_path'] = $default['tika_jar_path'];
    }

    return $merged;
}

function saveResumeIntelligenceConfig($config)
{
    $configPath = resumeIntelligenceConfigPath();
    $configDir = dirname($configPath);

    if (!is_dir($configDir) && !@mkdir($configDir, 0777, true) && !is_dir($configDir)) {
        return false;
    }

    $payload = array(
        'java_path' => isset($config['java_path']) ? trim((string) $config['java_path']) : '',
        'tika_jar_path' => isset($config['tika_jar_path']) ? trim((string) $config['tika_jar_path']) : ''
    );

    return @file_put_contents($configPath, resumeJsonEncode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false;
}

function geminiResumeConfigPath()
{
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'gemini_resume_config.json';
}

function loadGeminiResumeConfig()
{
    global $gemini_resume_api_key, $gemini_resume_model;

    $default = array(
        'api_key' => isset($gemini_resume_api_key) ? trim((string) $gemini_resume_api_key) : '',
        'model' => isset($gemini_resume_model) && trim((string) $gemini_resume_model) !== '' ? trim((string) $gemini_resume_model) : 'gemini-3.5-flash'
    );

    $configPath = geminiResumeConfigPath();
    if (!is_file($configPath)) {
        return $default;
    }

    $contents = @file_get_contents($configPath);
    if ($contents === false || trim($contents) === '') {
        return $default;
    }

    $decoded = json_decode($contents, true);
    if (!is_array($decoded)) {
        return $default;
    }

    $merged = array_merge($default, $decoded);

    if ($default['api_key'] !== '') {
        $merged['api_key'] = $default['api_key'];
    }
    if ($default['model'] !== '') {
        $merged['model'] = $default['model'];
    }

    return $merged;
}

function saveGeminiResumeConfig($config)
{
    $configPath = geminiResumeConfigPath();
    $configDir = dirname($configPath);

    if (!is_dir($configDir) && !@mkdir($configDir, 0777, true) && !is_dir($configDir)) {
        return false;
    }

    $payload = array(
        'api_key' => isset($config['api_key']) ? trim((string) $config['api_key']) : '',
        'model' => isset($config['model']) ? trim((string) $config['model']) : 'gemini-3.5-flash'
    );

    return @file_put_contents($configPath, resumeJsonEncode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false;
}

function getGeminiResumeStatus()
{
    $config = loadGeminiResumeConfig();
    $apiKey = isset($config['api_key']) ? trim((string) $config['api_key']) : '';

    return array(
        'config' => $config,
        'api_key_ready' => $apiKey !== '',
        'model' => isset($config['model']) ? trim((string) $config['model']) : 'gemini-3.5-flash'
    );
}

function getResumeParserStatus()
{
    $config = loadResumeIntelligenceConfig();
    $javaPath = isset($config['java_path']) ? trim((string) $config['java_path']) : '';
    $tikaJarPath = isset($config['tika_jar_path']) ? trim((string) $config['tika_jar_path']) : '';

    $javaReady = $javaPath !== '' && is_file($javaPath);
    $tikaReady = $tikaJarPath !== '' && is_file($tikaJarPath);

    return array(
        'config' => $config,
        'java_ready' => $javaReady,
        'tika_ready' => $tikaReady,
        'tika_available' => $javaReady && $tikaReady
    );
}

function resumeDocumentsTableSql()
{
    return <<<SQL
CREATE TABLE IF NOT EXISTS `resume_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lead_id` int(11) NOT NULL,
  `lead_name` varchar(300) DEFAULT NULL,
  `original_resume_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_extension` varchar(20) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `file_hash` varchar(64) DEFAULT NULL,
  `extraction_status` enum('pending','completed','unsupported','missing','error') NOT NULL DEFAULT 'pending',
  `extraction_engine` varchar(50) DEFAULT NULL,
  `raw_text` longtext DEFAULT NULL,
  `extracted_name` varchar(255) DEFAULT NULL,
  `extracted_email` varchar(255) DEFAULT NULL,
  `extracted_phone` varchar(100) DEFAULT NULL,
  `extracted_skills` text DEFAULT NULL,
  `metadata_json` longtext DEFAULT NULL,
  `last_error` text DEFAULT NULL,
  `extracted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_resume_doc_lead_file` (`lead_id`,`original_resume_name`),
  KEY `idx_resume_doc_status` (`extraction_status`),
  KEY `idx_resume_doc_file_hash` (`file_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;
}

function resumeWorkerStateTableSql()
{
    return <<<SQL
CREATE TABLE IF NOT EXISTS `resume_worker_state` (
  `id` tinyint(1) NOT NULL DEFAULT 1,
  `is_running` tinyint(1) NOT NULL DEFAULT 0,
  `worker_token` varchar(100) DEFAULT NULL,
  `worker_limit` int(11) NOT NULL DEFAULT 0,
  `processed_total` int(11) NOT NULL DEFAULT 0,
  `completed_total` int(11) NOT NULL DEFAULT 0,
  `pending_total` int(11) NOT NULL DEFAULT 0,
  `missing_total` int(11) NOT NULL DEFAULT 0,
  `unsupported_total` int(11) NOT NULL DEFAULT 0,
  `error_total` int(11) NOT NULL DEFAULT 0,
  `last_message` varchar(255) DEFAULT NULL,
  `current_file` varchar(255) DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `heartbeat_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;
}

function ensureResumeDocumentsTable($connect)
{
    return $connect instanceof mysqli && $connect->query(resumeDocumentsTableSql());
}

function ensureResumeWorkerStateTable($connect)
{
    if (!($connect instanceof mysqli) || !$connect->query(resumeWorkerStateTableSql())) {
        return false;
    }

    $connect->query("INSERT IGNORE INTO resume_worker_state (id) VALUES (1)");
    return true;
}

function ensureResumeIntelligenceTables($connect)
{
    return ensureResumeDocumentsTable($connect) && ensureResumeWorkerStateTable($connect);
}

function normalizeResumeWhitespace($text)
{
    $text = str_replace(array("\r\n", "\r"), "\n", (string) $text);
    $text = preg_replace("/[ \t]+/", " ", $text);
    $text = preg_replace("/\n{3,}/", "\n\n", $text);
    return trim((string) $text);
}

function resumeIntelligenceLog($channel, $message, $context = array())
{
    $logDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0777, true);
    }

    $channel = preg_replace('/[^a-zA-Z0-9_\-]/', '_', (string) $channel);
    if ($channel === '') {
        $channel = 'resume_intelligence';
    }

    $line = '[' . date('Y-m-d H:i:s') . '] ' . trim((string) $message);
    if (!empty($context)) {
        $encodedContext = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (is_string($encodedContext) && $encodedContext !== '') {
            $line .= ' | ' . $encodedContext;
        }
    }

    @file_put_contents($logDir . DIRECTORY_SEPARATOR . $channel . '.log', $line . PHP_EOL, FILE_APPEND);
}

function normalizeResumeUtf8Value($value)
{
    if (is_array($value)) {
        $normalized = array();
        foreach ($value as $key => $item) {
            $normalized[$key] = normalizeResumeUtf8Value($item);
        }
        return $normalized;
    }

    if (!is_string($value) || preg_match('//u', $value)) {
        return $value;
    }

    if (function_exists('mb_convert_encoding')) {
        return mb_convert_encoding($value, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
    }

    if (function_exists('iconv')) {
        $converted = @iconv('Windows-1252', 'UTF-8//IGNORE', $value);
        if ($converted !== false) {
            return $converted;
        }
    }

    return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $value);
}

function resumeJsonEncode($value, $options = 0)
{
    return json_encode(normalizeResumeUtf8Value($value), $options | JSON_INVALID_UTF8_SUBSTITUTE);
}

function sanitizeGeminiTextPart($text)
{
    $text = normalizeResumeUtf8Value((string) $text);
    $text = str_replace(array("\r\n", "\r"), "\n", (string) $text);
    $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]+/', ' ', (string) $text);
    return trim((string) $text);
}

function truncateGeminiTextPart($text, $maxChars = 18000)
{
    $text = sanitizeGeminiTextPart($text);
    $maxChars = max(500, (int) $maxChars);

    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text, 'UTF-8') <= $maxChars) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $maxChars, 'UTF-8')) . "\n\n[Resume text truncated for API request]";
    }

    if (strlen($text) <= $maxChars) {
        return $text;
    }

    return rtrim(substr($text, 0, $maxChars)) . "\n\n[Resume text truncated for API request]";
}

function buildGeminiCandidateMetadataText($candidate)
{
    $lines = array(
        'Candidate metadata:',
        'Lead ID: ' . (isset($candidate['lead_id']) ? (int) $candidate['lead_id'] : 0),
        'Name: ' . (isset($candidate['name']) ? sanitizeGeminiTextPart($candidate['name']) : ''),
        'Current experience years: ' . (isset($candidate['current_experience_years']) && $candidate['current_experience_years'] !== null ? (string) $candidate['current_experience_years'] : 'Not available'),
        'Original experience: ' . (isset($candidate['original_experience']) ? sanitizeGeminiTextPart($candidate['original_experience']) : ''),
        'Notice period: ' . (isset($candidate['notice_period']) ? sanitizeGeminiTextPart($candidate['notice_period']) : ''),
        'Skills: ' . (isset($candidate['skills']) ? sanitizeGeminiTextPart($candidate['skills']) : ''),
        'Email: ' . (isset($candidate['email']) ? sanitizeGeminiTextPart($candidate['email']) : ''),
        'Phone: ' . (isset($candidate['phone']) ? sanitizeGeminiTextPart($candidate['phone']) : '')
    );

    return implode("\n", $lines);
}

function getResumeIntelligenceLogTail($channel, $maxLines = 20)
{
    $channel = preg_replace('/[^a-zA-Z0-9_\-]/', '_', (string) $channel);
    if ($channel === '') {
        return array();
    }

    $logPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $channel . '.log';
    if (!is_file($logPath) || !is_readable($logPath)) {
        return array();
    }

    $lines = @file($logPath, FILE_IGNORE_NEW_LINES);
    if (!is_array($lines) || empty($lines)) {
        return array();
    }

    $maxLines = max(1, (int) $maxLines);
    return array_slice($lines, -1 * $maxLines);
}

function resumeStorageDirectories()
{
    $root = dirname(__DIR__);

    return array_values(array_unique(array(
        $root . DIRECTORY_SEPARATOR . 'resume',
        $root . DIRECTORY_SEPARATOR . 'Resume',
        $root . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'resume',
        $root . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'resumes'
    )));
}

function resolveResumeAbsolutePath($resumeFileName, $storedPath = '')
{
    $resumeFileName = basename(str_replace('\\', '/', (string) $resumeFileName));
    $storedPath = trim((string) $storedPath);

    if ($storedPath !== '' && is_file($storedPath) && is_readable($storedPath)) {
        return $storedPath;
    }

    if ($resumeFileName === '' || $resumeFileName === '.' || $resumeFileName === '..') {
        return '';
    }

    foreach (resumeStorageDirectories() as $directory) {
        $candidatePath = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $resumeFileName;
        if (is_file($candidatePath) && is_readable($candidatePath)) {
            return $candidatePath;
        }
    }

    return '';
}

function extractBaseExperienceYears($experienceText)
{
    $experienceText = trim((string) $experienceText);
    if ($experienceText === '') {
        return null;
    }

    $normalized = strtolower($experienceText);
    $normalized = str_replace(array('+', "\r", "\n", "\t"), ' ', $normalized);
    $normalized = preg_replace('/\s+/', ' ', $normalized);

    if (preg_match('/(\d+(?:\.\d+)?)\s*(?:years|year|yrs|yr)\s*(\d+(?:\.\d+)?)\s*(?:months|month|mos|mo)/i', $normalized, $matches)) {
        return (float) $matches[1] + (((float) $matches[2]) / 12);
    }

    if (preg_match('/(\d+(?:\.\d+)?)\s*(?:years|year|yrs|yr)/i', $normalized, $yearMatch)) {
        $years = (float) $yearMatch[1];
        $months = 0.0;
        if (preg_match('/(\d+(?:\.\d+)?)\s*(?:months|month|mos|mo)/i', $normalized, $monthMatch)) {
            $months = ((float) $monthMatch[1]) / 12;
        }
        return $years + $months;
    }

    if (preg_match('/(\d+(?:\.\d+)?)\s*(?:-|to)\s*(\d+(?:\.\d+)?)/i', $normalized, $rangeMatch)) {
        return (float) $rangeMatch[2];
    }

    if (preg_match('/^\d+(?:\.\d+)?$/', $normalized)) {
        $numericValue = (float) $normalized;
        $isWholeNumber = floor($numericValue) === $numericValue;

        // Heuristic for old tblleads data:
        // bare values above 7 are often stored as months, not years.
        if ($isWholeNumber && $numericValue > 7) {
            return $numericValue / 12;
        }

        return $numericValue;
    }

    if (preg_match('/(\d+(?:\.\d+)?)/', $normalized, $singleMatch)) {
        return (float) $singleMatch[1];
    }

    return null;
}

function calculateDynamicExperience($experienceText, $dateAdded, $asOfDate = null)
{
    $baseYears = extractBaseExperienceYears($experienceText);
    $result = array(
        'base_years' => $baseYears,
        'elapsed_years' => 0.0,
        'current_years' => $baseYears,
        'date_added' => $dateAdded,
        'base_label' => trim((string) $experienceText)
    );

    if ($baseYears === null) {
        return $result;
    }

    try {
        $startDate = new DateTime((string) $dateAdded);
        $currentDate = $asOfDate instanceof DateTime ? $asOfDate : new DateTime('now');
        if ($startDate <= $currentDate) {
            $seconds = $currentDate->getTimestamp() - $startDate->getTimestamp();
            $elapsedYears = $seconds > 0 ? ($seconds / 31557600) : 0;
            $result['elapsed_years'] = $elapsedYears;
            $result['current_years'] = $baseYears + $elapsedYears;
        }
    } catch (Exception $e) {
        $result['elapsed_years'] = 0.0;
        $result['current_years'] = $baseYears;
    }

    return $result;
}

function formatDynamicExperienceLabel($experienceText, $dateAdded)
{
    $experience = calculateDynamicExperience($experienceText, $dateAdded);
    if ($experience['base_years'] === null) {
        return trim((string) $experienceText) !== '' ? trim((string) $experienceText) : 'Not available';
    }

    $baseLabel = rtrim(rtrim(number_format((float) $experience['base_years'], 1, '.', ''), '0'), '.');
    $currentLabel = rtrim(rtrim(number_format((float) $experience['current_years'], 1, '.', ''), '0'), '.');

    return $baseLabel . ' yrs then -> ' . $currentLabel . ' yrs now';
}

function formatResumeApplyDate($dateAdded)
{
    $dateAdded = trim((string) $dateAdded);
    if ($dateAdded === '' || $dateAdded === '0000-00-00' || $dateAdded === '0000-00-00 00:00:00') {
        return 'Not available';
    }

    try {
        $date = new DateTime($dateAdded);
        return $date->format('d M Y');
    } catch (Exception $e) {
        return 'Not available';
    }
}

function buildResumeConversionInsight($dateAdded, $relevanceScore = 0)
{
    $label = 'Needs review';
    $reason = 'Application timing or match quality needs manual review.';

    $dateAdded = trim((string) $dateAdded);
    if ($dateAdded === '' || $dateAdded === '0000-00-00' || $dateAdded === '0000-00-00 00:00:00') {
        return array(
            'label' => $label,
            'reason' => $reason
        );
    }

    try {
        $appliedDate = new DateTime($dateAdded);
        $today = new DateTime('now');
        $days = (int) $appliedDate->diff($today)->format('%a');
        $score = (int) $relevanceScore;

        if ($days <= 45 && $score > 0) {
            return array(
                'label' => 'Most likely to convert',
                'reason' => 'Applied recently and matches the current search criteria.'
            );
        }

        if ($days > 45 && $score > 0) {
            return array(
                'label' => 'Less likely to convert',
                'reason' => 'Matches the current search criteria, but the application is older.'
            );
        }
    } catch (Exception $e) {
        return array(
            'label' => $label,
            'reason' => $reason
        );
    }

    return array(
        'label' => $label,
        'reason' => $reason
    );
}

function extractTextFromDocxResume($filePath)
{
    if (!class_exists('ZipArchive')) {
        return array(false, '', 'ZipArchive extension is not available.');
    }

    $zip = new ZipArchive();
    if ($zip->open($filePath) !== true) {
        return array(false, '', 'Unable to open DOCX archive.');
    }

    $documentXml = $zip->getFromName('word/document.xml');
    $zip->close();

    if ($documentXml === false) {
        return array(false, '', 'DOCX document.xml not found.');
    }

    $text = strip_tags(str_replace('</w:p>', "</w:p>\n", $documentXml));
    $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');

    return array(true, normalizeResumeWhitespace($text), null);
}

function extractTextFromPlainResume($filePath)
{
    $contents = @file_get_contents($filePath);
    if ($contents === false) {
        return array(false, '', 'Unable to read text file.');
    }

    return array(true, normalizeResumeWhitespace($contents), null);
}

function extractTextWithTika($filePath)
{
    $parserStatus = getResumeParserStatus();
    if (empty($parserStatus['tika_available'])) {
        return array(false, '', 'Apache Tika is not configured on this server yet.');
    }

    $javaPath = $parserStatus['config']['java_path'];
    $tikaJarPath = $parserStatus['config']['tika_jar_path'];

    $command = '"' . $javaPath . '" -jar "' . $tikaJarPath . '" --text "' . $filePath . '" 2>NUL';
    $output = @shell_exec($command);
    if (!is_string($output) || trim($output) === '') {
        return array(false, '', 'Apache Tika returned empty output for this file.');
    }

    return array(true, normalizeResumeWhitespace($output), null);
}

function decodePdfLiteralString($value)
{
    $result = '';
    $length = strlen($value);

    for ($i = 0; $i < $length; $i++) {
        $char = $value[$i];
        if ($char !== '\\') {
            $result .= $char;
            continue;
        }

        $i++;
        if ($i >= $length) {
            break;
        }

        $escaped = $value[$i];
        if ($escaped === 'n') {
            $result .= "\n";
        } elseif ($escaped === 'r') {
            $result .= "\r";
        } elseif ($escaped === 't') {
            $result .= "\t";
        } elseif ($escaped === 'b') {
            $result .= "\x08";
        } elseif ($escaped === 'f') {
            $result .= "\x0c";
        } elseif ($escaped === '(' || $escaped === ')' || $escaped === '\\') {
            $result .= $escaped;
        } elseif (ctype_digit($escaped)) {
            $octal = $escaped;
            for ($j = 0; $j < 2 && ($i + 1) < $length && ctype_digit($value[$i + 1]); $j++) {
                $i++;
                $octal .= $value[$i];
            }
            $result .= chr(octdec($octal));
        } else {
            $result .= $escaped;
        }
    }

    return $result;
}

function decodePdfHexString($value)
{
    $hex = preg_replace('/[^0-9A-Fa-f]/', '', $value);
    if ($hex === '') {
        return '';
    }

    if ((strlen($hex) % 2) === 1) {
        $hex .= '0';
    }

    $decoded = @hex2bin($hex);
    return $decoded === false ? '' : $decoded;
}

function extractTextFromPdfStreamTextBlocks($stream)
{
    $parts = array();

    if (preg_match_all('/\(([^()]*(?:\\\\.[^()]*)*)\)\s*Tj/s', $stream, $matches)) {
        foreach ($matches[1] as $match) {
            $parts[] = decodePdfLiteralString($match);
        }
    }

    if (preg_match_all('/<([0-9A-Fa-f\s]+)>\s*Tj/s', $stream, $matches)) {
        foreach ($matches[1] as $match) {
            $parts[] = decodePdfHexString($match);
        }
    }

    if (preg_match_all('/\[(.*?)\]\s*TJ/s', $stream, $matches)) {
        foreach ($matches[1] as $group) {
            if (preg_match_all('/\(([^()]*(?:\\\\.[^()]*)*)\)|<([0-9A-Fa-f\s]+)>/s', $group, $innerMatches, PREG_SET_ORDER)) {
                foreach ($innerMatches as $inner) {
                    if (!empty($inner[1])) {
                        $parts[] = decodePdfLiteralString($inner[1]);
                    } elseif (!empty($inner[2])) {
                        $parts[] = decodePdfHexString($inner[2]);
                    }
                }
            }
        }
    }

    $text = implode(' ', $parts);
    $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]+/', ' ', (string) $text);

    return normalizeResumeWhitespace($text);
}

function extractTextFromPdfResume($filePath)
{
    $contents = @file_get_contents($filePath);
    if ($contents === false || $contents === '') {
        return array(false, '', 'Unable to read PDF file.');
    }

    $extractedParts = array();
    $textBlockFound = false;

    if (preg_match_all('/<<(.*?)>>\s*stream\s*(.*?)\s*endstream/s', $contents, $streams, PREG_SET_ORDER)) {
        foreach ($streams as $streamMatch) {
            $dictionary = isset($streamMatch[1]) ? $streamMatch[1] : '';
            $streamData = isset($streamMatch[2]) ? $streamMatch[2] : '';
            $decodedStream = $streamData;

            if (stripos($dictionary, '/FlateDecode') !== false) {
                $inflated = @gzuncompress($streamData);
                if ($inflated === false) {
                    $inflated = @gzinflate($streamData);
                }
                if ($inflated === false && substr($streamData, 0, 2) === "\x78\x9c") {
                    $inflated = @gzuncompress($streamData);
                }
                if ($inflated !== false && is_string($inflated)) {
                    $decodedStream = $inflated;
                }
            }

            if (strpos($decodedStream, 'BT') !== false && strpos($decodedStream, 'ET') !== false) {
                $textBlockFound = true;
                if (preg_match_all('/BT(.*?)ET/s', $decodedStream, $blocks)) {
                    foreach ($blocks[1] as $block) {
                        $blockText = extractTextFromPdfStreamTextBlocks($block);
                        if ($blockText !== '') {
                            $extractedParts[] = $blockText;
                        }
                    }
                }
            }
        }
    }

    $text = normalizeResumeWhitespace(implode("\n", $extractedParts));

    if ($text !== '' && strlen($text) >= 40) {
        return array(true, $text, null);
    }

    if ($textBlockFound) {
        return array(false, '', 'PDF text blocks were found but could not be decoded cleanly.');
    }

    return array(false, '', 'No embedded text was found in this PDF. It is likely a scanned/image PDF and will need OCR.');
}

function detectResumeSkills($text)
{
    $keywords = array(
        'seo' => 'SEO',
        'search engine optimisation' => 'SEO',
        'google ads' => 'Google Ads',
        'facebook ads' => 'Facebook Ads',
        'meta ads' => 'Meta Ads',
        'social media' => 'Social Media',
        'content writing' => 'Content Writing',
        'copywriting' => 'Copywriting',
        'graphic design' => 'Graphic Design',
        'motion graphics' => 'Motion Graphics',
        'video editing' => 'Video Editing',
        'wordpress' => 'WordPress',
        'php' => 'PHP',
        'laravel' => 'Laravel',
        'mysql' => 'MySQL',
        'javascript' => 'JavaScript',
        'react' => 'React',
        'html' => 'HTML',
        'css' => 'CSS',
        'client servicing' => 'Client Servicing',
        'business development' => 'Business Development',
        'hr' => 'HR',
        'recruitment' => 'Recruitment'
    );

    $normalized = strtolower($text);
    $skills = array();

    foreach ($keywords as $needle => $label) {
        if (strpos($normalized, $needle) !== false) {
            $skills[$label] = true;
        }
    }

    return array_keys($skills);
}

function extractResumeEmail($text, $fallbackEmail = '')
{
    if (preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $text, $matches)) {
        return $matches[0];
    }

    return $fallbackEmail;
}

function extractResumePhone($text, $fallbackPhone = '')
{
    if (preg_match('/(?:\+?\d[\d\-\s]{8,}\d)/', $text, $matches)) {
        return trim($matches[0]);
    }

    return $fallbackPhone;
}

function buildResumeDocumentPayload($lead, $resumeDir)
{
    $fileName = isset($lead['resume']) ? trim((string) $lead['resume']) : '';
    $leadId = isset($lead['id']) ? (int) $lead['id'] : 0;
    $leadName = isset($lead['name']) ? (string) $lead['name'] : '';
    $leadEmail = isset($lead['email']) ? (string) $lead['email'] : '';
    $leadPhone = isset($lead['phonenumber']) ? (string) $lead['phonenumber'] : '';
    $safeFileName = basename(str_replace('\\', '/', $fileName));
    $absolutePath = resolveResumeAbsolutePath($safeFileName, rtrim($resumeDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $safeFileName);
    $extension = strtolower(pathinfo($safeFileName, PATHINFO_EXTENSION));

    $payload = array(
        'lead_id' => $leadId,
        'lead_name' => $leadName,
        'original_resume_name' => $safeFileName,
        'file_path' => $absolutePath,
        'file_extension' => $extension,
        'file_size' => null,
        'file_hash' => null,
        'extraction_status' => 'pending',
        'extraction_engine' => 'resume_intelligence_v1',
        'raw_text' => null,
        'extracted_name' => $leadName,
        'extracted_email' => $leadEmail,
        'extracted_phone' => $leadPhone,
        'extracted_skills' => null,
        'metadata_json' => null,
        'last_error' => null,
        'extracted_at' => null
    );

    if ($safeFileName === '') {
        $payload['extraction_status'] = 'missing';
        $payload['last_error'] = 'No resume file name is stored for this lead.';
        return $payload;
    }

    if ($absolutePath === '' || !is_file($absolutePath)) {
        $payload['extraction_status'] = 'missing';
        $payload['last_error'] = 'Resume file is missing from the resume directory.';
        return $payload;
    }

    $payload['file_size'] = @filesize($absolutePath);
    $payload['file_hash'] = @hash_file('sha256', $absolutePath) ?: null;

    if ($extension === 'docx') {
        list($ok, $text, $error) = extractTextFromDocxResume($absolutePath);
        if ($ok) {
            $skills = detectResumeSkills($text);
            $payload['raw_text'] = $text;
            $payload['extraction_status'] = 'completed';
            $payload['extracted_email'] = extractResumeEmail($text, $leadEmail);
            $payload['extracted_phone'] = extractResumePhone($text, $leadPhone);
            $payload['extracted_skills'] = !empty($skills) ? implode(', ', $skills) : null;
            $payload['metadata_json'] = json_encode(
                array(
                    'line_count' => substr_count($text, "\n") + 1,
                    'skill_count' => count($skills),
                    'source_type' => 'docx'
                ),
                JSON_UNESCAPED_SLASHES
            );
            $payload['extracted_at'] = date('Y-m-d H:i:s');
        } else {
            $payload['extraction_status'] = 'error';
            $payload['last_error'] = $error;
        }

        return $payload;
    }

    if ($extension === 'txt') {
        list($ok, $text, $error) = extractTextFromPlainResume($absolutePath);
        if ($ok) {
            $skills = detectResumeSkills($text);
            $payload['raw_text'] = $text;
            $payload['extraction_status'] = 'completed';
            $payload['extracted_email'] = extractResumeEmail($text, $leadEmail);
            $payload['extracted_phone'] = extractResumePhone($text, $leadPhone);
            $payload['extracted_skills'] = !empty($skills) ? implode(', ', $skills) : null;
            $payload['metadata_json'] = json_encode(
                array(
                    'line_count' => substr_count($text, "\n") + 1,
                    'skill_count' => count($skills),
                    'source_type' => 'txt'
                ),
                JSON_UNESCAPED_SLASHES
            );
            $payload['extracted_at'] = date('Y-m-d H:i:s');
        } else {
            $payload['extraction_status'] = 'error';
            $payload['last_error'] = $error;
        }

        return $payload;
    }

    if ($extension === 'pdf') {
        $parserStatus = getResumeParserStatus();
        if (!empty($parserStatus['tika_available'])) {
            list($ok, $text, $error) = extractTextWithTika($absolutePath);
        } else {
            list($ok, $text, $error) = extractTextFromPdfResume($absolutePath);
        }
        if ($ok) {
            $skills = detectResumeSkills($text);
            $payload['raw_text'] = $text;
            $payload['extraction_status'] = 'completed';
            $payload['extracted_email'] = extractResumeEmail($text, $leadEmail);
            $payload['extracted_phone'] = extractResumePhone($text, $leadPhone);
            $payload['extracted_skills'] = !empty($skills) ? implode(', ', $skills) : null;
            $payload['metadata_json'] = json_encode(
                array(
                    'line_count' => substr_count($text, "\n") + 1,
                    'skill_count' => count($skills),
                    'source_type' => 'pdf',
                    'parser' => !empty($parserStatus['tika_available']) ? 'apache_tika' : 'native_php_pdf_text'
                ),
                JSON_UNESCAPED_SLASHES
            );
            $payload['extracted_at'] = date('Y-m-d H:i:s');
        } else {
            $payload['extraction_status'] = 'pending';
            $payload['last_error'] = $error;
            $payload['metadata_json'] = json_encode(
                array(
                    'source_type' => 'pdf',
                    'parser' => !empty($parserStatus['tika_available']) ? 'apache_tika' : 'native_php_pdf_text',
                    'needs_ocr' => true
                ),
                JSON_UNESCAPED_SLASHES
            );
        }
        return $payload;
    }

    if ($extension === 'doc') {
        $parserStatus = getResumeParserStatus();
        if (!empty($parserStatus['tika_available'])) {
            list($ok, $text, $error) = extractTextWithTika($absolutePath);
            if ($ok) {
                $skills = detectResumeSkills($text);
                $payload['raw_text'] = $text;
                $payload['extraction_status'] = 'completed';
                $payload['extracted_email'] = extractResumeEmail($text, $leadEmail);
                $payload['extracted_phone'] = extractResumePhone($text, $leadPhone);
                $payload['extracted_skills'] = !empty($skills) ? implode(', ', $skills) : null;
                $payload['metadata_json'] = json_encode(
                    array(
                        'line_count' => substr_count($text, "\n") + 1,
                        'skill_count' => count($skills),
                        'source_type' => 'doc',
                        'parser' => 'apache_tika'
                    ),
                    JSON_UNESCAPED_SLASHES
                );
                $payload['extracted_at'] = date('Y-m-d H:i:s');
            } else {
                $payload['extraction_status'] = 'pending';
                $payload['last_error'] = $error;
                $payload['metadata_json'] = json_encode(
                    array(
                        'source_type' => 'doc',
                        'parser' => 'apache_tika'
                    ),
                    JSON_UNESCAPED_SLASHES
                );
            }
        } else {
            $payload['extraction_status'] = 'unsupported';
            $payload['last_error'] = 'Legacy DOC parsing is not supported without Apache Tika.';
            $payload['metadata_json'] = json_encode(array('source_type' => 'doc'), JSON_UNESCAPED_SLASHES);
        }
        return $payload;
    }

    $payload['extraction_status'] = 'unsupported';
    $payload['last_error'] = 'Unsupported file type: ' . $extension;
    $payload['metadata_json'] = json_encode(array('source_type' => $extension), JSON_UNESCAPED_SLASHES);

    return $payload;
}

function upsertResumeDocument($connect, $payload)
{
    $sql = "INSERT INTO resume_documents (
                lead_id,
                lead_name,
                original_resume_name,
                file_path,
                file_extension,
                file_size,
                file_hash,
                extraction_status,
                extraction_engine,
                raw_text,
                extracted_name,
                extracted_email,
                extracted_phone,
                extracted_skills,
                metadata_json,
                last_error,
                extracted_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                lead_name = VALUES(lead_name),
                file_path = VALUES(file_path),
                file_extension = VALUES(file_extension),
                file_size = VALUES(file_size),
                file_hash = VALUES(file_hash),
                extraction_status = VALUES(extraction_status),
                extraction_engine = VALUES(extraction_engine),
                raw_text = VALUES(raw_text),
                extracted_name = VALUES(extracted_name),
                extracted_email = VALUES(extracted_email),
                extracted_phone = VALUES(extracted_phone),
                extracted_skills = VALUES(extracted_skills),
                metadata_json = VALUES(metadata_json),
                last_error = VALUES(last_error),
                extracted_at = VALUES(extracted_at)";

    $stmt = $connect->prepare($sql);
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        'isssssissssssssss',
        $payload['lead_id'],
        $payload['lead_name'],
        $payload['original_resume_name'],
        $payload['file_path'],
        $payload['file_extension'],
        $payload['file_size'],
        $payload['file_hash'],
        $payload['extraction_status'],
        $payload['extraction_engine'],
        $payload['raw_text'],
        $payload['extracted_name'],
        $payload['extracted_email'],
        $payload['extracted_phone'],
        $payload['extracted_skills'],
        $payload['metadata_json'],
        $payload['last_error'],
        $payload['extracted_at']
    );

    $result = $stmt->execute();
    $stmt->close();

    return $result;
}

function syncResumeDocuments($connect, $limit = 50)
{
    if (function_exists('set_time_limit')) {
        @set_time_limit(30);
    }

    $summary = array(
        'processed' => 0,
        'completed' => 0,
        'pending' => 0,
        'unsupported' => 0,
        'missing' => 0,
        'error' => 0,
        'messages' => array()
    );

    if (!ensureResumeDocumentsTable($connect)) {
        $summary['messages'][] = 'Could not create or access the resume_documents table.';
        return $summary;
    }

    $limit = max(1, (int) $limit);
    $queryWindow = max($limit * 8, 200);
    $sql = "SELECT t.id, t.name, t.email, t.phonenumber, t.resume
            FROM tblleads t
            LEFT JOIN resume_documents d
                ON d.lead_id = t.id
               AND d.original_resume_name = t.resume
            WHERE t.resume IS NOT NULL
              AND TRIM(t.resume) <> ''
              AND d.id IS NULL
            ORDER BY t.id DESC
            LIMIT " . $queryWindow;

    $result = $connect->query($sql);
    if (!$result) {
        $summary['messages'][] = 'Could not read resume leads from tblleads.';
        return $summary;
    }

    $resumeDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resume';
    $prioritizedLeads = array();
    $fallbackLeads = array();

    while ($lead = $result->fetch_assoc()) {
        $resumeFile = basename(str_replace('\\', '/', (string) $lead['resume']));
        $absolutePath = $resumeDir . DIRECTORY_SEPARATOR . $resumeFile;
        if (is_file($absolutePath)) {
            $prioritizedLeads[] = $lead;
        } else {
            $fallbackLeads[] = $lead;
        }
    }

    $result->free();

    $selectedLeads = array_slice(array_merge($prioritizedLeads, $fallbackLeads), 0, $limit);

    foreach ($selectedLeads as $lead) {
        $payload = buildResumeDocumentPayload($lead, $resumeDir);
        if (upsertResumeDocument($connect, $payload)) {
            $summary['processed']++;
            $status = $payload['extraction_status'];
            if (isset($summary[$status])) {
                $summary[$status]++;
            }
        } else {
            $summary['processed']++;
            $summary['error']++;
            $summary['messages'][] = 'Failed to save resume document for lead #' . (int) $lead['id'] . '.';
        }
    }

    if ($summary['processed'] === 0) {
        $summary['messages'][] = 'No new resumes were available in the next batch.';
    }

    return $summary;
}

function fetchResumeQueueStats($connect)
{
    $stats = array(
        'with_resume' => 0,
        'indexed' => 0,
        'remaining' => 0
    );

    if (!ensureResumeDocumentsTable($connect)) {
        return $stats;
    }

    $withResumeResult = $connect->query("SELECT COUNT(*) AS total FROM tblleads WHERE resume IS NOT NULL AND TRIM(resume) <> ''");
    if ($withResumeResult) {
        $row = $withResumeResult->fetch_assoc();
        $stats['with_resume'] = (int) $row['total'];
        $withResumeResult->free();
    }

    $indexedResult = $connect->query("SELECT COUNT(*) AS total FROM resume_documents");
    if ($indexedResult) {
        $row = $indexedResult->fetch_assoc();
        $stats['indexed'] = (int) $row['total'];
        $indexedResult->free();
    }

    $stats['remaining'] = max(0, $stats['with_resume'] - $stats['indexed']);

    return $stats;
}

function fetchResumeDocumentStats($connect)
{
    $stats = array(
        'total' => 0,
        'completed' => 0,
        'pending' => 0,
        'unsupported' => 0,
        'missing' => 0,
        'error' => 0
    );

    if (!ensureResumeDocumentsTable($connect)) {
        return $stats;
    }

    $result = $connect->query("SELECT extraction_status, COUNT(*) AS total FROM resume_documents GROUP BY extraction_status");
    if (!$result) {
        return $stats;
    }

    while ($row = $result->fetch_assoc()) {
        $status = $row['extraction_status'];
        $count = (int) $row['total'];
        $stats['total'] += $count;
        if (isset($stats[$status])) {
            $stats[$status] = $count;
        }
    }

    $result->free();

    return $stats;
}

function fetchRecentResumeDocuments($connect, $limit = 25)
{
    $rows = array();

    if (!ensureResumeDocumentsTable($connect)) {
        return $rows;
    }

    $limit = max(1, (int) $limit);
    $sql = "SELECT id, lead_id, lead_name, original_resume_name, file_extension, extraction_status, extracted_email, extracted_phone, extracted_skills, updated_at, last_error
            FROM resume_documents
            ORDER BY updated_at DESC
            LIMIT " . $limit;
    $result = $connect->query($sql);

    if (!$result) {
        return $rows;
    }

    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $result->free();

    return $rows;
}

function fetchResumeRoleOptions($connect)
{
    $roles = array();

    if (!($connect instanceof mysqli)) {
        return $roles;
    }

    $result = $connect->query("SELECT id, name FROM tblrole ORDER BY name ASC");
    if (!$result) {
        return $roles;
    }

    while ($row = $result->fetch_assoc()) {
        $roles[] = $row;
    }

    $result->free();

    return $roles;
}

function fetchResumeSourceOptions($connect)
{
    $sources = array();

    if (!($connect instanceof mysqli)) {
        return $sources;
    }

    $result = $connect->query("SELECT id, name FROM tblleadssources ORDER BY name ASC");
    if (!$result) {
        return $sources;
    }

    while ($row = $result->fetch_assoc()) {
        $sources[] = $row;
    }

    $result->free();

    return $sources;
}

function fetchResumeLeadStatusOptions($connect)
{
    $statuses = array();

    if (!($connect instanceof mysqli)) {
        return $statuses;
    }

    $result = $connect->query("SELECT id, name FROM tblleadsstatus ORDER BY name ASC");
    if (!$result) {
        return $statuses;
    }

    while ($row = $result->fetch_assoc()) {
        $statuses[] = $row;
    }

    $result->free();

    return $statuses;
}

function resumeSearchAnnualSalarySql($columnName)
{
    $columnName = trim((string) $columnName);
    $cleanValue = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(COALESCE(" . $columnName . ", ''))), ',', ''), ' ', ''), 'rs.', ''), 'rs', ''), 'lakhs', ''), 'lakh', ''), 'lacs', ''), 'lac', ''), 'lpa', ''), 'lps', ''), 'l', '')";

    return "(CASE
        WHEN TRIM(LOWER(COALESCE(" . $columnName . ", ''))) IN ('', 'na', 'n/a', 'none', 'null', '-') THEN NULL
        WHEN " . $cleanValue . " REGEXP '^[0-9]+(\\\\.[0-9]+)?$' THEN
            CASE
                WHEN CAST(" . $cleanValue . " AS DECIMAL(12,2)) < 100 THEN CAST(" . $cleanValue . " AS DECIMAL(12,2)) * 100000
                WHEN CAST(" . $cleanValue . " AS DECIMAL(12,2)) < 100000 THEN CAST(" . $cleanValue . " AS DECIMAL(12,2)) * 12
                ELSE CAST(" . $cleanValue . " AS DECIMAL(12,2))
            END
        ELSE NULL
    END)";
}

function resumeSearchNormalizeSalaryThreshold($value)
{
    $value = strtolower(trim((string) $value));
    if ($value === '') {
        return null;
    }

    $value = str_replace(array(',', ' ', 'rs.', 'rs'), '', $value);

    if (preg_match('/^(\d+(?:\.\d+)?)(l|lp|lac|lacs|lakh|lakhs|lpa|lps)$/', $value, $matches)) {
        return (float) $matches[1] * 100000;
    }

    if (preg_match('/^(\d+(?:\.\d+)?)(k)$/', $value, $matches)) {
        return (float) $matches[1] * 1000;
    }

    if (!preg_match('/^\d+(?:\.\d+)?$/', $value)) {
        return null;
    }

    $amount = (float) $value;
    if ($amount < 100) {
        return $amount * 100000;
    }
    if ($amount < 100000) {
        return $amount * 12;
    }

    return $amount;
}

function resumeSearchNormalizedExperienceSql($columnName)
{
    $columnName = trim((string) $columnName);
    $cleanValue = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(COALESCE(" . $columnName . ", ''))), 'years', ''), 'year', ''), 'yrs', ''), 'yr', ''), ' ', '')";

    return "(CASE
        WHEN TRIM(LOWER(COALESCE(" . $columnName . ", ''))) IN ('', 'na', 'n/a', 'none', 'null', '-') THEN NULL
        WHEN " . $cleanValue . " REGEXP '^[0-9]+(\\\\.[0-9]+)?$' THEN
            CASE
                WHEN " . $cleanValue . " LIKE '%.%' THEN CAST(" . $cleanValue . " AS DECIMAL(12,2))
                WHEN CAST(" . $cleanValue . " AS UNSIGNED) >= 8 THEN CAST(" . $cleanValue . " AS DECIMAL(12,2)) / 12
                ELSE CAST(" . $cleanValue . " AS DECIMAL(12,2))
            END
        ELSE NULL
    END)";
}

function fetchResumeSearchResults($connect, $filters = array(), $page = 1, $perPage = 25)
{
    $empty = array(
        'rows' => array(),
        'total' => 0,
        'page' => 1,
        'per_page' => 25,
        'pages' => 1
    );

    if (!ensureResumeDocumentsTable($connect)) {
        return $empty;
    }

    $page = max(1, (int) $page);
    $perPage = max(1, min(100, (int) $perPage));

    $where = array("1=1");
    $relevanceParts = array();

    $search = isset($filters['q']) ? trim((string) $filters['q']) : '';
    if ($search !== '') {
        $escaped = $connect->real_escape_string($search);
        $like = "'%" . $escaped . "%'";
        $where[] = "(rd.lead_name LIKE $like
            OR rd.original_resume_name LIKE $like
            OR rd.extracted_email LIKE $like
            OR rd.extracted_phone LIKE $like
            OR rd.extracted_skills LIKE $like
            OR rd.raw_text LIKE $like
            OR l.email LIKE $like
            OR l.phonenumber LIKE $like)";
        $relevanceParts[] = "CASE WHEN rd.lead_name LIKE $like THEN 40 ELSE 0 END";
        $relevanceParts[] = "CASE WHEN rd.extracted_skills LIKE $like THEN 35 ELSE 0 END";
        $relevanceParts[] = "CASE WHEN rd.raw_text LIKE $like THEN 25 ELSE 0 END";
        $relevanceParts[] = "CASE WHEN rd.extracted_email LIKE $like OR l.email LIKE $like THEN 10 ELSE 0 END";
        $relevanceParts[] = "CASE WHEN rd.extracted_phone LIKE $like OR l.phonenumber LIKE $like THEN 10 ELSE 0 END";
    }

    $keywords = isset($filters['keywords']) && is_array($filters['keywords']) ? $filters['keywords'] : array();
    $keywordWhere = array();
    foreach ($keywords as $keyword) {
        $keyword = trim((string) $keyword);
        if ($keyword === '') {
            continue;
        }
        $escapedKeyword = $connect->real_escape_string($keyword);
        $keywordLike = "'%" . $escapedKeyword . "%'";
        $keywordWhere[] = "(rd.extracted_skills LIKE $keywordLike OR rd.raw_text LIKE $keywordLike OR l.skillset LIKE $keywordLike OR l.cjtitle LIKE $keywordLike)";
        $relevanceParts[] = "CASE WHEN rd.extracted_skills LIKE $keywordLike OR l.skillset LIKE $keywordLike THEN 30 ELSE 0 END";
        $relevanceParts[] = "CASE WHEN rd.raw_text LIKE $keywordLike THEN 15 ELSE 0 END";
    }
    if (!empty($keywordWhere)) {
        $where[] = "(" . implode(" OR ", $keywordWhere) . ")";
    }

    $status = isset($filters['status']) ? trim((string) $filters['status']) : '';
    if ($status !== '') {
        $where[] = "rd.extraction_status = '" . $connect->real_escape_string($status) . "'";
    }

    $role = isset($filters['role']) ? (int) $filters['role'] : 0;
    if ($role > 0) {
        $where[] = "FIND_IN_SET(" . $role . ", COALESCE(l.roles, ''))";
    }

    $experienceMin = isset($filters['experience_min']) ? trim((string) $filters['experience_min']) : '';
    $experienceMax = isset($filters['experience_max']) ? trim((string) $filters['experience_max']) : '';
    if ($experienceMin !== '' && preg_match('/\d+(?:\.\d+)?/', $experienceMin, $experienceMinMatch)) {
        $where[] = resumeSearchNormalizedExperienceSql('l.experiance') . " >= " . (float) $experienceMinMatch[0];
    }
    if ($experienceMax !== '' && preg_match('/\d+(?:\.\d+)?/', $experienceMax, $experienceMaxMatch)) {
        $where[] = resumeSearchNormalizedExperienceSql('l.experiance') . " <= " . (float) $experienceMaxMatch[0];
    }

    $experience = isset($filters['experience']) ? trim((string) $filters['experience']) : '';
    if ($experience !== '' && $experienceMin === '' && $experienceMax === '') {
        if (preg_match('/\d+(?:\.\d+)?/', $experience, $experienceMatch)) {
            $where[] = resumeSearchNormalizedExperienceSql('l.experiance') . " >= " . (float) $experienceMatch[0];
        } else {
            $escapedExperience = $connect->real_escape_string($experience);
            $where[] = "COALESCE(l.experiance, '') LIKE '%" . $escapedExperience . "%'";
        }
    }

    $leadStatus = isset($filters['lead_status']) ? (int) $filters['lead_status'] : 0;
    if ($leadStatus > 0) {
        $where[] = "l.status = " . $leadStatus;
    }

    $source = isset($filters['source']) ? (int) $filters['source'] : 0;
    if ($source > 0) {
        $where[] = "l.source = " . $source;
    }

    $city = isset($filters['city']) ? trim((string) $filters['city']) : '';
    if ($city !== '') {
        $where[] = "l.city LIKE '%" . $connect->real_escape_string($city) . "%'";
    }

    $relocate = isset($filters['relocate']) ? trim((string) $filters['relocate']) : '';
    if ($relocate !== '') {
        $where[] = "l.willing_to_relocate = '" . $connect->real_escape_string($relocate) . "'";
    }

    $currentCtc = isset($filters['current_ctc']) ? trim((string) $filters['current_ctc']) : '';
    if ($currentCtc !== '') {
        $normalizedCurrentCtc = resumeSearchNormalizeSalaryThreshold($currentCtc);
        if ($normalizedCurrentCtc !== null) {
            $where[] = resumeSearchAnnualSalarySql('l.csalary') . " <= " . (float) $normalizedCurrentCtc;
        }
    }

    $expectedCtc = isset($filters['expected_ctc']) ? trim((string) $filters['expected_ctc']) : '';
    if ($expectedCtc !== '') {
        $normalizedExpectedCtc = resumeSearchNormalizeSalaryThreshold($expectedCtc);
        if ($normalizedExpectedCtc !== null) {
            $where[] = resumeSearchAnnualSalarySql('l.esalary') . " <= " . (float) $normalizedExpectedCtc;
        }
    }

    $noticePeriod = isset($filters['notice_period']) ? trim((string) $filters['notice_period']) : '';
    if ($noticePeriod !== '') {
        if (preg_match('/^\d+$/', $noticePeriod)) {
            $cleanNotice = "REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(COALESCE(l.nperiod, ''))), 'days', ''), 'day', ''), ' ', ''), '+', '')";
            $where[] = "(" . $cleanNotice . " REGEXP '^[0-9]+$' AND CAST(" . $cleanNotice . " AS UNSIGNED) <= " . (int) $noticePeriod . ")";
        } else {
            $where[] = "COALESCE(l.nperiod, '') LIKE '%" . $connect->real_escape_string($noticePeriod) . "%'";
        }
    }

    $interval = isset($filters['interval']) ? trim((string) $filters['interval']) : '';
    if ($interval === 'last-seven') {
        $where[] = "DATE(l.dateadded) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    } elseif ($interval === 'last-thirty') {
        $where[] = "DATE(l.dateadded) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    } elseif ($interval === 'last-month') {
        $where[] = "DATE(l.dateadded) >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
    }

    $whereSql = implode(' AND ', $where);
    $fromSql = " FROM resume_documents rd
        LEFT JOIN tblleads l ON l.id = rd.lead_id
        LEFT JOIN tblleadsstatus ls ON ls.id = l.status
        LEFT JOIN tblleadssources src ON src.id = l.source
        WHERE $whereSql";

    $countResult = $connect->query("SELECT COUNT(*) AS total" . $fromSql);
    if (!$countResult) {
        return $empty;
    }

    $countRow = $countResult->fetch_assoc();
    $total = isset($countRow['total']) ? (int) $countRow['total'] : 0;
    $countResult->free();

    $pages = max(1, (int) ceil($total / $perPage));
    if ($page > $pages) {
        $page = $pages;
    }

    $offset = ($page - 1) * $perPage;

    $relevanceSql = empty($relevanceParts) ? "0" : implode(' + ', $relevanceParts);

    $sql = "SELECT
                rd.id,
                rd.lead_id,
                rd.lead_name,
                rd.original_resume_name,
                rd.file_path,
                rd.file_extension,
                rd.extraction_status,
                rd.extracted_email,
                rd.extracted_phone,
                rd.extracted_skills,
                rd.raw_text,
                rd.updated_at,
                rd.last_error,
                l.email AS lead_email,
                l.phonenumber AS lead_phone,
                l.city AS city,
                l.city AS lead_city,
                l.country AS country,
                l.country AS lead_country,
                l.willing_to_relocate AS willing_to_relocate,
                l.willing_to_relocate AS lead_willing_to_relocate,
                l.experiance AS experiance,
                l.experiance AS lead_experiance,
                l.qualification AS qualification,
                l.qualification AS lead_qualification,
                l.cjtitle AS cjtitle,
                l.cjtitle AS lead_cjtitle,
                l.cemployer AS cemployer,
                l.cemployer AS lead_cemployer,
                l.csalary AS csalary,
                l.csalary AS lead_csalary,
                l.esalary AS esalary,
                l.esalary AS lead_esalary,
                l.skillset AS skillset,
                l.skillset AS lead_skillset,
                l.ainfo AS ainfo,
                l.ainfo AS lead_ainfo,
                l.nperiod AS nperiod,
                l.nperiod AS lead_nperiod,
                l.dateadded AS dateadded,
                l.dateadded AS lead_dateadded,
                l.roles AS roles,
                l.roles AS lead_roles,
                ls.name AS lead_status_name,
                src.name AS lead_source_name,
                (" . $relevanceSql . ") AS relevance_score" .
            $fromSql .
            " ORDER BY relevance_score DESC, l.dateadded DESC, rd.updated_at DESC, rd.id DESC LIMIT " . $offset . ", " . $perPage;

    $result = $connect->query($sql);
    if (!$result) {
        return $empty;
    }

    $rows = array();
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $result->free();

    if (!empty($rows)) {
        $leadIds = array();
        foreach ($rows as $row) {
            if (!empty($row['lead_id'])) {
                $leadIds[] = (int) $row['lead_id'];
            }
        }
        $leadIds = array_values(array_unique(array_filter($leadIds)));

        if (!empty($leadIds)) {
            $leadResult = $connect->query("SELECT id, city, country, willing_to_relocate, experiance, qualification, cjtitle, cemployer, csalary, esalary, skillset, ainfo, nperiod, dateadded, roles, email, phonenumber, status, source FROM tblleads WHERE id IN (" . implode(',', $leadIds) . ")");
            if ($leadResult) {
                $leadMap = array();
                while ($leadRow = $leadResult->fetch_assoc()) {
                    $leadMap[(int) $leadRow['id']] = $leadRow;
                }
                $leadResult->free();

                foreach ($rows as $index => $row) {
                    $leadId = isset($row['lead_id']) ? (int) $row['lead_id'] : 0;
                    if ($leadId <= 0 || empty($leadMap[$leadId])) {
                        continue;
                    }

                    $leadRow = $leadMap[$leadId];
                    foreach (array('city', 'country', 'willing_to_relocate', 'experiance', 'qualification', 'cjtitle', 'cemployer', 'csalary', 'esalary', 'skillset', 'ainfo', 'nperiod', 'dateadded', 'roles') as $field) {
                        $rows[$index]['lead_' . $field] = isset($leadRow[$field]) ? $leadRow[$field] : '';
                        if (!isset($rows[$index][$field]) || $rows[$index][$field] === null || trim((string) $rows[$index][$field]) === '') {
                            $rows[$index][$field] = isset($leadRow[$field]) ? $leadRow[$field] : '';
                        }
                    }
                    if (!isset($rows[$index]['lead_email']) || trim((string) $rows[$index]['lead_email']) === '') {
                        $rows[$index]['lead_email'] = isset($leadRow['email']) ? $leadRow['email'] : '';
                    }
                    if (!isset($rows[$index]['lead_phone']) || trim((string) $rows[$index]['lead_phone']) === '') {
                        $rows[$index]['lead_phone'] = isset($leadRow['phonenumber']) ? $leadRow['phonenumber'] : '';
                    }
                }
            }
        }
    }

    return array(
        'rows' => $rows,
        'total' => $total,
        'page' => $page,
        'per_page' => $perPage,
        'pages' => $pages
    );
}

function fetchResumeLeadSearchResults($connect, $filters = array(), $page = 1, $perPage = 25)
{
    $empty = array(
        'rows' => array(),
        'total' => 0,
        'page' => 1,
        'per_page' => 25,
        'pages' => 1
    );

    if (!ensureResumeDocumentsTable($connect)) {
        return $empty;
    }

    $page = max(1, (int) $page);
    $perPage = max(1, min(100, (int) $perPage));

    $where = array("t.resume IS NOT NULL", "TRIM(t.resume) <> ''");
    $relevanceParts = array();

    $search = isset($filters['q']) ? trim((string) $filters['q']) : '';
    if ($search !== '') {
        $escaped = $connect->real_escape_string($search);
        $like = "'%" . $escaped . "%'";
        $where[] = "(t.name LIKE $like
            OR t.resume LIKE $like
            OR t.email LIKE $like
            OR t.phonenumber LIKE $like
            OR t.skillset LIKE $like
            OR t.ainfo LIKE $like
            OR rd.extracted_skills LIKE $like
            OR rd.raw_text LIKE $like)";
        $relevanceParts[] = "CASE WHEN t.name LIKE $like THEN 40 ELSE 0 END";
        $relevanceParts[] = "CASE WHEN t.skillset LIKE $like OR rd.extracted_skills LIKE $like THEN 35 ELSE 0 END";
        $relevanceParts[] = "CASE WHEN t.ainfo LIKE $like OR rd.raw_text LIKE $like THEN 25 ELSE 0 END";
        $relevanceParts[] = "CASE WHEN t.email LIKE $like OR t.phonenumber LIKE $like THEN 10 ELSE 0 END";
    }

    $keywords = isset($filters['keywords']) && is_array($filters['keywords']) ? $filters['keywords'] : array();
    $keywordWhere = array();
    foreach ($keywords as $keyword) {
        $keyword = trim((string) $keyword);
        if ($keyword === '') {
            continue;
        }
        $escapedKeyword = $connect->real_escape_string($keyword);
        $keywordLike = "'%" . $escapedKeyword . "%'";
        $keywordWhere[] = "(t.skillset LIKE $keywordLike OR t.cjtitle LIKE $keywordLike OR t.ainfo LIKE $keywordLike OR rd.extracted_skills LIKE $keywordLike OR rd.raw_text LIKE $keywordLike)";
        $relevanceParts[] = "CASE WHEN t.skillset LIKE $keywordLike OR rd.extracted_skills LIKE $keywordLike THEN 30 ELSE 0 END";
        $relevanceParts[] = "CASE WHEN t.ainfo LIKE $keywordLike OR rd.raw_text LIKE $keywordLike THEN 15 ELSE 0 END";
    }
    if (!empty($keywordWhere)) {
        $where[] = "(" . implode(" OR ", $keywordWhere) . ")";
    }

    $role = isset($filters['role']) ? (int) $filters['role'] : 0;
    if ($role > 0) {
        $where[] = "FIND_IN_SET(" . $role . ", COALESCE(t.roles, ''))";
    }

    $experienceMin = isset($filters['experience_min']) ? trim((string) $filters['experience_min']) : '';
    $experienceMax = isset($filters['experience_max']) ? trim((string) $filters['experience_max']) : '';
    if ($experienceMin !== '' && preg_match('/\d+(?:\.\d+)?/', $experienceMin, $experienceMinMatch)) {
        $where[] = resumeSearchNormalizedExperienceSql('t.experiance') . " >= " . (float) $experienceMinMatch[0];
    }
    if ($experienceMax !== '' && preg_match('/\d+(?:\.\d+)?/', $experienceMax, $experienceMaxMatch)) {
        $where[] = resumeSearchNormalizedExperienceSql('t.experiance') . " <= " . (float) $experienceMaxMatch[0];
    }

    $experience = isset($filters['experience']) ? trim((string) $filters['experience']) : '';
    if ($experience !== '' && $experienceMin === '' && $experienceMax === '' && preg_match('/\d+(?:\.\d+)?/', $experience, $experienceMatch)) {
        $where[] = resumeSearchNormalizedExperienceSql('t.experiance') . " >= " . (float) $experienceMatch[0];
    }

    $leadStatus = isset($filters['lead_status']) ? (int) $filters['lead_status'] : 0;
    if ($leadStatus > 0) {
        $where[] = "t.status = " . $leadStatus;
    }

    $source = isset($filters['source']) ? (int) $filters['source'] : 0;
    if ($source > 0) {
        $where[] = "t.source = " . $source;
    }

    $city = isset($filters['city']) ? trim((string) $filters['city']) : '';
    if ($city !== '') {
        $where[] = "t.city LIKE '%" . $connect->real_escape_string($city) . "%'";
    }

    $relocate = isset($filters['relocate']) ? trim((string) $filters['relocate']) : '';
    if ($relocate !== '') {
        $where[] = "t.willing_to_relocate = '" . $connect->real_escape_string($relocate) . "'";
    }

    $currentCtc = isset($filters['current_ctc']) ? trim((string) $filters['current_ctc']) : '';
    if ($currentCtc !== '') {
        $normalizedCurrentCtc = resumeSearchNormalizeSalaryThreshold($currentCtc);
        if ($normalizedCurrentCtc !== null) {
            $where[] = resumeSearchAnnualSalarySql('t.csalary') . " <= " . (float) $normalizedCurrentCtc;
        }
    }

    $expectedCtc = isset($filters['expected_ctc']) ? trim((string) $filters['expected_ctc']) : '';
    if ($expectedCtc !== '') {
        $normalizedExpectedCtc = resumeSearchNormalizeSalaryThreshold($expectedCtc);
        if ($normalizedExpectedCtc !== null) {
            $where[] = resumeSearchAnnualSalarySql('t.esalary') . " <= " . (float) $normalizedExpectedCtc;
        }
    }

    $noticePeriod = isset($filters['notice_period']) ? trim((string) $filters['notice_period']) : '';
    if ($noticePeriod !== '') {
        if (preg_match('/^\d+$/', $noticePeriod)) {
            $cleanNotice = "REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TRIM(COALESCE(t.nperiod, ''))), 'days', ''), 'day', ''), ' ', ''), '+', '')";
            $where[] = "(" . $cleanNotice . " REGEXP '^[0-9]+$' AND CAST(" . $cleanNotice . " AS UNSIGNED) <= " . (int) $noticePeriod . ")";
        } else {
            $where[] = "COALESCE(t.nperiod, '') LIKE '%" . $connect->real_escape_string($noticePeriod) . "%'";
        }
    }

    $interval = isset($filters['interval']) ? trim((string) $filters['interval']) : '';
    if ($interval === 'last-seven') {
        $where[] = "DATE(t.dateadded) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    } elseif ($interval === 'last-thirty') {
        $where[] = "DATE(t.dateadded) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    } elseif ($interval === 'last-month') {
        $where[] = "DATE(t.dateadded) >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
    }

    $whereSql = implode(' AND ', $where);
    $fromSql = " FROM tblleads t
        LEFT JOIN resume_documents rd
            ON rd.lead_id = t.id
           AND rd.original_resume_name = t.resume
        LEFT JOIN tblleadsstatus ls ON ls.id = t.status
        LEFT JOIN tblleadssources src ON src.id = t.source
        WHERE $whereSql";

    $countResult = $connect->query("SELECT COUNT(*) AS total" . $fromSql);
    if (!$countResult) {
        return $empty;
    }

    $countRow = $countResult->fetch_assoc();
    $total = isset($countRow['total']) ? (int) $countRow['total'] : 0;
    $countResult->free();

    $pages = max(1, (int) ceil($total / $perPage));
    if ($page > $pages) {
        $page = $pages;
    }

    $offset = ($page - 1) * $perPage;
    $relevanceSql = empty($relevanceParts) ? "0" : implode(' + ', $relevanceParts);

    $sql = "SELECT
                COALESCE(rd.id, 0) AS id,
                t.id AS lead_id,
                t.name AS lead_name,
                t.resume AS original_resume_name,
                COALESCE(rd.file_path, '') AS file_path,
                LOWER(SUBSTRING_INDEX(t.resume, '.', -1)) AS file_extension,
                COALESCE(rd.extraction_status, 'not_indexed') AS extraction_status,
                COALESCE(rd.extracted_email, t.email) AS extracted_email,
                COALESCE(rd.extracted_phone, t.phonenumber) AS extracted_phone,
                COALESCE(rd.extracted_skills, t.skillset) AS extracted_skills,
                rd.raw_text,
                COALESCE(rd.updated_at, t.dateadded) AS updated_at,
                rd.last_error,
                t.email AS lead_email,
                t.phonenumber AS lead_phone,
                t.city AS city,
                t.city AS lead_city,
                t.country AS country,
                t.country AS lead_country,
                t.state AS state,
                t.street AS street,
                t.zip AS zip,
                t.willing_to_relocate AS willing_to_relocate,
                t.willing_to_relocate AS lead_willing_to_relocate,
                t.experiance AS experiance,
                t.experiance AS lead_experiance,
                t.qualification AS qualification,
                t.qualification AS lead_qualification,
                t.cjtitle AS cjtitle,
                t.cjtitle AS lead_cjtitle,
                t.cemployer AS cemployer,
                t.cemployer AS lead_cemployer,
                t.csalary AS csalary,
                t.csalary AS lead_csalary,
                t.esalary AS esalary,
                t.esalary AS lead_esalary,
                t.skillset AS skillset,
                t.skillset AS lead_skillset,
                t.ainfo AS ainfo,
                t.ainfo AS lead_ainfo,
                t.nperiod AS nperiod,
                t.nperiod AS lead_nperiod,
                t.dateadded AS dateadded,
                t.dateadded AS lead_dateadded,
                t.roles AS roles,
                t.roles AS lead_roles,
                t.status AS status,
                t.source AS source,
                ls.name AS lead_status_name,
                src.name AS lead_source_name,
                (" . $relevanceSql . ") AS relevance_score" .
            $fromSql .
            " ORDER BY relevance_score DESC, t.dateadded DESC, COALESCE(rd.updated_at, t.dateadded) DESC, t.id DESC LIMIT " . $offset . ", " . $perPage;

    $result = $connect->query($sql);
    if (!$result) {
        return $empty;
    }

    $rows = array();
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $result->free();

    return array(
        'rows' => $rows,
        'total' => $total,
        'page' => $page,
        'per_page' => $perPage,
        'pages' => $pages
    );
}

function extractGeminiOutputText($response)
{
    if (is_array($response) && isset($response['output_text']) && trim((string) $response['output_text']) !== '') {
        return trim((string) $response['output_text']);
    }

    if (!is_array($response)) {
        return '';
    }

    $contentExtractors = array(
        function ($node) {
            if (is_array($node) && !empty($node['content']) && is_array($node['content'])) {
                $parts = array();
                foreach ($node['content'] as $contentPart) {
                    if (isset($contentPart['text']) && trim((string) $contentPart['text']) !== '') {
                        $parts[] = trim((string) $contentPart['text']);
                    }
                }
                if (!empty($parts)) {
                    return trim(implode("\n", $parts));
                }
            }
            return '';
        },
        function ($node) {
            if (is_array($node) && isset($node['text']) && trim((string) $node['text']) !== '') {
                return trim((string) $node['text']);
            }
            return '';
        }
    );

    $preferredNodes = array();
    if (!empty($response['model_output'])) {
        $preferredNodes[] = $response['model_output'];
    }
    if (!empty($response['output'])) {
        $preferredNodes[] = $response['output'];
    }
    if (!empty($response['response'])) {
        $preferredNodes[] = $response['response'];
    }

    foreach ($preferredNodes as $node) {
        foreach ($contentExtractors as $extractor) {
            $text = $extractor($node);
            if ($text !== '') {
                return $text;
            }
        }
    }

    if (!empty($response['steps']) && is_array($response['steps'])) {
        $steps = $response['steps'];
        for ($i = count($steps) - 1; $i >= 0; $i--) {
            $step = $steps[$i];
            foreach ($contentExtractors as $extractor) {
                $text = $extractor($step);
                if ($text !== '') {
                    return $text;
                }
            }
            if (is_array($step)) {
                foreach (array('model_output', 'output', 'result') as $nestedKey) {
                    if (!empty($step[$nestedKey])) {
                        foreach ($contentExtractors as $extractor) {
                            $text = $extractor($step[$nestedKey]);
                            if ($text !== '') {
                                return $text;
                            }
                        }
                    }
                }
            }
        }
    }

    $stack = array($response);
    while (!empty($stack)) {
        $node = array_pop($stack);
        if (!is_array($node)) {
            continue;
        }

        foreach ($contentExtractors as $extractor) {
            $text = $extractor($node);
            if ($text !== '') {
                return $text;
            }
        }

        foreach ($node as $value) {
            if (is_array($value)) {
                $stack[] = $value;
            }
        }
    }

    return '';
}

function normalizeGeminiJsonResponseText($text)
{
    $text = trim((string) $text);
    if ($text === '') {
        return '';
    }

    if (preg_match('/```(?:json)?\s*(.*?)```/is', $text, $matches)) {
        return trim((string) $matches[1]);
    }

    return $text;
}

function normalizeResumeCandidateKey($value)
{
    $value = strtolower(trim((string) $value));
    $value = preg_replace('/[^a-z0-9]+/', ' ', $value);
    return trim((string) $value);
}

function detectLocalPdfPageCount($filePath)
{
    if (!is_file($filePath) || !is_readable($filePath)) {
        return 0;
    }

    $contents = @file_get_contents($filePath);
    if ($contents === false || $contents === '') {
        return 0;
    }

    if (preg_match_all('/\/Type\s*\/Page\b/i', $contents, $matches)) {
        $pageCount = isset($matches[0]) ? count($matches[0]) : 0;
        if ($pageCount > 0) {
            return $pageCount;
        }
    }

    if (preg_match('/\/Count\s+(\d+)/i', $contents, $countMatch)) {
        return max(0, (int) $countMatch[1]);
    }

    return 0;
}

function uploadGeminiResumeFile($apiKey, $filePath, $mimeType)
{
    if (!is_file($filePath) || !is_readable($filePath)) {
        return array('ok' => false, 'message' => 'Resume file is missing or unreadable.');
    }

    $numBytes = filesize($filePath);
    if ($numBytes === false || $numBytes <= 0) {
        return array('ok' => false, 'message' => 'Resume file is empty.');
    }

    $startHeaders = array();
    $ch = curl_init('https://generativelanguage.googleapis.com/upload/v1beta/files');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curl, $headerLine) use (&$startHeaders) {
        $length = strlen($headerLine);
        $parts = explode(':', $headerLine, 2);
        if (count($parts) === 2) {
            $startHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
        }
        return $length;
    });
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'x-goog-api-key: ' . $apiKey,
        'X-Goog-Upload-Protocol: resumable',
        'X-Goog-Upload-Command: start',
        'X-Goog-Upload-Header-Content-Length: ' . $numBytes,
        'X-Goog-Upload-Header-Content-Type: ' . $mimeType,
        'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, resumeJsonEncode(array(
        'file' => array(
            'display_name' => basename($filePath)
        )
    )));
    curl_setopt($ch, CURLOPT_TIMEOUT, 240);

    $startResponse = curl_exec($ch);
    $startError = curl_error($ch);
    $startCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($startResponse === false || $startCode >= 400 || empty($startHeaders['x-goog-upload-url'])) {
        $responsePreview = '';
        if (is_string($startResponse) && trim($startResponse) !== '') {
            $responsePreview = ' Response: ' . substr(trim(preg_replace('/\s+/', ' ', $startResponse)), 0, 260);
        }
        return array('ok' => false, 'message' => 'Could not initialize Gemini file upload. HTTP ' . $startCode . '. ' . $startError . $responsePreview);
    }

    $uploadUrl = $startHeaders['x-goog-upload-url'];
    $fileBytes = @file_get_contents($filePath);
    if ($fileBytes === false) {
        return array('ok' => false, 'message' => 'Could not read resume bytes for Gemini upload.');
    }

    $ch = curl_init($uploadUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Length: ' . strlen($fileBytes),
        'X-Goog-Upload-Offset: 0',
        'X-Goog-Upload-Command: upload, finalize'
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fileBytes);
    curl_setopt($ch, CURLOPT_TIMEOUT, 180);

    $uploadResponse = curl_exec($ch);
    $uploadError = curl_error($ch);
    $uploadCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($uploadResponse === false || $uploadCode >= 400) {
        $responsePreview = '';
        if (is_string($uploadResponse) && trim($uploadResponse) !== '') {
            $responsePreview = ' Response: ' . substr(trim(preg_replace('/\s+/', ' ', $uploadResponse)), 0, 260);
        }
        return array('ok' => false, 'message' => 'Gemini file upload failed. HTTP ' . $uploadCode . '. ' . $uploadError . $responsePreview);
    }

    $decoded = json_decode($uploadResponse, true);
    if (!is_array($decoded) || empty($decoded['file']['uri'])) {
        return array('ok' => false, 'message' => 'Gemini file upload did not return a usable file URI.');
    }

    return array(
        'ok' => true,
        'uri' => (string) $decoded['file']['uri'],
        'name' => isset($decoded['file']['name']) ? (string) $decoded['file']['name'] : '',
        'mime_type' => isset($decoded['file']['mimeType']) ? (string) $decoded['file']['mimeType'] : $mimeType
    );
}

function deleteGeminiResumeFile($apiKey, $fileName)
{
    $fileName = trim((string) $fileName);
    if ($fileName === '') {
        return;
    }

    $ch = curl_init('https://generativelanguage.googleapis.com/v1beta/' . $fileName);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'x-goog-api-key: ' . $apiKey
    ));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_exec($ch);
    curl_close($ch);
}

function requestGeminiResumeInsights($hiringBrief, $candidateRows, $filters = array())
{
    if (function_exists('set_time_limit')) {
        @set_time_limit(300);
    }

    $status = getGeminiResumeStatus();
    if (empty($status['api_key_ready'])) {
        return array(
            'ok' => false,
            'message' => 'Gemini API key is not configured yet.'
        );
    }

    if (!function_exists('curl_init')) {
        return array(
            'ok' => false,
            'message' => 'cURL is not available in this PHP environment.'
        );
    }

    $candidates = array();
    $inputParts = array();
    $uploadedFiles = array();
    $candidateErrors = array();
    $successfulCandidateIds = array();

    $filtersText = sanitizeGeminiTextPart((string) resumeJsonEncode($filters, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    $inputParts[] = array(
        'type' => 'text',
        'text' => "You are an HR screening assistant. Return only valid JSON. Do not add markdown.\n\nHiring brief:\n" . sanitizeGeminiTextPart($hiringBrief) . "\n\nCurrent search filters:\n" . $filtersText . "\n\nYou will receive candidate resumes with metadata. Score every candidate and return only valid JSON."
    );

    foreach ((array) $candidateRows as $row) {
        $dynamicExperience = calculateDynamicExperience(
            isset($row['experiance']) ? $row['experiance'] : '',
            isset($row['dateadded']) ? $row['dateadded'] : ''
        );

        $candidate = array(
            'lead_id' => isset($row['lead_id']) ? (int) $row['lead_id'] : 0,
            'name' => isset($row['lead_name']) ? (string) $row['lead_name'] : '',
            'current_experience_years' => $dynamicExperience['current_years'] !== null ? round((float) $dynamicExperience['current_years'], 1) : null,
            'original_experience' => isset($row['experiance']) ? (string) $row['experiance'] : '',
            'notice_period' => isset($row['nperiod']) ? (string) $row['nperiod'] : '',
            'skills' => isset($row['extracted_skills']) ? (string) $row['extracted_skills'] : '',
            'email' => isset($row['extracted_email']) && trim((string) $row['extracted_email']) !== '' ? (string) $row['extracted_email'] : (isset($row['lead_email']) ? (string) $row['lead_email'] : ''),
            'phone' => isset($row['extracted_phone']) && trim((string) $row['extracted_phone']) !== '' ? (string) $row['extracted_phone'] : (isset($row['lead_phone']) ? (string) $row['lead_phone'] : ''),
        );
        $candidates[] = $candidate;

        $fileExtension = isset($row['file_extension']) ? strtolower(trim((string) $row['file_extension'])) : '';
        $filePath = resolveResumeAbsolutePath(
            isset($row['original_resume_name']) ? (string) $row['original_resume_name'] : '',
            isset($row['file_path']) ? (string) $row['file_path'] : ''
        );
        $inputParts[] = array(
            'type' => 'text',
            'text' => buildGeminiCandidateMetadataText($candidate)
        );

        if ($fileExtension === 'pdf') {
            $mimeType = 'application/pdf';
            $localPageCount = detectLocalPdfPageCount($filePath);
            if ($localPageCount < 1) {
                $candidateErrors[(int) $candidate['lead_id']] = 'AI PDF validation failed. The resume PDF appears to have no readable pages for document processing.';
                continue;
            }

            $uploadResult = uploadGeminiResumeFile($status['config']['api_key'], $filePath, $mimeType);
            if (!$uploadResult['ok']) {
                $candidateErrors[(int) $candidate['lead_id']] = 'AI PDF upload failed. ' . $uploadResult['message'];
                continue;
            }

            $uploadedFiles[] = $uploadResult;
            $successfulCandidateIds[] = (int) $candidate['lead_id'];
            $inputParts[] = array(
                'type' => 'document',
                'uri' => $uploadResult['uri'],
                'mime_type' => $uploadResult['mime_type']
            );
            continue;
        }

        $rawText = isset($row['raw_text']) ? trim((string) $row['raw_text']) : '';
        if ($rawText === '') {
            $candidateErrors[(int) $candidate['lead_id']] = 'AI resume text is not available for this non-PDF file yet.';
            continue;
        }

        $successfulCandidateIds[] = (int) $candidate['lead_id'];
        $inputParts[] = array(
            'type' => 'text',
            'text' => "Candidate resume text:\n" . truncateGeminiTextPart($rawText)
        );
    }

    if (empty($successfulCandidateIds)) {
        resumeIntelligenceLog('gemini_resume', 'No candidates could be prepared for Gemini insights.', array(
            'filters' => $filters,
            'candidate_errors' => $candidateErrors
        ));
        return array(
            'ok' => false,
            'message' => 'AI could not process any resumes in this batch.',
            'candidate_errors' => $candidateErrors
        );
    }

    $payload = array(
        'model' => $status['model'],
        'input' => array_merge(
            $inputParts,
            array(
                array(
                    'type' => 'text',
                    'text' => "Return one JSON object only.\n\nRequired keys:\nsummary\n interpreted_filters\n search_strategy\n top_50_recommended_candidates\n skills_heatmap\n experience_distribution\n notice_period_analysis\n all_candidates\n\nExpected item shapes:\n- interpreted_filters: object with skills, experience, salary, notice_period, location, role, other_requirements\n- search_strategy: short object or string explaining how you interpreted the recruiter request\n- top_50_recommended_candidates: array of objects with lead_id, name, match_score, ordered best to weakest. Include up to 50 candidates, matching the provided candidate count.\n- skills_heatmap: array of objects with skill, strength\n- all_candidates: array of objects with lead_id, name, candidate_match_score, interview_readiness_score, ai_generated_candidate_summary, recommended_interview_questions, risk_indicators, why, interview_focus\n\nRules:\n- Include exactly one all_candidates item for every provided candidate.\n- If 50 candidates are provided, return 50 all_candidates items.\n- Use only lead_id values from the provided candidate list.\n- Keep score fields between 1 and 100.\n- ai_generated_candidate_summary must be 6 to 8 practical HR-perspective sentences. Mention resume evidence, CRM experience, salary/CTC fit, location fit, notice period, strengths, concerns, and whether HR should prioritize outreach.\n- recommended_interview_questions must be an array of strings.\n- risk_indicators must be an array of strings.\n- Do not wrap the JSON in markdown."
                )
            )
        )
    );

    $ch = curl_init('https://generativelanguage.googleapis.com/v1beta/interactions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'x-goog-api-key: ' . $status['config']['api_key'],
        'Content-Type: application/json'
    ));
    $payloadJson = resumeJsonEncode($payload, JSON_UNESCAPED_SLASHES);
    if (!is_string($payloadJson) || $payloadJson === '') {
        foreach ($uploadedFiles as $uploadedFile) {
            deleteGeminiResumeFile($status['config']['api_key'], isset($uploadedFile['name']) ? $uploadedFile['name'] : '');
        }

        resumeIntelligenceLog('gemini_resume', 'Failed to encode Gemini payload as JSON.', array(
            'json_last_error' => function_exists('json_last_error_msg') ? json_last_error_msg() : 'Unknown JSON error',
            'candidate_errors' => $candidateErrors
        ));

        return array(
            'ok' => false,
            'message' => 'Could not encode the Gemini request payload as valid JSON.'
        );
    }

    resumeIntelligenceLog('gemini_resume', 'Prepared Gemini payload.', array(
        'successful_candidate_ids' => $successfulCandidateIds,
        'candidate_error_count' => count($candidateErrors),
        'payload_bytes' => strlen($payloadJson),
        'payload_preview' => substr($payloadJson, 0, 2000)
    ));

    curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 240);

    resumeIntelligenceLog('gemini_resume', 'Sending Gemini request.', array(
        'model' => $status['model'],
        'payload_bytes' => strlen($payloadJson)
    ));

    $rawResponse = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $totalTime = (float) curl_getinfo($ch, CURLINFO_TOTAL_TIME);
    curl_close($ch);

    resumeIntelligenceLog('gemini_resume', 'Gemini request completed.', array(
        'http_code' => $httpCode,
        'curl_error' => $curlError,
        'total_time_seconds' => $totalTime,
        'response_bytes' => is_string($rawResponse) ? strlen($rawResponse) : 0
    ));

    if ($rawResponse === false) {
        resumeIntelligenceLog('gemini_resume', 'Gemini cURL request failed.', array(
            'curl_error' => $curlError,
            'http_code' => $httpCode,
            'total_time_seconds' => $totalTime
        ));
        return array(
            'ok' => false,
            'message' => 'Gemini request failed: ' . $curlError
        );
    }

    $decoded = json_decode($rawResponse, true);
    if ($httpCode >= 400) {
        $errorMessage = 'Gemini API returned HTTP ' . $httpCode . '.';
        if (is_array($decoded) && isset($decoded['error']['message'])) {
            $errorMessage = (string) $decoded['error']['message'];
        }

        foreach ($uploadedFiles as $uploadedFile) {
            deleteGeminiResumeFile($status['config']['api_key'], isset($uploadedFile['name']) ? $uploadedFile['name'] : '');
        }

        resumeIntelligenceLog('gemini_resume', 'Gemini API returned an error response.', array(
            'http_code' => $httpCode,
            'message' => $errorMessage,
            'payload_preview' => substr($payloadJson, 0, 2000),
            'raw_response_preview' => substr(trim(preg_replace('/\s+/', ' ', (string) $rawResponse)), 0, 500),
            'candidate_errors' => $candidateErrors
        ));

        return array(
            'ok' => false,
            'message' => $errorMessage,
            'candidate_errors' => $candidateErrors
        );
    }

    if (is_array($decoded)) {
        resumeIntelligenceLog('gemini_resume', 'Gemini JSON response received.', array(
            'http_code' => $httpCode,
            'top_level_keys' => array_keys($decoded)
        ));
    }

    if (!is_array($decoded)) {
        foreach ($uploadedFiles as $uploadedFile) {
            deleteGeminiResumeFile($status['config']['api_key'], isset($uploadedFile['name']) ? $uploadedFile['name'] : '');
        }

        resumeIntelligenceLog('gemini_resume', 'Gemini returned HTTP 200 with a non-JSON body.', array(
            'http_code' => $httpCode,
            'raw_response_preview' => substr(trim(preg_replace('/\s+/', ' ', (string) $rawResponse)), 0, 1000)
        ));

        return array(
            'ok' => false,
            'message' => 'Gemini returned HTTP 200 but the response was not valid JSON. Check data/logs/gemini_resume.log on the server.'
        );
    }

    $outputText = extractGeminiOutputText($decoded);
    $jsonText = normalizeGeminiJsonResponseText($outputText);
    $parsed = json_decode($jsonText, true);

    foreach ($uploadedFiles as $uploadedFile) {
        deleteGeminiResumeFile($status['config']['api_key'], isset($uploadedFile['name']) ? $uploadedFile['name'] : '');
    }

    if (trim((string) $outputText) === '') {
        resumeIntelligenceLog('gemini_resume', 'Gemini response JSON did not contain output text.', array(
            'http_code' => $httpCode,
            'decoded_response' => $decoded
        ));

        return array(
            'ok' => false,
            'message' => 'Gemini returned HTTP 200 but no usable output text was found. Check data/logs/gemini_resume.log on the server.'
        );
    }

    if (!is_array($parsed)) {
        resumeIntelligenceLog('gemini_resume', 'Gemini output text was not valid JSON.', array(
            'output_text_preview' => substr(trim(preg_replace('/\s+/', ' ', (string) $outputText)), 0, 1000)
        ));

        return array(
            'ok' => false,
            'message' => 'Gemini returned text, but it was not valid JSON. Check data/logs/gemini_resume.log on the server.',
            'raw_text' => $outputText,
            'candidate_errors' => $candidateErrors,
            'successful_candidate_ids' => $successfulCandidateIds
        );
    }

    return array(
        'ok' => true,
        'message' => 'Gemini insights generated.',
        'raw_text' => $outputText,
        'parsed' => is_array($parsed) ? $parsed : null,
        'candidate_errors' => $candidateErrors,
        'successful_candidate_ids' => $successfulCandidateIds
    );
}

function getNextResumeBatchLeads($connect, $limit = 50)
{
    $leads = array();

    if (!ensureResumeDocumentsTable($connect)) {
        return $leads;
    }

    $limit = max(1, (int) $limit);
    $queryWindow = max($limit * 8, 200);
    $sql = "SELECT t.id, t.name, t.email, t.phonenumber, t.resume
            FROM tblleads t
            LEFT JOIN resume_documents d
                ON d.lead_id = t.id
               AND d.original_resume_name = t.resume
            WHERE t.resume IS NOT NULL
              AND TRIM(t.resume) <> ''
              AND d.id IS NULL
            ORDER BY t.id DESC
            LIMIT " . $queryWindow;

    $result = $connect->query($sql);
    if (!$result) {
        return $leads;
    }

    $resumeDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resume';
    $prioritizedLeads = array();
    $fallbackLeads = array();

    while ($lead = $result->fetch_assoc()) {
        $resumeFile = basename(str_replace('\\', '/', (string) $lead['resume']));
        $absolutePath = $resumeDir . DIRECTORY_SEPARATOR . $resumeFile;
        if (is_file($absolutePath)) {
            $prioritizedLeads[] = $lead;
        } else {
            $fallbackLeads[] = $lead;
        }
    }

    $result->free();

    return array_slice(array_merge($prioritizedLeads, $fallbackLeads), 0, $limit);
}

function processResumeLead($connect, $lead)
{
    $resumeDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resume';
    $payload = buildResumeDocumentPayload($lead, $resumeDir);
    $ok = upsertResumeDocument($connect, $payload);

    return array(
        'ok' => $ok,
        'lead_id' => isset($lead['id']) ? (int) $lead['id'] : 0,
        'lead_name' => isset($lead['name']) ? (string) $lead['name'] : '',
        'resume' => isset($lead['resume']) ? (string) $lead['resume'] : '',
        'status' => $ok ? $payload['extraction_status'] : 'error',
        'email' => $payload['extracted_email'],
        'phone' => $payload['extracted_phone'],
        'skills' => $payload['extracted_skills'],
        'note' => $ok ? $payload['last_error'] : 'Failed to save resume document.',
        'updated_at' => date('Y-m-d H:i:s')
    );
}

function fetchLeadMapForResumeIds($connect, $leadIds)
{
    $map = array();
    $leadIds = array_values(array_filter(array_map('intval', (array) $leadIds)));
    if (empty($leadIds)) {
        return $map;
    }

    $sql = "SELECT id, name, email, phonenumber, resume
            FROM tblleads
            WHERE id IN (" . implode(',', $leadIds) . ")";
    $result = $connect->query($sql);
    if (!$result) {
        return $map;
    }

    while ($row = $result->fetch_assoc()) {
        $map[(int) $row['id']] = $row;
    }

    $result->free();

    return $map;
}

function fetchResumeWorkerState($connect)
{
    $default = array(
        'id' => 1,
        'is_running' => 0,
        'worker_token' => '',
        'worker_limit' => 0,
        'processed_total' => 0,
        'completed_total' => 0,
        'pending_total' => 0,
        'missing_total' => 0,
        'unsupported_total' => 0,
        'error_total' => 0,
        'last_message' => 'Worker idle.',
        'current_file' => '',
        'started_at' => null,
        'heartbeat_at' => null,
        'finished_at' => null
    );

    if (!ensureResumeWorkerStateTable($connect)) {
        return $default;
    }

    $result = $connect->query("SELECT * FROM resume_worker_state WHERE id = 1 LIMIT 1");
    if (!$result) {
        return $default;
    }

    $row = $result->fetch_assoc();
    $result->free();

    if (!$row) {
        return $default;
    }

    $state = array_merge($default, $row);

    if (!empty($state['is_running']) && !empty($state['heartbeat_at'])) {
        $heartbeat = strtotime($state['heartbeat_at']);
        if ($heartbeat !== false && $heartbeat < (time() - 900)) {
            $connect->query("UPDATE resume_worker_state
                SET is_running = 0,
                    last_message = 'Worker marked idle after stale heartbeat.',
                    finished_at = NOW()
                WHERE id = 1");
            $state['is_running'] = 0;
            $state['last_message'] = 'Worker marked idle after stale heartbeat.';
            $state['finished_at'] = date('Y-m-d H:i:s');
        }
    }

    return $state;
}

function resetResumeWorkerState($connect, $token, $limit)
{
    if (!ensureResumeWorkerStateTable($connect)) {
        return false;
    }

    $stmt = $connect->prepare("UPDATE resume_worker_state
        SET is_running = 1,
            worker_token = ?,
            worker_limit = ?,
            processed_total = 0,
            completed_total = 0,
            pending_total = 0,
            missing_total = 0,
            unsupported_total = 0,
            error_total = 0,
            last_message = 'Worker started.',
            current_file = NULL,
            started_at = NOW(),
            heartbeat_at = NOW(),
            finished_at = NULL
        WHERE id = 1");
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('si', $token, $limit);
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}

function updateResumeWorkerProgress($connect, $token, $counts, $message, $currentFile = '')
{
    if (!ensureResumeWorkerStateTable($connect)) {
        return false;
    }

    $processed = isset($counts['processed_total']) ? (int) $counts['processed_total'] : 0;
    $completed = isset($counts['completed_total']) ? (int) $counts['completed_total'] : 0;
    $pending = isset($counts['pending_total']) ? (int) $counts['pending_total'] : 0;
    $missing = isset($counts['missing_total']) ? (int) $counts['missing_total'] : 0;
    $unsupported = isset($counts['unsupported_total']) ? (int) $counts['unsupported_total'] : 0;
    $error = isset($counts['error_total']) ? (int) $counts['error_total'] : 0;

    $stmt = $connect->prepare("UPDATE resume_worker_state
        SET processed_total = ?,
            completed_total = ?,
            pending_total = ?,
            missing_total = ?,
            unsupported_total = ?,
            error_total = ?,
            last_message = ?,
            current_file = ?,
            heartbeat_at = NOW()
        WHERE id = 1 AND worker_token = ?");
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        'iiiiiisss',
        $processed,
        $completed,
        $pending,
        $missing,
        $unsupported,
        $error,
        $message,
        $currentFile,
        $token
    );
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}

function finishResumeWorkerState($connect, $token, $counts, $message)
{
    if (!ensureResumeWorkerStateTable($connect)) {
        return false;
    }

    $processed = isset($counts['processed_total']) ? (int) $counts['processed_total'] : 0;
    $completed = isset($counts['completed_total']) ? (int) $counts['completed_total'] : 0;
    $pending = isset($counts['pending_total']) ? (int) $counts['pending_total'] : 0;
    $missing = isset($counts['missing_total']) ? (int) $counts['missing_total'] : 0;
    $unsupported = isset($counts['unsupported_total']) ? (int) $counts['unsupported_total'] : 0;
    $error = isset($counts['error_total']) ? (int) $counts['error_total'] : 0;

    $stmt = $connect->prepare("UPDATE resume_worker_state
        SET is_running = 0,
            processed_total = ?,
            completed_total = ?,
            pending_total = ?,
            missing_total = ?,
            unsupported_total = ?,
            error_total = ?,
            last_message = ?,
            heartbeat_at = NOW(),
            finished_at = NOW()
        WHERE id = 1 AND worker_token = ?");
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        'iiiiiiss',
        $processed,
        $completed,
        $pending,
        $missing,
        $unsupported,
        $error,
        $message,
        $token
    );
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}

function canStartResumeWorker($connect)
{
    $state = fetchResumeWorkerState($connect);
    return empty($state['is_running']);
}

function buildResumeWorkerCommand($limit, $token)
{
    $scriptPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resume_worker.php';
    $limit = max(1, (int) $limit);
    $token = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string) $token);
    $xamppPhpBinary = 'C:\\xampp\\php\\php.exe';
    $projectRoot = dirname(__DIR__);
    $phpBinary = $xamppPhpBinary;
    if (!is_file($phpBinary)) {
        $phpBinary = $projectRoot . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'php.exe';
    }
    if (!is_file($phpBinary)) {
        $phpBinary = PHP_BINARY;
    }

    return array(
        'php_binary' => $phpBinary,
        'script_path' => $scriptPath,
        'limit' => $limit,
        'token' => $token
    );
}

function resumeWorkerLogPaths()
{
    $logDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0777, true);
    }

    return array(
        'stdout' => $logDir . DIRECTORY_SEPARATOR . 'resume_worker.out.log',
        'stderr' => $logDir . DIRECTORY_SEPARATOR . 'resume_worker.err.log'
    );
}

function startResumeWorkerProcess($limit, $token)
{
    $command = buildResumeWorkerCommand($limit, $token);
    $logPaths = resumeWorkerLogPaths();

    $phpBinary = isset($command['php_binary']) ? $command['php_binary'] : PHP_BINARY;
    $scriptPath = isset($command['script_path']) ? $command['script_path'] : '';
    $workerLimit = isset($command['limit']) ? (int) $command['limit'] : (int) $limit;
    $workerToken = isset($command['token']) ? $command['token'] : $token;

    if ($scriptPath === '' || !is_file($phpBinary) || !is_file($scriptPath)) {
        return false;
    }

    $psCommand = "Start-Process -FilePath '" . str_replace("'", "''", $phpBinary) . "' " .
        "-ArgumentList @('" . str_replace("'", "''", $scriptPath) . "','--limit=" . $workerLimit . "','--token=" . str_replace("'", "''", $workerToken) . "') " .
        "-WindowStyle Hidden " .
        "-RedirectStandardOutput '" . str_replace("'", "''", $logPaths['stdout']) . "' " .
        "-RedirectStandardError '" . str_replace("'", "''", $logPaths['stderr']) . "'";

    $shellCommand = 'powershell.exe -NoProfile -ExecutionPolicy Bypass -Command "' . str_replace('"', '`"', $psCommand) . '"';

    if (function_exists('pclose') && function_exists('popen')) {
        @pclose(@popen($shellCommand, 'r'));
        return true;
    }

    if (function_exists('exec')) {
        @exec($shellCommand);
        return true;
    }

    return false;
}

?>
