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

function aiSearchHiringBrief($filters, $instructions)
{
    $brief = "Structured recruiter filters:";
    $brief .= "\nRole filter ID: " . (int) $filters['role'];
    $brief .= "\nExperience minimum: " . $filters['experience_min'];
    $brief .= "\nExperience maximum: " . $filters['experience_max'];
    $brief .= "\nLead status ID: " . (int) $filters['lead_status'];
    $brief .= "\nSource filter ID: " . (int) $filters['source'];
    $brief .= "\nLocation filter: " . $filters['city'];
    $brief .= "\nRelocation filter: " . ($filters['relocate'] !== '' ? $filters['relocate'] : 'none');
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

$roleOptions = fetchResumeRoleOptions($connect);
$statusOptions = fetchResumeLeadStatusOptions($connect);
$sourceOptions = fetchResumeSourceOptions($connect);
$hiringBrief = aiSearchHiringBrief($filters, $instructions);
$candidateResult = fetchResumeLeadSearchResults($connect, $filters, 1, $totalLimit);
$candidateRows = isset($candidateResult['rows']) ? $candidateResult['rows'] : array();
$batchRows = array_slice($candidateRows, $offset, $batchSize);

if (empty($candidateRows)) {
    aiSearchJson(array(
        'success' => false,
        'message' => 'No resume files match the selected filters.',
        'cards' => array(),
        'filters_used' => array(
            'Role' => aiSearchOptionName($roleOptions, $filters['role'], 'Any role'),
            'Experience' => $filters['experience_min'] . ' to ' . $filters['experience_max'] . ' years',
            'Status' => aiSearchOptionName($statusOptions, $filters['lead_status'], 'Any status'),
            'Source' => aiSearchOptionName($sourceOptions, $filters['source'], 'Any source'),
            'Location' => $filters['city'],
            'Current CTC' => '<= ' . $filters['current_ctc'],
            'Max CTC' => '<= ' . $filters['expected_ctc'],
            'Notice Period' => $filters['notice_period'] !== '' ? '<= ' . $filters['notice_period'] . ' days' : 'Any',
            'Date Added' => aiSearchIntervalLabel($filters['interval'])
        )
    ));
}

if (empty($batchRows)) {
    aiSearchJson(array(
        'success' => true,
        'message' => 'No more candidates are available.',
        'cards' => array(),
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
    aiSearchJson(array(
        'success' => false,
        'message' => isset($aiResult['message']) ? $aiResult['message'] : 'AI batch failed.',
        'cards' => array(),
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

$insightMap = aiSearchInsightMap($aiResult, $batchRows);
$cards = array();
foreach ($batchRows as $index => $row) {
    $leadId = isset($row['lead_id']) ? (int) $row['lead_id'] : 0;
    $cards[] = aiSearchBuildCard($row, isset($insightMap[$leadId]) ? $insightMap[$leadId] : array(), $offset + $index + 1);
}

$processedTo = min($offset + count($batchRows), count($candidateRows), $totalLimit);

aiSearchJson(array(
    'success' => true,
    'message' => 'AI batch completed.',
    'cards' => $cards,
    'summary' => isset($aiResult['parsed']['summary']) ? $aiResult['parsed']['summary'] : '',
    'filters_used' => array(
        'Role' => aiSearchOptionName($roleOptions, $filters['role'], 'Any role'),
        'Experience' => $filters['experience_min'] . ' to ' . $filters['experience_max'] . ' years',
        'Status' => aiSearchOptionName($statusOptions, $filters['lead_status'], 'Any status'),
        'Source' => aiSearchOptionName($sourceOptions, $filters['source'], 'Any source'),
        'Location' => $filters['city'],
        'Relocate' => $filters['relocate'] !== '' ? $filters['relocate'] : 'Any',
        'Current CTC' => '<= ' . $filters['current_ctc'],
        'Max CTC' => '<= ' . $filters['expected_ctc'],
        'Notice Period' => $filters['notice_period'] !== '' ? '<= ' . $filters['notice_period'] . ' days' : 'Any',
        'Date Added' => aiSearchIntervalLabel($filters['interval'])
    ),
    'batch' => array(
        'offset' => $offset,
        'batch_size' => $batchSize,
        'total_available' => count($candidateRows),
        'processed_to' => $processedTo,
        'is_final' => $processedTo >= count($candidateRows) || $processedTo >= $totalLimit
    )
));
