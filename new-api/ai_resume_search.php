<?php
ob_start();
@ini_set('display_errors', '0');

register_shutdown_function(function () {
    $error = error_get_last();
    if (!$error || !in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true)) {
        return;
    }

    while (ob_get_level() > 0) {
        @ob_end_clean();
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(array(
        'success' => false,
        'message' => 'AI batch failed on the server: ' . $error['message'],
        'cards' => array(),
        'batch' => array(
            'offset' => isset($_POST['offset']) ? (int) $_POST['offset'] : 0,
            'batch_size' => isset($_POST['batch_size']) ? (int) $_POST['batch_size'] : 10,
            'processed_to' => isset($_POST['offset']) ? (int) $_POST['offset'] : 0,
            'is_final' => true
        )
    ), JSON_UNESCAPED_SLASHES);
});

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$projectRoot = dirname(__DIR__);
@chdir($projectRoot);
require_once $projectRoot . '/inc/config.php';
require_once $projectRoot . '/includes/resume_intelligence.php';

ensureResumeIntelligenceTables($connect);

function aiSearchJson($payload)
{
    while (ob_get_level() > 0) {
        @ob_end_clean();
    }

    echo resumeJsonEncode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function aiSearchPostValue($key, $default = '')
{
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : $default;
}

function aiSearchOptionName($options, $selectedId, $fallback)
{
    $selectedId = (int) $selectedId;
    if ($selectedId <= 0) {
        return $fallback;
    }

    foreach ((array) $options as $option) {
        if (isset($option['id']) && (int) $option['id'] === $selectedId) {
            return isset($option['name']) ? (string) $option['name'] : $fallback;
        }
    }

    return 'ID ' . $selectedId;
}

function aiSearchIntervalLabel($interval)
{
    $interval = trim((string) $interval);
    if ($interval === 'last-seven') {
        return 'Last 7 days';
    }
    if ($interval === 'last-thirty') {
        return 'Last 30 days';
    }
    if ($interval === 'last-month') {
        return 'Last 3 months';
    }

    return 'Any date';
}

function aiSearchCleanValue($value)
{
    $value = trim((string) $value);
    if ($value === '' || in_array(strtolower($value), array('na', 'n/a', 'none', 'null', '-'), true)) {
        return 'Not provided';
    }

    return $value;
}

function aiSearchKeywords($text)
{
    $knownSkills = array(
        'SEO' => array('seo', 'search engine optimization', 'search engine optimisation', 'on page', 'on-page', 'off page', 'off-page'),
        'Google Ads' => array('google ads', 'adwords', 'paid search', 'ppc'),
        'Meta Ads' => array('meta ads', 'facebook ads', 'instagram ads'),
        'Social Media' => array('social media'),
        'Content Writing' => array('content writing', 'content writer'),
        'Copywriting' => array('copywriting', 'copywriter'),
        'WordPress' => array('wordpress'),
        'Client Servicing' => array('client servicing', 'client communication')
    );

    $normalized = strtolower((string) $text);
    $keywords = array();
    foreach ($knownSkills as $label => $needles) {
        foreach ($needles as $needle) {
            if (strpos($normalized, strtolower($needle)) !== false) {
                $keywords[$label] = true;
                break;
            }
        }
    }

    return array_keys($keywords);
}

function ensureHrAiUsageLogsTable($connect)
{
    if (!($connect instanceof mysqli)) {
        return false;
    }

    $created = (bool) $connect->query("CREATE TABLE IF NOT EXISTS hr_ai_usage_logs (
        id int(11) NOT NULL AUTO_INCREMENT,
        staff_id int(11) NOT NULL DEFAULT 0,
        tool_name varchar(50) NOT NULL DEFAULT 'HrCrm',
        feature_name varchar(100) NOT NULL DEFAULT 'ai_search',
        model_name varchar(100) DEFAULT NULL,
        input_tokens int(11) NOT NULL DEFAULT 0,
        output_tokens int(11) NOT NULL DEFAULT 0,
        total_tokens int(11) NOT NULL DEFAULT 0,
        candidate_count int(11) NOT NULL DEFAULT 0,
        batch_offset int(11) NOT NULL DEFAULT 0,
        batch_size int(11) NOT NULL DEFAULT 0,
        status varchar(30) NOT NULL DEFAULT 'success',
        external_usage_id int(11) DEFAULT NULL,
        input_cost decimal(18,8) NOT NULL DEFAULT 0.00000000,
        output_cost decimal(18,8) NOT NULL DEFAULT 0.00000000,
        total_cost decimal(18,8) NOT NULL DEFAULT 0.00000000,
        currency varchar(10) NOT NULL DEFAULT 'USD',
        external_response_json longtext DEFAULT NULL,
        notes text DEFAULT NULL,
        created_at datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (id),
        KEY idx_hr_ai_usage_staff_date (staff_id, created_at),
        KEY idx_hr_ai_usage_tool_date (tool_name, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    if (!$created) {
        return false;
    }

    $columns = array(
        'input_cost' => "ALTER TABLE hr_ai_usage_logs ADD input_cost decimal(18,8) NOT NULL DEFAULT 0.00000000 AFTER external_usage_id",
        'output_cost' => "ALTER TABLE hr_ai_usage_logs ADD output_cost decimal(18,8) NOT NULL DEFAULT 0.00000000 AFTER input_cost",
        'total_cost' => "ALTER TABLE hr_ai_usage_logs ADD total_cost decimal(18,8) NOT NULL DEFAULT 0.00000000 AFTER output_cost",
        'currency' => "ALTER TABLE hr_ai_usage_logs ADD currency varchar(10) NOT NULL DEFAULT 'USD' AFTER total_cost"
    );

    foreach ($columns as $column => $alterSql) {
        $check = $connect->query("SHOW COLUMNS FROM hr_ai_usage_logs LIKE '" . $connect->real_escape_string($column) . "'");
        $exists = $check && $check->num_rows > 0;
        if ($check) {
            $check->free();
        }
        if (!$exists) {
            $connect->query($alterSql);
        }
    }

    return true;
}

function ensureHrAiSearchHistoryTable($connect)
{
    if (!($connect instanceof mysqli)) {
        return false;
    }

    return (bool) $connect->query("CREATE TABLE IF NOT EXISTS hr_ai_search_history (
        id int(11) NOT NULL AUTO_INCREMENT,
        staff_id int(11) NOT NULL DEFAULT 0,
        title varchar(255) NOT NULL DEFAULT 'AI Search',
        filters_json longtext DEFAULT NULL,
        filters_used_json longtext DEFAULT NULL,
        summary text DEFAULT NULL,
        result_cards_json longtext DEFAULT NULL,
        processed_count int(11) NOT NULL DEFAULT 0,
        total_limit int(11) NOT NULL DEFAULT 50,
        total_available int(11) NOT NULL DEFAULT 0,
        input_tokens int(11) NOT NULL DEFAULT 0,
        output_tokens int(11) NOT NULL DEFAULT 0,
        total_tokens int(11) NOT NULL DEFAULT 0,
        status varchar(30) NOT NULL DEFAULT 'running',
        created_at datetime NOT NULL DEFAULT current_timestamp(),
        updated_at datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (id),
        KEY idx_hr_ai_search_history_staff_date (staff_id, updated_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function aiSearchHistoryTitle($filters, $roleOptions)
{
    $parts = array();
    $roleLabel = aiSearchOptionName($roleOptions, isset($filters['role']) ? (int) $filters['role'] : 0, '');
    if ($roleLabel !== '') {
        $parts[] = $roleLabel;
    }
    if (!empty($filters['city'])) {
        $parts[] = $filters['city'];
    }
    if (!empty($filters['experience_min']) || !empty($filters['experience_max'])) {
        $parts[] = trim((string) $filters['experience_min']) . '-' . trim((string) $filters['experience_max']) . ' yrs';
    }

    $title = trim(implode(' | ', array_filter($parts)));
    return $title !== '' ? substr($title, 0, 255) : 'AI Search';
}

function aiSearchCreateHistory($connect, $staffId, $filters, $roleOptions, $totalLimit)
{
    if (!ensureHrAiSearchHistoryTable($connect)) {
        return 0;
    }

    $title = aiSearchHistoryTitle($filters, $roleOptions);
    $filtersJson = resumeJsonEncode($filters, JSON_UNESCAPED_SLASHES);
    $emptyCards = resumeJsonEncode(array(), JSON_UNESCAPED_SLASHES);
    $status = 'running';
    $stmt = $connect->prepare("INSERT INTO hr_ai_search_history (staff_id, title, filters_json, result_cards_json, total_limit, status) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        return 0;
    }

    $stmt->bind_param('isssis', $staffId, $title, $filtersJson, $emptyCards, $totalLimit, $status);
    $ok = $stmt->execute();
    $id = $ok ? (int) $stmt->insert_id : 0;
    $stmt->close();

    return $id;
}

function aiSearchUpdateHistory($connect, $historyId, $staffId, $filtersUsed, $summary, $newCards, $processedTo, $totalLimit, $totalAvailable, $usage, $isFinal, $status)
{
    $historyId = (int) $historyId;
    if ($historyId <= 0 || !ensureHrAiSearchHistoryTable($connect)) {
        return false;
    }

    $existingCards = array();
    $result = $connect->query("SELECT result_cards_json, input_tokens, output_tokens, total_tokens FROM hr_ai_search_history WHERE id = " . $historyId . " AND staff_id = " . (int) $staffId . " LIMIT 1");
    $inputTokens = 0;
    $outputTokens = 0;
    $totalTokens = 0;
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row) {
            $decodedCards = json_decode((string) $row['result_cards_json'], true);
            $existingCards = is_array($decodedCards) ? $decodedCards : array();
            $inputTokens = (int) $row['input_tokens'];
            $outputTokens = (int) $row['output_tokens'];
            $totalTokens = (int) $row['total_tokens'];
        }
        $result->free();
    }

    $cardsByLead = array();
    foreach (array_merge($existingCards, (array) $newCards) as $card) {
        if (is_array($card) && isset($card['lead_id'])) {
            $cardsByLead[(int) $card['lead_id']] = $card;
        }
    }

    $allCards = array_values($cardsByLead);
    $filtersUsedJson = resumeJsonEncode($filtersUsed, JSON_UNESCAPED_SLASHES);
    $cardsJson = resumeJsonEncode($allCards, JSON_UNESCAPED_SLASHES);
    $inputTokens += isset($usage['input_tokens']) ? (int) $usage['input_tokens'] : 0;
    $outputTokens += isset($usage['output_tokens']) ? (int) $usage['output_tokens'] : 0;
    $totalTokens += isset($usage['total_tokens']) ? (int) $usage['total_tokens'] : 0;
    $finalStatus = $status !== '' ? $status : ($isFinal ? 'completed' : 'running');

    $stmt = $connect->prepare("UPDATE hr_ai_search_history
        SET filters_used_json = ?,
            summary = ?,
            result_cards_json = ?,
            processed_count = ?,
            total_limit = ?,
            total_available = ?,
            input_tokens = ?,
            output_tokens = ?,
            total_tokens = ?,
            status = ?
        WHERE id = ? AND staff_id = ?");
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        'sssiiiiiisii',
        $filtersUsedJson,
        $summary,
        $cardsJson,
        $processedTo,
        $totalLimit,
        $totalAvailable,
        $inputTokens,
        $outputTokens,
        $totalTokens,
        $finalStatus,
        $historyId,
        $staffId
    );
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}

function aiSearchUsageApiConfig()
{
    global $hrcrm_ai_usage_api_url, $hrcrm_ai_usage_api_key, $hrcrm_ai_usage_tool_name;

    return array(
        'url' => isset($hrcrm_ai_usage_api_url) && trim((string) $hrcrm_ai_usage_api_url) !== '' ? trim((string) $hrcrm_ai_usage_api_url) : 'https://digichefs.in/ai-usage-api/log-usage.php',
        'api_key' => isset($hrcrm_ai_usage_api_key) && trim((string) $hrcrm_ai_usage_api_key) !== '' ? trim((string) $hrcrm_ai_usage_api_key) : 'dgcf_ai_usage_2026_x7Kp92LmQ4vN8zR1tB6sY3wE',
        'tool_name' => isset($hrcrm_ai_usage_tool_name) && trim((string) $hrcrm_ai_usage_tool_name) !== '' ? trim((string) $hrcrm_ai_usage_tool_name) : 'HrCrm'
    );
}

function aiSearchPostUsageToExternalApi($usage, $notes)
{
    if (!function_exists('curl_init')) {
        return array('ok' => false, 'message' => 'cURL is not available.');
    }

    $config = aiSearchUsageApiConfig();
    if ($config['url'] === '' || $config['api_key'] === '') {
        return array('ok' => false, 'message' => 'Usage API is not configured.');
    }

    $payload = array(
        'tool_name' => $config['tool_name'],
        'input_tokens' => isset($usage['input_tokens']) ? (int) $usage['input_tokens'] : 0,
        'output_tokens' => isset($usage['output_tokens']) ? (int) $usage['output_tokens'] : 0,
        'total_tokens' => isset($usage['total_tokens']) ? (int) $usage['total_tokens'] : 0,
        'image_count' => 0,
        'status' => 'success',
        'notes' => $notes
    );

    $ch = curl_init($config['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-API-KEY: ' . $config['api_key'],
        'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, resumeJsonEncode($payload, JSON_UNESCAPED_SLASHES));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    $rawResponse = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = is_string($rawResponse) ? json_decode($rawResponse, true) : null;

    return array(
        'ok' => $rawResponse !== false && $httpCode < 400 && is_array($decoded) && !empty($decoded['success']),
        'http_code' => $httpCode,
        'message' => $curlError,
        'response' => is_array($decoded) ? $decoded : $rawResponse
    );
}

function aiSearchLogUsage($connect, $staffId, $aiResult, $filters, $batchRows, $offset, $batchSize, $status = 'success')
{
    if (!ensureHrAiUsageLogsTable($connect)) {
        return array('ok' => false, 'message' => 'Could not create usage log table.');
    }

    $usage = normalizeGeminiUsageForLog(isset($aiResult['usage']) ? $aiResult['usage'] : array());
    $geminiStatus = getGeminiResumeStatus();
    $modelName = isset($geminiStatus['model']) ? (string) $geminiStatus['model'] : '';
    $notes = resumeJsonEncode(array(
        'feature' => 'ai_search',
        'staff_id' => (int) $staffId,
        'candidate_count' => count($batchRows),
        'candidate_ids' => array_values(array_map(function ($row) {
            return isset($row['lead_id']) ? (int) $row['lead_id'] : 0;
        }, (array) $batchRows)),
        'batch_offset' => (int) $offset,
        'batch_size' => (int) $batchSize,
        'filters' => $filters
    ), JSON_UNESCAPED_SLASHES);

    $external = aiSearchPostUsageToExternalApi($usage, $notes);
    $externalUsageId = null;
    if (!empty($external['response']['data']['id'])) {
        $externalUsageId = (int) $external['response']['data']['id'];
    }
    $externalData = !empty($external['response']['data']) && is_array($external['response']['data']) ? $external['response']['data'] : array();
    $inputCost = isset($externalData['input_cost']) ? (float) $externalData['input_cost'] : 0.0;
    $outputCost = isset($externalData['output_cost']) ? (float) $externalData['output_cost'] : 0.0;
    $totalCost = isset($externalData['total_cost']) ? (float) $externalData['total_cost'] : ($inputCost + $outputCost);
    $currency = isset($externalData['currency']) && trim((string) $externalData['currency']) !== '' ? trim((string) $externalData['currency']) : 'USD';
    $externalJson = resumeJsonEncode($external, JSON_UNESCAPED_SLASHES);

    $toolName = aiSearchUsageApiConfig()['tool_name'];
    $featureName = 'ai_search';
    $inputTokens = (int) $usage['input_tokens'];
    $outputTokens = (int) $usage['output_tokens'];
    $totalTokens = (int) $usage['total_tokens'];
    $candidateCount = count($batchRows);

    $stmt = $connect->prepare("INSERT INTO hr_ai_usage_logs (
        staff_id, tool_name, feature_name, model_name, input_tokens, output_tokens, total_tokens,
        candidate_count, batch_offset, batch_size, status, external_usage_id, input_cost, output_cost,
        total_cost, currency, external_response_json, notes
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        return array('ok' => false, 'message' => 'Could not prepare usage log insert.');
    }

    $stmt->bind_param(
        'isssiiiiiisidddsss',
        $staffId,
        $toolName,
        $featureName,
        $modelName,
        $inputTokens,
        $outputTokens,
        $totalTokens,
        $candidateCount,
        $offset,
        $batchSize,
        $status,
        $externalUsageId,
        $inputCost,
        $outputCost,
        $totalCost,
        $currency,
        $externalJson,
        $notes
    );
    $ok = $stmt->execute();
    $localId = $stmt->insert_id;
    $stmt->close();

    return array(
        'ok' => $ok,
        'local_id' => $localId,
        'external' => $external,
        'usage' => array_merge($usage, array(
            'input_cost' => $inputCost,
            'output_cost' => $outputCost,
            'total_cost' => $totalCost,
            'currency' => $currency
        ))
    );
}

function aiSearchHiringBrief($filters, $instructions)
{
    $brief = "Structured recruiter filters:";
    $brief .= "\nRole filter ID: " . (int) $filters['role'];
    $brief .= "\nExperience minimum: " . $filters['experience_min'];
    $brief .= "\nExperience maximum: " . $filters['experience_max'];
    $brief .= "\nLead status ID: " . (int) $filters['lead_status'];
    $brief .= "\nLocation filter: " . $filters['city'];
    $brief .= "\nCurrent CTC maximum: " . $filters['current_ctc'];
    $brief .= "\nMax CTC / expected CTC maximum: " . $filters['expected_ctc'];
    $brief .= "\nNotice period maximum: " . ($filters['notice_period'] !== '' ? $filters['notice_period'] : 'none');
    $brief .= "\nDate added filter: " . ($filters['interval'] !== '' ? $filters['interval'] : 'none');

    if (trim((string) $instructions) !== '') {
        $brief .= "\n\nAI scoring instructions:\n" . trim((string) $instructions);
    }

    $brief .= "\n\nTask: Score and summarize the provided candidates only. The structured filters already decided which resumes are eligible.";
    $brief .= "\nWrite from an HR screening perspective. For every candidate, compare resume evidence plus CRM data against role fit, experience range, current/expected salary, location, notice period, skills, stability/risk, and whether the person is worth prioritizing for outreach.";
    $brief .= "\nThe ai_generated_candidate_summary must be in-depth: 6 to 8 practical sentences, not a short tagline.";

    return $brief;
}

function aiSearchInsightMap($aiResult, $inputRows)
{
    $insightMap = array();
    if (empty($aiResult['parsed']['all_candidates']) || !is_array($aiResult['parsed']['all_candidates'])) {
        return $insightMap;
    }

    $nameMap = array();
    $candidateIds = array();
    foreach ($inputRows as $candidateRow) {
        $normalizedName = normalizeResumeCandidateKey(isset($candidateRow['lead_name']) ? $candidateRow['lead_name'] : '');
        if ($normalizedName !== '') {
            $nameMap[$normalizedName] = (int) $candidateRow['lead_id'];
        }
        $candidateIds[] = (int) $candidateRow['lead_id'];
    }

    foreach (array_values($aiResult['parsed']['all_candidates']) as $index => $match) {
        $leadId = isset($match['lead_id']) ? (int) $match['lead_id'] : 0;
        if ($leadId <= 0 && !empty($match['name'])) {
            $normalizedName = normalizeResumeCandidateKey($match['name']);
            if ($normalizedName !== '' && isset($nameMap[$normalizedName])) {
                $leadId = (int) $nameMap[$normalizedName];
            }
        }
        if ($leadId <= 0 && isset($candidateIds[$index])) {
            $leadId = (int) $candidateIds[$index];
        }
        if ($leadId > 0) {
            $insightMap[$leadId] = $match;
        }
    }

    return $insightMap;
}

function aiSearchBuildCard($row, $insight, $cardNumber)
{
    $leadId = isset($row['lead_id']) ? (int) $row['lead_id'] : 0;
    $score = 0;
    if (isset($insight['candidate_match_score'])) {
        $score = (int) $insight['candidate_match_score'];
    } elseif (isset($insight['match_score'])) {
        $score = (int) $insight['match_score'];
    }

    $conversion = buildResumeConversionInsight(
        isset($row['dateadded']) ? (string) $row['dateadded'] : '',
        isset($row['relevance_score']) ? (int) $row['relevance_score'] : 0
    );

    return array(
        'card_number' => $cardNumber,
        'lead_id' => $leadId,
        'id' => $leadId,
        'name' => isset($row['lead_name']) ? (string) $row['lead_name'] : '',
        'email' => isset($row['lead_email']) ? (string) $row['lead_email'] : '',
        'phone' => isset($row['lead_phone']) ? (string) $row['lead_phone'] : '',
        'resume' => isset($row['original_resume_name']) ? (string) $row['original_resume_name'] : '',
        'resume_status' => isset($row['extraction_status']) ? (string) $row['extraction_status'] : '',
        'role' => function_exists('getroletext') ? trim((string) getroletext((string) $row['roles'])) : (isset($row['roles']) ? (string) $row['roles'] : ''),
        'roles' => function_exists('getroletext') ? trim((string) getroletext((string) $row['roles'])) : (isset($row['roles']) ? (string) $row['roles'] : ''),
        'role_ids' => isset($row['roles']) ? (string) $row['roles'] : '',
        'status' => isset($row['lead_status_name']) ? (string) $row['lead_status_name'] : '',
        'status_id' => isset($row['status']) ? (int) $row['status'] : 0,
        'source' => isset($row['lead_source_name']) ? (string) $row['lead_source_name'] : '',
        'source_id' => isset($row['source']) ? (int) $row['source'] : 0,
        'city' => aiSearchCleanValue(isset($row['city']) ? $row['city'] : ''),
        'country' => aiSearchCleanValue(isset($row['country']) ? $row['country'] : ''),
        'state' => aiSearchCleanValue(isset($row['state']) ? $row['state'] : ''),
        'street' => aiSearchCleanValue(isset($row['street']) ? $row['street'] : ''),
        'pincode' => aiSearchCleanValue(isset($row['zip']) ? $row['zip'] : ''),
        'relocate' => aiSearchCleanValue(isset($row['willing_to_relocate']) ? $row['willing_to_relocate'] : ''),
        'experience' => formatDynamicExperienceLabel(isset($row['experiance']) ? $row['experiance'] : '', isset($row['dateadded']) ? $row['dateadded'] : ''),
        'raw_experience' => isset($row['experiance']) ? (string) $row['experiance'] : '',
        'current_ctc' => aiSearchCleanValue(isset($row['csalary']) ? $row['csalary'] : ''),
        'expected_ctc' => aiSearchCleanValue(isset($row['esalary']) ? $row['esalary'] : ''),
        'notice_period' => aiSearchCleanValue(isset($row['nperiod']) ? $row['nperiod'] : ''),
        'qualification' => aiSearchCleanValue(isset($row['qualification']) ? $row['qualification'] : ''),
        'current_title' => aiSearchCleanValue(isset($row['cjtitle']) ? $row['cjtitle'] : ''),
        'employer' => aiSearchCleanValue(isset($row['cemployer']) ? $row['cemployer'] : ''),
        'skills' => aiSearchCleanValue(isset($row['skillset']) && trim((string) $row['skillset']) !== '' ? $row['skillset'] : (isset($row['extracted_skills']) ? $row['extracted_skills'] : '')),
        'additional_info' => aiSearchCleanValue(isset($row['ainfo']) ? $row['ainfo'] : ''),
        'applied_date' => formatResumeApplyDate(isset($row['dateadded']) ? $row['dateadded'] : ''),
        'date_added' => isset($row['dateadded']) ? (string) $row['dateadded'] : '',
        'conversion' => $conversion,
        'ai_score' => max(0, min(100, $score)),
        'ai_summary' => isset($insight['ai_generated_candidate_summary']) ? (string) $insight['ai_generated_candidate_summary'] : (isset($insight['why']) ? (string) $insight['why'] : ''),
        'why' => isset($insight['why']) ? (string) $insight['why'] : '',
        'interview_focus' => isset($insight['interview_focus']) ? (string) $insight['interview_focus'] : '',
        'questions' => isset($insight['recommended_interview_questions']) && is_array($insight['recommended_interview_questions']) ? $insight['recommended_interview_questions'] : array(),
        'risks' => isset($insight['risk_indicators']) && is_array($insight['risk_indicators']) ? $insight['risk_indicators'] : array()
    );
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    aiSearchJson(array('success' => false, 'message' => 'Only POST is allowed.'));
}

$filters = array(
    'q' => '',
    'keywords' => aiSearchKeywords(aiSearchPostValue('instructions')),
    'role' => (int) aiSearchPostValue('role', 0),
    'experience' => '',
    'experience_min' => aiSearchPostValue('experience_min'),
    'experience_max' => aiSearchPostValue('experience_max'),
    'lead_status' => (int) aiSearchPostValue('lead_status', 0),
    'source' => (int) aiSearchPostValue('source', 0),
    'city' => aiSearchPostValue('city'),
    'relocate' => aiSearchPostValue('relocate'),
    'current_ctc' => aiSearchPostValue('current_ctc'),
    'expected_ctc' => aiSearchPostValue('expected_ctc'),
    'notice_period' => aiSearchPostValue('notice_period'),
    'interval' => aiSearchPostValue('interval')
);

if ($filters['role'] <= 0 || $filters['experience_min'] === '' || $filters['experience_max'] === '' || $filters['city'] === '' || $filters['current_ctc'] === '' || $filters['expected_ctc'] === '') {
    aiSearchJson(array(
        'success' => false,
        'message' => 'Please select Role and enter Min Experience, Max Experience, Location, Current CTC, and Max CTC.'
    ));
}

$totalLimit = max(10, min(50, (int) aiSearchPostValue('total_limit', 50)));
$offset = max(0, (int) aiSearchPostValue('offset', 0));
$batchSize = max(1, min(10, (int) aiSearchPostValue('batch_size', 10)));
$instructions = aiSearchPostValue('instructions');
$staffId = max(0, (int) aiSearchPostValue('staff_id', 0));
$historyId = max(0, (int) aiSearchPostValue('history_id', 0));

$roleOptions = fetchResumeRoleOptions($connect);
$statusOptions = fetchResumeLeadStatusOptions($connect);
$sourceOptions = fetchResumeSourceOptions($connect);
$hiringBrief = aiSearchHiringBrief($filters, $instructions);
$historyFilters = $filters;
$historyFilters['instructions'] = $instructions;
$historyFilters['total_limit'] = (string) $totalLimit;
if ($historyId <= 0 && $offset === 0) {
    $historyId = aiSearchCreateHistory($connect, $staffId, $historyFilters, $roleOptions, $totalLimit);
}
$candidateResult = fetchResumeLeadSearchResults($connect, $filters, 1, $totalLimit);
$candidateRows = isset($candidateResult['rows']) ? $candidateResult['rows'] : array();
$batchRows = array_slice($candidateRows, $offset, $batchSize);

$filtersUsedPayload = array(
    'Role' => aiSearchOptionName($roleOptions, $filters['role'], 'Any role'),
    'Experience' => $filters['experience_min'] . ' to ' . $filters['experience_max'] . ' years',
    'Status' => aiSearchOptionName($statusOptions, $filters['lead_status'], 'Any status'),
    'Location' => $filters['city'],
    'Current CTC' => '<= ' . $filters['current_ctc'],
    'Max CTC' => '<= ' . $filters['expected_ctc'],
    'Notice Period' => $filters['notice_period'] !== '' ? '<= ' . $filters['notice_period'] . ' days' : 'Any',
    'Date Added' => aiSearchIntervalLabel($filters['interval'])
);

if (empty($candidateRows)) {
    aiSearchUpdateHistory($connect, $historyId, $staffId, $filtersUsedPayload, '', array(), 0, $totalLimit, 0, array(), true, 'empty');
    aiSearchJson(array(
        'success' => false,
        'message' => 'No resume files match the selected filters.',
        'cards' => array(),
        'history_id' => $historyId,
        'filters_used' => $filtersUsedPayload
    ));
}

if (empty($batchRows)) {
    aiSearchUpdateHistory($connect, $historyId, $staffId, $filtersUsedPayload, '', array(), $offset, $totalLimit, count($candidateRows), array(), true, 'completed');
    aiSearchJson(array(
        'success' => true,
        'message' => 'No more candidates are available.',
        'cards' => array(),
        'history_id' => $historyId,
        'batch' => array(
            'offset' => $offset,
            'batch_size' => $batchSize,
            'total_available' => count($candidateRows),
            'processed_to' => $offset,
            'is_final' => true
        )
    ));
}

$aiFilters = $filters;
$aiFilters['instructions'] = $instructions;
$aiFilters['batch'] = array(
    'offset' => $offset,
    'batch_size' => $batchSize,
    'total_limit' => $totalLimit
);

$aiResult = requestGeminiResumeInsights($hiringBrief, $batchRows, $aiFilters);
if (empty($aiResult['ok'])) {
    $usageLog = aiSearchLogUsage($connect, $staffId, $aiResult, $aiFilters, $batchRows, $offset, $batchSize, 'error');
    aiSearchUpdateHistory($connect, $historyId, $staffId, $filtersUsedPayload, '', array(), $offset, $totalLimit, count($candidateRows), isset($usageLog['usage']) ? $usageLog['usage'] : array(), false, 'error');
    aiSearchJson(array(
        'success' => false,
        'message' => isset($aiResult['message']) ? $aiResult['message'] : 'AI batch failed.',
        'cards' => array(),
        'history_id' => $historyId,
        'usage' => isset($usageLog['usage']) ? $usageLog['usage'] : normalizeGeminiUsageForLog(array()),
        'usage_log' => $usageLog,
        'candidate_errors' => isset($aiResult['candidate_errors']) ? $aiResult['candidate_errors'] : array(),
        'batch' => array(
            'offset' => $offset,
            'batch_size' => $batchSize,
            'total_available' => count($candidateRows),
            'processed_to' => $offset,
            'is_final' => false
        )
    ));
}

$usageLog = aiSearchLogUsage($connect, $staffId, $aiResult, $aiFilters, $batchRows, $offset, $batchSize, 'success');

$insightMap = aiSearchInsightMap($aiResult, $batchRows);
$cards = array();
foreach ($batchRows as $index => $row) {
    $leadId = isset($row['lead_id']) ? (int) $row['lead_id'] : 0;
    $cards[] = aiSearchBuildCard($row, isset($insightMap[$leadId]) ? $insightMap[$leadId] : array(), $offset + $index + 1);
}

$processedTo = min($offset + count($batchRows), count($candidateRows), $totalLimit);
$isFinal = $processedTo >= count($candidateRows) || $processedTo >= $totalLimit;
$summaryText = isset($aiResult['parsed']['summary']) ? $aiResult['parsed']['summary'] : '';
aiSearchUpdateHistory($connect, $historyId, $staffId, $filtersUsedPayload, $summaryText, $cards, $processedTo, $totalLimit, count($candidateRows), isset($usageLog['usage']) ? $usageLog['usage'] : array(), $isFinal, $isFinal ? 'completed' : 'running');

aiSearchJson(array(
    'success' => true,
    'message' => 'AI batch completed.',
    'cards' => $cards,
    'history_id' => $historyId,
    'summary' => $summaryText,
    'usage' => isset($usageLog['usage']) ? $usageLog['usage'] : normalizeGeminiUsageForLog(array()),
    'usage_log' => array(
        'ok' => isset($usageLog['ok']) ? $usageLog['ok'] : false,
        'local_id' => isset($usageLog['local_id']) ? $usageLog['local_id'] : null,
        'external_ok' => isset($usageLog['external']['ok']) ? $usageLog['external']['ok'] : false,
        'external_id' => isset($usageLog['external']['response']['data']['id']) ? $usageLog['external']['response']['data']['id'] : null
    ),
    'filters_used' => $filtersUsedPayload,
    'batch' => array(
        'offset' => $offset,
        'batch_size' => $batchSize,
        'total_available' => count($candidateRows),
        'processed_to' => $processedTo,
        'is_final' => $isFinal
    )
));
