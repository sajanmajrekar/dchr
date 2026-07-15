<?php
ob_start();
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    @ini_set('display_errors', '0');
}
include 'inc/config.php';
?>
<?php
$template['header_link'] = 'RESUME LIBRARY';
require_once 'includes/resume_intelligence.php';

ensureResumeIntelligenceTables($connect);

function resumeLibraryJson($payload)
{
    while (ob_get_level() > 0) {
        @ob_end_clean();
    }
    header('Content-Type: application/json');
    $json = resumeJsonEncode($payload, JSON_UNESCAPED_SLASHES);
    if (!is_string($json) || $json === '') {
        resumeIntelligenceLog('resume_library', 'Failed to encode AJAX JSON response.', array(
            'json_last_error' => function_exists('json_last_error_msg') ? json_last_error_msg() : 'Unknown JSON error'
        ));
        echo '{"ok":false,"message":"Server could not encode the AI response as valid JSON."}';
        exit();
    }
    echo $json;
    exit();
}

function resumeLibraryEsc($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function resumeLibraryUrl($overrides = array())
{
    $params = $_GET;
    foreach ($overrides as $key => $value) {
        if ($value === null || $value === '') {
            unset($params[$key]);
        } else {
            $params[$key] = $value;
        }
    }

    $query = http_build_query($params);
    return 'resume_library.php' . ($query !== '' ? '?' . $query : '');
}

function resumeLibraryHasActiveFilters($filters)
{
    return trim((string) $filters['q']) !== ''
        || (int) $filters['role'] > 0
        || trim((string) $filters['experience']) !== ''
        || (int) $filters['lead_status'] > 0
        || (int) $filters['source'] > 0;
}

$requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
$action = isset($_POST['action']) ? trim((string) $_POST['action']) : '';

if ($requestMethod === 'POST' && $action !== '') {
    if ($action === 'ai_insights') {
        $filters = array(
            'q' => isset($_POST['q']) ? trim((string) $_POST['q']) : '',
            'role' => isset($_POST['role']) ? (int) $_POST['role'] : 0,
            'experience' => isset($_POST['experience']) ? trim((string) $_POST['experience']) : '',
            'lead_status' => isset($_POST['lead_status']) ? (int) $_POST['lead_status'] : 0,
            'source' => isset($_POST['source']) ? (int) $_POST['source'] : 0
        );

        if (!resumeLibraryHasActiveFilters($filters)) {
            resumeLibraryJson(array(
                'ok' => false,
                'message' => 'Use keyword or role filter first, then run AI Search.'
            ));
        }

        $hiringBrief = 'Keyword search: ' . ($filters['q'] !== '' ? $filters['q'] : 'none');
        $hiringBrief .= "\nRole filter ID: " . (int) $filters['role'];
        $hiringBrief .= "\nExperience filter: " . ($filters['experience'] !== '' ? $filters['experience'] : 'none');
        $hiringBrief .= "\nLead status ID: " . (int) $filters['lead_status'];
        $hiringBrief .= "\nSource filter ID: " . (int) $filters['source'];
        $hiringBrief .= "\nTask: Review the top search results and explain fit for this search.";

        $uiResultLimit = 10;
        $aiBatchLimit = 4;
        $candidateResult = fetchResumeSearchResults($connect, array(
            'q' => $filters['q'],
            'role' => $filters['role'],
            'experience' => $filters['experience'],
            'lead_status' => $filters['lead_status'],
            'source' => $filters['source'],
            'status' => 'completed'
        ), 1, $uiResultLimit);

        if (empty($candidateResult['rows'])) {
            resumeLibraryJson(array(
                'ok' => false,
                'message' => 'No completed resumes match the current search filters.'
            ));
        }

        $aiInputRows = array_slice($candidateResult['rows'], 0, $aiBatchLimit);
        $aiResult = requestGeminiResumeInsights($hiringBrief, $aiInputRows, $filters);
        $candidateRowsForUi = array();
        foreach ($candidateResult['rows'] as $candidateRow) {
            $candidateRow['role_text'] = trim((string) getroletext((string) $candidateRow['roles']));
            $candidateRow['dynamic_experience_label'] = formatDynamicExperienceLabel(
                isset($candidateRow['experiance']) ? (string) $candidateRow['experiance'] : '',
                isset($candidateRow['dateadded']) ? (string) $candidateRow['dateadded'] : ''
            );
            $candidateRow['formatted_apply_date'] = formatResumeApplyDate(
                isset($candidateRow['dateadded']) ? (string) $candidateRow['dateadded'] : ''
            );
            $candidateRow['conversion_insight'] = buildResumeConversionInsight(
                isset($candidateRow['dateadded']) ? (string) $candidateRow['dateadded'] : '',
                isset($candidateRow['relevance_score']) ? (int) $candidateRow['relevance_score'] : 0
            );
            $candidateRowsForUi[] = $candidateRow;
        }
        $aiResult['candidate_rows'] = $candidateRowsForUi;
        $aiResult['ai_batch_limit'] = $aiBatchLimit;
        if (!empty($aiResult['ok']) && !empty($aiResult['parsed']['all_candidates']) && is_array($aiResult['parsed']['all_candidates'])) {
            $insightMap = array();
            $nameMap = array();
            $candidateIds = array();
            foreach ($aiInputRows as $candidateRow) {
                $normalizedName = normalizeResumeCandidateKey(isset($candidateRow['lead_name']) ? $candidateRow['lead_name'] : '');
                if ($normalizedName !== '') {
                    $nameMap[$normalizedName] = (int) $candidateRow['lead_id'];
                }
                $candidateIds[] = (int) $candidateRow['lead_id'];
            }
            $sequentialMatches = array_values($aiResult['parsed']['all_candidates']);
            foreach ($sequentialMatches as $index => $match) {
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
            $aiResult['insight_map'] = $insightMap;
        }
        resumeLibraryJson($aiResult);
    }
}

$filters = array(
    'q' => isset($_GET['q']) ? trim((string) $_GET['q']) : '',
    'role' => isset($_GET['role']) ? (int) $_GET['role'] : 0,
    'experience' => isset($_GET['experience']) ? trim((string) $_GET['experience']) : '',
    'lead_status' => isset($_GET['lead_status']) ? (int) $_GET['lead_status'] : 0,
    'source' => isset($_GET['source']) ? (int) $_GET['source'] : 0
);

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$perPage = 10;

$stats = fetchResumeDocumentStats($connect);
$queueStats = fetchResumeQueueStats($connect);
$roleOptions = fetchResumeRoleOptions($connect);
$leadStatusOptions = fetchResumeLeadStatusOptions($connect);
$sourceOptions = fetchResumeSourceOptions($connect);
$hasActiveFilters = resumeLibraryHasActiveFilters($filters);
$results = array(
    'rows' => array(),
    'total' => 0,
    'page' => 1,
    'per_page' => $perPage,
    'pages' => 1
);

if ($hasActiveFilters) {
    $results = fetchResumeSearchResults($connect, array(
        'q' => $filters['q'],
        'role' => $filters['role'],
        'experience' => $filters['experience'],
        'lead_status' => $filters['lead_status'],
        'source' => $filters['source'],
        'status' => 'completed'
    ), $page, $perPage);
}
?>
<?php include 'inc/template_start.php'; ?>
<?php include 'inc/page_head.php'; ?>

<style type="text/css">
    .resume-panel {
        background: #ffffff;
        border: 1px solid #e6e9ef;
        border-radius: 8px;
        padding: 22px;
        margin-bottom: 20px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }
    .resume-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #fff;
        border-radius: 10px;
        padding: 24px;
        margin-bottom: 22px;
    }
    .resume-hero h2 {
        margin: 0 0 10px 0;
        font-size: 30px;
        font-weight: 600;
    }
    .resume-hero p {
        margin: 0;
        color: rgba(255, 255, 255, 0.82);
        font-size: 15px;
        line-height: 1.6;
    }
    .mini-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-top: 18px;
    }
    .mini-stat {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 8px;
        padding: 14px;
    }
    .mini-stat-value {
        font-size: 28px;
        line-height: 1.1;
        margin-bottom: 4px;
    }
    .mini-stat-label {
        font-size: 11px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.74);
    }
    .candidate-card {
        border: 1px solid #edf1f5;
        border-radius: 10px;
        padding: 18px;
        margin-bottom: 16px;
        background: #fcfdff;
    }
    .candidate-grid {
        display: block;
    }
    .candidate-grid.ai-mode {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }
    .candidate-grid.ai-mode .candidate-card {
        margin-bottom: 0;
    }
    .candidate-grid.ai-mode .card-system-data,
    .candidate-grid.ai-mode .card-ai-insight {
        display: none !important;
    }
    .candidate-head {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 12px;
    }
    .candidate-name {
        font-size: 20px;
        font-weight: 600;
        color: #172033;
    }
    .candidate-meta {
        color: #5c6677;
        line-height: 1.7;
    }
    .experience-pill {
        display: inline-block;
        background: #e8f6ef;
        color: #177245;
        border: 1px solid #cdebd9;
        border-radius: 999px;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
    .skills-wrap .label {
        display: inline-block;
        margin: 0 6px 6px 0;
        font-size: 11px;
    }
    .resume-preview {
        color: #4d5768;
        line-height: 1.65;
        margin-top: 12px;
    }
    .side-title {
        margin-top: 0;
        margin-bottom: 14px;
        font-size: 20px;
        color: #172033;
    }
    .card-ai-insight {
        background: #f7fafc;
        border: 1px solid #dce8f4;
        border-radius: 10px;
        padding: 14px 16px;
        margin-top: 14px;
        display: none;
    }
    .card-ai-insight.is-visible {
        display: block;
    }
    .card-score-row {
        display: none;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid #e9eef4;
    }
    .candidate-card.ai-ready .card-score-row {
        display: flex;
    }
    .card-ai-score {
        display: inline-block;
    }
    .card-match-score {
        display: inline-block;
        margin-left: 6px;
    }
    .card-ai-why,
    .card-ai-focus {
        margin-top: 8px;
        color: #4d5768;
    }
    .card-system-data {
        display: block;
    }
    .candidate-card.ai-collapsed .card-system-data,
    .candidate-card.ai-collapsed .card-ai-insight {
        display: none;
    }
    .pagination-wrap {
        margin-top: 18px;
        text-align: center;
    }
    .ai-progress-wrap {
        display: none;
        margin-top: 16px;
    }
    .ai-progress-wrap.is-visible {
        display: block;
    }
    .ai-summary-panel {
        display: none;
        margin-bottom: 18px;
        background: #f8fbff;
        border: 1px solid #dbe8f5;
        border-radius: 10px;
        padding: 18px;
    }
    .ai-summary-panel.is-visible {
        display: block;
    }
    .ai-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-top: 14px;
    }
    .ai-summary-box {
        background: #fff;
        border: 1px solid #e3edf7;
        border-radius: 8px;
        padding: 14px;
    }
    .ai-heatmap-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: flex-start;
        margin-top: 8px;
    }
    .ai-heatmap-list .label {
        display: inline-flex;
        max-width: 100%;
        white-space: normal;
        line-height: 1.4;
        text-align: left;
    }
    .ai-summary-copy {
        margin-top: 8px;
        color: #4d5768;
        line-height: 1.6;
        white-space: pre-wrap;
        word-break: break-word;
    }
    @media (max-width: 991px) {
        .candidate-grid.ai-mode {
            grid-template-columns: 1fr;
        }
        .ai-summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div id="page-content">
    <div class="content-header">
        <div class="header-section">
            <h1>
                Resume Library<br>
                <small>Search by keyword and role, see updated current experience, and ask Gemini for shortlist insights.</small>
            </h1>
        </div>
    </div>

    <ul class="breadcrumb breadcrumb-top">
        <li>HR CRM</li>
        <li><a href="resume_intelligence.php">Resume Intelligence</a></li>
        <li><a href="resume_library.php">Resume Library</a></li>
    </ul>

    <div class="resume-hero">
        <h2>Recruiter Search + AI Copilot</h2>
        <p>The indexed resumes are now usable. Search the processed pool, check live current experience, and ask Gemini to explain which candidates best fit your hiring brief.</p>
        <div class="mini-stats">
            <div class="mini-stat">
                <div class="mini-stat-value"><?php echo (int) $stats['completed']; ?></div>
                <div class="mini-stat-label">Completed Resumes</div>
            </div>
            <div class="mini-stat">
                <div class="mini-stat-value"><?php echo $hasActiveFilters ? (int) $results['total'] : 0; ?></div>
                <div class="mini-stat-label">Matching Candidates</div>
            </div>
            <div class="mini-stat">
                <div class="mini-stat-value"><?php echo (int) $queueStats['remaining']; ?></div>
                <div class="mini-stat-label">Remaining Queue</div>
            </div>
        </div>
    </div>

    <div class="resume-panel">
        <form method="get" action="resume_library.php" class="form-horizontal">
            <div class="row">
                <div class="col-md-4">
                    <label for="q">Search keyword</label>
                    <input type="text" id="q" name="q" class="form-control input-lg" value="<?php echo resumeLibraryEsc($filters['q']); ?>" placeholder="SEO, paid media, content, Meta ads, GA4, copywriting">
                </div>
                <div class="col-md-2">
                    <label for="role">Role</label>
                    <select id="role" name="role" class="form-control input-lg">
                        <option value="0">All roles</option>
                        <?php foreach ($roleOptions as $roleOption) { ?>
                            <option value="<?php echo (int) $roleOption['id']; ?>"<?php echo (int) $filters['role'] === (int) $roleOption['id'] ? ' selected' : ''; ?>><?php echo resumeLibraryEsc($roleOption['name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="experience">Experience</label>
                    <input type="text" id="experience" name="experience" class="form-control input-lg" value="<?php echo resumeLibraryEsc($filters['experience']); ?>" placeholder="2, 3+, 5 years">
                </div>
                <div class="col-md-2">
                    <label for="lead_status">Status</label>
                    <select id="lead_status" name="lead_status" class="form-control input-lg">
                        <option value="0">Please select</option>
                        <?php foreach ($leadStatusOptions as $leadStatusOption) { ?>
                            <option value="<?php echo (int) $leadStatusOption['id']; ?>"<?php echo (int) $filters['lead_status'] === (int) $leadStatusOption['id'] ? ' selected' : ''; ?>><?php echo resumeLibraryEsc($leadStatusOption['name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="source">Source</label>
                    <select id="source" name="source" class="form-control input-lg">
                        <option value="0">Please select</option>
                        <?php foreach ($sourceOptions as $sourceOption) { ?>
                            <option value="<?php echo (int) $sourceOption['id']; ?>"<?php echo (int) $filters['source'] === (int) $sourceOption['id'] ? ' selected' : ''; ?>><?php echo resumeLibraryEsc($sourceOption['name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div style="margin-top:16px;">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-search"></i> Normal Search</button>
                <button type="button" id="runAiInsightsBtn" class="btn btn-success btn-lg"><i class="fa fa-magic"></i> AI Search</button>
                <a href="resume_library.php" class="btn btn-default btn-lg">Reset</a>
                <span id="aiInsightMessage" style="margin-left:12px; color:#666;"></span>
            </div>
            <div id="aiProgressWrap" class="ai-progress-wrap">
                <div class="progress progress-striped active" style="margin-bottom:8px;">
                    <div id="aiProgressBar" class="progress-bar progress-bar-success" style="width: 8%;">8%</div>
                </div>
                <div id="aiProgressText" class="text-muted">Preparing top 10 matches...</div>
            </div>
        </form>
    </div>

    <div class="resume-panel">
        <h3 class="side-title">Matching Candidates</h3>
        <p class="text-muted" style="margin-top:-4px;">Normal search shows the matches. AI Search takes the top 10 current results and writes insights directly into those same cards.</p>
        <div id="aiSummaryPanel" class="ai-summary-panel">
            <h4 style="margin-top:0;">AI Shortlist Summary</h4>
            <p id="aiSummaryText" class="text-muted" style="margin-bottom:10px;">Waiting for AI shortlist...</p>
            <div class="ai-summary-grid">
                <div class="ai-summary-box">
                    <strong>Top 5 Recommended Candidates</strong>
                    <div id="aiTopFiveList" style="margin-top:8px;"></div>
                </div>
                <div class="ai-summary-box">
                    <strong>Skills Heatmap</strong>
                    <div id="aiSkillsHeatmap" style="margin-top:8px;"></div>
                </div>
                <div class="ai-summary-box">
                    <strong>Experience Distribution</strong>
                    <div id="aiExperienceDistribution" style="margin-top:8px;"></div>
                </div>
                <div class="ai-summary-box">
                    <strong>Notice Period Analysis</strong>
                    <div id="aiNoticePeriodAnalysis" style="margin-top:8px;"></div>
                </div>
            </div>
        </div>
        <?php if (!$hasActiveFilters) { ?>
            <div class="alert alert-info" style="margin-bottom:0;">Use any filter from the search form first. Results will appear only after you search.</div>
        <?php } elseif (empty($results['rows'])) { ?>
            <div class="alert alert-info" style="margin-bottom:0;">No completed resumes matched your current filter selection.</div>
        <?php } ?>

        <div id="candidateGrid" class="candidate-grid">
        <?php foreach ($results['rows'] as $row) { ?>
                    <?php
                    $candidateEmail = trim((string) ($row['extracted_email'] !== '' ? $row['extracted_email'] : $row['lead_email']));
                    $candidatePhone = trim((string) ($row['extracted_phone'] !== '' ? $row['extracted_phone'] : $row['lead_phone']));
                    $experienceLabel = formatDynamicExperienceLabel((string) $row['experiance'], (string) $row['dateadded']);
                    $applyDateLabel = formatResumeApplyDate((string) $row['dateadded']);
                    $conversionInsight = buildResumeConversionInsight((string) $row['dateadded'], isset($row['relevance_score']) ? (int) $row['relevance_score'] : 0);
                    $skills = array_filter(array_map('trim', explode(',', (string) $row['extracted_skills'])));
                    $previewText = trim((string) $row['raw_text']);
                    if (strlen($previewText) > 340) {
                        $previewText = substr($previewText, 0, 340) . '...';
                    }
                    ?>
                    <div class="candidate-card"
                         data-lead-id="<?php echo (int) $row['lead_id']; ?>"
                         data-resume-url="<?php echo resumeLibraryEsc('view_resume.php?file=' . rawurlencode((string) $row['original_resume_name'])); ?>"
                         data-file-extension="<?php echo resumeLibraryEsc((string) $row['file_extension']); ?>"
                         data-apply-date="<?php echo resumeLibraryEsc($applyDateLabel); ?>"
                         data-conversion-label="<?php echo resumeLibraryEsc($conversionInsight['label']); ?>"
                         data-conversion-reason="<?php echo resumeLibraryEsc($conversionInsight['reason']); ?>">
                        <div class="candidate-head">
                            <div>
                                <div class="candidate-name"><?php echo resumeLibraryEsc($row['lead_name']); ?></div>
                                <div class="candidate-meta">
                                    <strong>Email:</strong> <?php echo resumeLibraryEsc($candidateEmail !== '' ? $candidateEmail : '-'); ?><br>
                                    <strong>Phone:</strong> <?php echo resumeLibraryEsc($candidatePhone !== '' ? $candidatePhone : '-'); ?><br>
                                    <strong>Role:</strong> <?php echo resumeLibraryEsc(trim((string) getroletext((string) $row['roles'])) !== '' ? trim((string) getroletext((string) $row['roles'])) : '-'); ?><br>
                                    <strong>Applied:</strong> <?php echo resumeLibraryEsc($applyDateLabel); ?><br>
                                    <strong>Conversion:</strong> <?php echo resumeLibraryEsc($conversionInsight['label']); ?>
                                </div>
                            </div>
                            <div class="experience-pill"><?php echo resumeLibraryEsc($experienceLabel); ?></div>
                        </div>

                        <div class="card-score-row">
                            <div>
                                <strong>AI Score</strong>
                                <span class="label label-success card-ai-score">Pending</span>
                            </div>
                            <button type="button" class="btn btn-xs btn-info card-open-modal">View Details</button>
                        </div>

                        <div class="card-system-data">
                            <div class="skills-wrap">
                                <?php if (!empty($skills)) { ?>
                                    <?php foreach (array_slice($skills, 0, 10) as $skill) { ?>
                                        <span class="label label-info"><?php echo resumeLibraryEsc($skill); ?></span>
                                    <?php } ?>
                                <?php } else { ?>
                                    <span class="text-muted">No extracted skills yet</span>
                                <?php } ?>
                            </div>

                            <div class="resume-preview">
                                <?php echo nl2br(resumeLibraryEsc($previewText !== '' ? $previewText : 'Preview not available yet.')); ?>
                            </div>

                            <div style="margin-top:14px;">
                                <a href="view_resume.php?file=<?php echo rawurlencode((string) $row['original_resume_name']); ?>" target="_blank" class="btn btn-success btn-sm">
                                    <i class="fa fa-eye"></i> Open Resume
                                </a>
                                <a href="candidates.php" class="btn btn-default btn-sm">
                                    <i class="fa fa-users"></i> Candidates Page
                                </a>
                            </div>
                        </div>

                        <div class="card-ai-insight">
                            <strong>AI Insight</strong>
                            <div class="card-ai-why"></div>
                            <div class="card-ai-focus"></div>
                        </div>
                    </div>
        <?php } ?>
        </div>

        <?php if ($hasActiveFilters && $results['pages'] > 1) { ?>
            <div class="pagination-wrap">
                <ul class="pagination pagination-sm">
                    <li class="<?php echo $results['page'] <= 1 ? 'disabled' : ''; ?>">
                        <a href="<?php echo $results['page'] <= 1 ? 'javascript:void(0)' : resumeLibraryUrl(array('page' => $results['page'] - 1)); ?>">Prev</a>
                    </li>
                    <?php for ($i = 1; $i <= $results['pages']; $i++) { ?>
                        <li class="<?php echo $i === (int) $results['page'] ? 'active' : ''; ?>">
                            <a href="<?php echo resumeLibraryUrl(array('page' => $i)); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php } ?>
                    <li class="<?php echo $results['page'] >= $results['pages'] ? 'disabled' : ''; ?>">
                        <a href="<?php echo $results['page'] >= $results['pages'] ? 'javascript:void(0)' : resumeLibraryUrl(array('page' => $results['page'] + 1)); ?>">Next</a>
                    </li>
                </ul>
            </div>
        <?php } ?>
    </div>
</div>

<div id="candidateInsightModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><strong id="modalCandidateName">Candidate Details</strong></h3>
            </div>
            <div class="modal-body">
                <div id="modalCandidateScore" style="margin-bottom:12px;"></div>
                <div id="modalCandidateAiSummary" style="margin-bottom:12px;"></div>
                <div id="modalCandidateAiInsight" style="margin-bottom:12px;"></div>
                <div id="modalCandidateQuestions" style="margin-bottom:12px;"></div>
                <div id="modalCandidateRisks" style="margin-bottom:12px;"></div>
                <div id="modalCandidateSystem" style="margin-bottom:12px;"></div>
                <div id="modalCandidatePdf" style="margin-bottom:0;"></div>
            </div>
        </div>
    </div>
</div>

<?php include 'inc/template_scripts.php'; ?>
<script>
$(function () {
    var aiProgressTimer = null;

    $('#runAiInsightsBtn').on('click', function () {
        if (!$('#q').val().trim() && (!$('#role').val() || $('#role').val() === '0') && !$('#experience').val().trim() && (!$('#lead_status').val() || $('#lead_status').val() === '0') && (!$('#source').val() || $('#source').val() === '0')) {
            $('#aiInsightMessage').text('Use at least one filter before running AI Search.');
            return;
        }

        $('#aiInsightMessage').text('AI is analyzing the top 10 current search results...');
        $('#aiSummaryPanel').removeClass('is-visible');
        $('#aiSummaryText').text('Waiting for AI shortlist...');
        $('#aiTopFiveList').html('');
        $('#aiSkillsHeatmap').html('');
        $('#aiExperienceDistribution').html('');
        $('#aiNoticePeriodAnalysis').html('');
        $('#candidateGrid').addClass('ai-mode');
        $('.card-ai-insight').removeClass('is-visible');
        $('.candidate-card').removeClass('ai-ready ai-collapsed');
        $('.card-ai-score').text('Pending');
        $('.candidate-card').removeData('aiPayload');
        $('.card-ai-why').html('');
        $('.card-ai-focus').html('');
        $('#aiProgressWrap').addClass('is-visible');
        updateAiProgress(8, 'Preparing top matches for AI review...');
        startAiProgressAnimation();

        $.post('resume_library.php', {
            action: 'ai_insights',
            q: $('#q').val(),
            role: $('#role').val(),
            experience: $('#experience').val(),
            lead_status: $('#lead_status').val(),
            source: $('#source').val()
        }, function (response) {
            stopAiProgressAnimation();
            if (Array.isArray(response.candidate_rows) && response.candidate_rows.length) {
                renderCandidateCards(response.candidate_rows);
                $('#candidateGrid').addClass('ai-mode');
            }

            if (!response.ok) {
                $('#aiInsightMessage').text(response.message || 'Could not generate AI insights.');
                updateAiProgress(100, 'AI search failed.');
                return;
            }

            $('#aiInsightMessage').text(response.message || 'Insights ready.');
            updateAiProgress(100, 'AI shortlist ready.');
            renderAiSummary(response.parsed || {});

            var workingInsightMap = response.insight_map || {};
            if ((!workingInsightMap || !Object.keys(workingInsightMap).length) && response.parsed && Array.isArray(response.parsed.all_candidates)) {
                workingInsightMap = buildSequentialInsightMap(response.parsed.all_candidates);
            }

            if (response.candidate_errors) {
                Object.keys(response.candidate_errors).forEach(function (leadId) {
                    var $card = $('.candidate-card[data-lead-id="' + leadId + '"]');
                    if (!$card.length) {
                        return;
                    }
                    $card.addClass('ai-ready ai-collapsed');
                    $card.find('.card-ai-score').text('Error');
                    $card.find('.card-ai-why').html('<strong>AI Summary:</strong> ' + escapeHtml(response.candidate_errors[leadId]));
                    $card.find('.card-ai-focus').html('<strong>Interview focus:</strong> Resume needs manual review.');
                    $card.find('.card-ai-insight').addClass('is-visible');
                    $card.data('aiPayload', {
                        ai_generated_candidate_summary: response.candidate_errors[leadId],
                        interview_focus: 'Resume needs manual review.',
                        recommended_interview_questions: [],
                        risk_indicators: ['AI PDF processing failed for this resume.']
                    });
                });
            }

            if (workingInsightMap && Object.keys(workingInsightMap).length) {
                sortCardsByAiScore(workingInsightMap);
                Object.keys(workingInsightMap).forEach(function (leadId) {
                    var item = workingInsightMap[leadId];
                    var $card = $('.candidate-card[data-lead-id="' + leadId + '"]');
                    if (!$card.length) {
                        return;
                    }

                    $card.addClass('ai-ready ai-collapsed');
                    $card.find('.card-ai-score').text((item.candidate_match_score || item.match_score || item.fit_score || 0) + '/100');
                    $card.find('.card-ai-why').html('<strong>AI Summary:</strong> ' + escapeHtml(item.ai_generated_candidate_summary || item.why || '-'));
                    $card.find('.card-ai-focus').html('<strong>Interview readiness:</strong> ' + escapeHtml(String(item.interview_readiness_score || '-')) + ' | <strong>Interview focus:</strong> ' + escapeHtml(item.interview_focus || '-'));
                    $card.find('.card-ai-insight').addClass('is-visible');
                    $card.data('aiPayload', item);
                });

                $('.candidate-card').each(function () {
                    var $card = $(this);
                    if (!$card.hasClass('ai-ready')) {
                        $card.addClass('ai-ready ai-collapsed');
                        $card.find('.card-ai-score').text('No score');
                        $card.find('.card-ai-why').html('<strong>AI Summary:</strong> AI did not return a mapped insight for this candidate.');
                        $card.find('.card-ai-focus').html('<strong>Interview focus:</strong> Review manually.');
                        $card.find('.card-ai-insight').addClass('is-visible');
                    }
                });
            } else {
                $('#aiInsightMessage').text('AI responded, but no card-level scores could be matched. Refine the filter and try again.');
            }
        }, 'json').fail(function () {
            stopAiProgressAnimation();
            var errorText = 'Gemini request failed.';
            if (arguments.length > 0 && arguments[0]) {
                var xhr = arguments[0];
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorText = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    errorText = 'Gemini request failed: ' + String(xhr.responseText).replace(/\s+/g, ' ').substring(0, 220);
                } else if (xhr.status) {
                    errorText = 'Gemini request failed. HTTP ' + xhr.status;
                }
            }
            $('#aiInsightMessage').text(errorText);
            updateAiProgress(100, 'AI search failed.');
        });
    });

    $(document).on('click', '.card-open-modal', function (event) {
        event.preventDefault();
        event.stopPropagation();
        var $card = $(this).closest('.candidate-card');
        var aiPayload = $card.data('aiPayload') || {};
        var resumeUrl = $card.attr('data-resume-url') || '';
        var fileExtension = String($card.attr('data-file-extension') || '').toLowerCase();
        var applyDate = $card.attr('data-apply-date') || 'Not available';
        var conversionLabel = $card.attr('data-conversion-label') || 'Needs review';
        var conversionReason = $card.attr('data-conversion-reason') || 'Manual review recommended.';
        $('#modalCandidateName').text($card.find('.candidate-name').text());
        $('#modalCandidateScore').html('<strong>Candidate match score:</strong> ' + escapeHtml($card.find('.card-ai-score').text()));
        $('#modalCandidateAiSummary').html('<strong>AI-generated candidate summary:</strong> ' + escapeHtml(aiPayload.ai_generated_candidate_summary || 'Not available'));
        $('#modalCandidateAiInsight').html('<strong>Interview readiness score:</strong> ' + escapeHtml(String(aiPayload.interview_readiness_score || 'Not available')) + '<br><strong>Interview focus:</strong> ' + escapeHtml(aiPayload.interview_focus || 'Not available') + '<br><strong>Applied date:</strong> ' + escapeHtml(applyDate) + '<br><strong>Conversion likelihood:</strong> ' + escapeHtml(conversionLabel) + '<br><strong>Why:</strong> ' + escapeHtml(conversionReason));
        $('#modalCandidateQuestions').html('<strong>Recommended interview questions:</strong><br>' + renderList(aiPayload.recommended_interview_questions));
        $('#modalCandidateRisks').html('<strong>Risk indicators:</strong><br>' + renderList(aiPayload.risk_indicators));
        $('#modalCandidateSystem').html('<strong>System data:</strong><br>' + $card.find('.candidate-meta').html() + '<br><br><strong>Resume preview:</strong><br>' + $card.find('.resume-preview').html());
        if (resumeUrl !== '' && fileExtension === 'pdf') {
            $('#modalCandidatePdf').html('<strong>Resume PDF:</strong><div style="margin-top:10px; border:1px solid #dfe5ee; border-radius:8px; overflow:hidden;"><iframe src="' + escapeHtml(resumeUrl) + '" style="width:100%; height:520px; border:0;"></iframe></div>');
        } else if (resumeUrl !== '') {
            $('#modalCandidatePdf').html('<strong>Resume Preview:</strong><br><span class="text-muted">Inline preview is available only for PDF resumes.</span><div style="margin-top:10px;"><a href="' + escapeHtml(resumeUrl) + '" target="_blank" class="btn btn-success btn-sm"><i class="fa fa-external-link"></i> Open Resume in New Tab</a></div>');
        } else {
            $('#modalCandidatePdf').html('<strong>Resume PDF:</strong><br><span class="text-muted">Resume preview not available.</span>');
        }
        $('#candidateInsightModal').modal('show');
    });

    function startAiProgressAnimation() {
        stopAiProgressAnimation();
        var percent = 8;
        aiProgressTimer = setInterval(function () {
            percent = Math.min(percent + 7, 92);
            updateAiProgress(percent, percent < 35 ? 'Collecting top keyword matches...' : (percent < 70 ? 'Uploading exact resume PDFs to AI...' : 'Generating shortlist insights...'));
        }, 900);
    }

    function stopAiProgressAnimation() {
        if (aiProgressTimer) {
            clearInterval(aiProgressTimer);
            aiProgressTimer = null;
        }
    }

    function updateAiProgress(percent, text) {
        $('#aiProgressBar').css('width', percent + '%').text(percent + '%');
        $('#aiProgressText').text(text);
    }

    function sortCardsByAiScore(insightMap) {
        var $cards = $('.candidate-card').get();
        $cards.sort(function (a, b) {
            var aId = String($(a).data('lead-id'));
            var bId = String($(b).data('lead-id'));
            var aScore = 0;
            var bScore = 0;
            if (insightMap[aId]) {
                aScore = parseInt(insightMap[aId].candidate_match_score || insightMap[aId].match_score || insightMap[aId].fit_score || 0, 10);
            }
            if (insightMap[bId]) {
                bScore = parseInt(insightMap[bId].candidate_match_score || insightMap[bId].match_score || insightMap[bId].fit_score || 0, 10);
            }
            return bScore - aScore;
        });
        $.each($cards, function (_, card) {
            $('#candidateGrid').append(card);
        });
    }

    function buildSequentialInsightMap(allCandidates) {
        var map = {};
        var $cards = $('.candidate-card');
        $cards.each(function (index) {
            if (!allCandidates[index]) {
                return;
            }
            var leadId = String($(this).data('lead-id'));
            map[leadId] = allCandidates[index];
        });
        return map;
    }

    function renderCandidateCards(rows) {
        var html = '';
        rows.forEach(function (row) {
            var candidateEmail = row.extracted_email && row.extracted_email.trim() !== '' ? row.extracted_email : (row.lead_email || '-');
            var candidatePhone = row.extracted_phone && row.extracted_phone.trim() !== '' ? row.extracted_phone : (row.lead_phone || '-');
            var roleText = row.role_text && row.role_text.trim() !== '' ? row.role_text : '-';
            var experienceLabel = row.dynamic_experience_label && row.dynamic_experience_label.trim() !== '' ? row.dynamic_experience_label : (row.experiance && row.experiance.trim() !== '' ? row.experiance : 'Not available');
            var applyDateLabel = row.formatted_apply_date && row.formatted_apply_date.trim() !== '' ? row.formatted_apply_date : 'Not available';
            var conversionLabel = row.conversion_insight && row.conversion_insight.label ? row.conversion_insight.label : 'Needs review';
            var conversionReason = row.conversion_insight && row.conversion_insight.reason ? row.conversion_insight.reason : 'Manual review recommended.';
            var previewText = row.raw_text || 'Preview not available yet.';
            if (previewText.length > 340) {
                previewText = previewText.substring(0, 340) + '...';
            }

            var skillsHtml = '<span class="text-muted">No extracted skills yet</span>';
            if (row.extracted_skills && row.extracted_skills.trim() !== '') {
                var parts = [];
                row.extracted_skills.split(',').forEach(function (skill, index) {
                    if (index < 10 && skill.trim() !== '') {
                        parts.push('<span class="label label-info">' + escapeHtml(skill.trim()) + '</span>');
                    }
                });
                if (parts.length) {
                    skillsHtml = parts.join(' ');
                }
            }

            html += '<div class="candidate-card" data-lead-id="' + escapeHtml(String(row.lead_id || '')) + '" data-resume-url="view_resume.php?file=' + encodeURIComponent(row.original_resume_name || '') + '" data-file-extension="' + escapeHtml(String(row.file_extension || '')) + '" data-apply-date="' + escapeHtml(applyDateLabel) + '" data-conversion-label="' + escapeHtml(conversionLabel) + '" data-conversion-reason="' + escapeHtml(conversionReason) + '">';
            html += '<div class="candidate-head">';
            html += '<div>';
            html += '<div class="candidate-name">' + escapeHtml(row.lead_name || '') + '</div>';
            html += '<div class="candidate-meta"><strong>Email:</strong> ' + escapeHtml(candidateEmail) + '<br><strong>Phone:</strong> ' + escapeHtml(candidatePhone) + '<br><strong>Role:</strong> ' + escapeHtml(roleText) + '<br><strong>Applied:</strong> ' + escapeHtml(applyDateLabel) + '<br><strong>Conversion:</strong> ' + escapeHtml(conversionLabel) + '</div>';
            html += '</div>';
            html += '<div class="experience-pill">' + escapeHtml(experienceLabel) + '</div>';
            html += '</div>';
            html += '<div class="card-score-row"><div><strong>AI Score</strong> <span class="label label-success card-ai-score">Pending</span></div><button type="button" class="btn btn-xs btn-info card-open-modal">View Details</button></div>';
            html += '<div class="card-system-data">';
            html += '<div class="skills-wrap">' + skillsHtml + '</div>';
            html += '<div class="resume-preview">' + escapeHtml(previewText).replace(/\n/g, '<br>') + '</div>';
            html += '<div style="margin-top:14px;"><a href="view_resume.php?file=' + encodeURIComponent(row.original_resume_name || '') + '" target="_blank" class="btn btn-success btn-sm"><i class="fa fa-eye"></i> Open Resume</a> <a href="candidates.php" class="btn btn-default btn-sm"><i class="fa fa-users"></i> Candidates Page</a></div>';
            html += '</div>';
            html += '<div class="card-ai-insight"><strong>AI Insight</strong><div class="card-ai-why"></div><div class="card-ai-focus"></div></div>';
            html += '</div>';
        });
        $('#candidateGrid').html(html);
    }

    function renderAiSummary(parsed) {
        $('#aiSummaryPanel').addClass('is-visible');
        $('#aiSummaryText').text(parsed.summary || 'AI shortlist generated.');
        $('#aiTopFiveList').html(renderTopFive(parsed.top_5_recommended_candidates || []));
        $('#aiSkillsHeatmap').html(renderSkillsHeatmap(parsed.skills_heatmap || []));
        $('#aiExperienceDistribution').html(renderSummaryValue(parsed.experience_distribution));
        $('#aiNoticePeriodAnalysis').html(renderSummaryValue(parsed.notice_period_analysis));
    }

    function renderTopFive(items) {
        if (!Array.isArray(items) || !items.length) {
            return '<span class="text-muted">Not available</span>';
        }
        var html = '<ol style="padding-left:18px; margin-bottom:0;">';
        items.forEach(function (item) {
            html += '<li>' + escapeHtml(item.name || 'Candidate') + ' - ' + escapeHtml(String(item.match_score || item.candidate_match_score || 0)) + '/100</li>';
        });
        html += '</ol>';
        return html;
    }

    function renderSkillsHeatmap(items) {
        if (!Array.isArray(items) || !items.length) {
            return '<span class="text-muted">Not available</span>';
        }
        var html = '<div class="ai-heatmap-list">';
        items.forEach(function (item) {
            if (typeof item === 'string') {
                html += '<span class="label label-info">' + escapeHtml(item) + '</span>';
            } else {
                html += '<span class="label label-info">' + escapeHtml((item.skill || 'Skill') + ' - ' + (item.strength || '')) + '</span>';
            }
        });
        html += '</div>';
        return html;
    }

    function renderSummaryValue(value) {
        if (Array.isArray(value)) {
            return renderList(value);
        }
        if (value && typeof value === 'object') {
            var lines = [];
            Object.keys(value).forEach(function (key) {
                lines.push('<strong>' + escapeHtml(key) + ':</strong> ' + escapeHtml(value[key]));
            });
            return lines.length ? '<div class="ai-summary-copy">' + lines.join('<br>') + '</div>' : '<span class="text-muted">Not available</span>';
        }
        if (value === null || typeof value === 'undefined' || String(value).trim() === '') {
            return '<span class="text-muted">Not available</span>';
        }
        return '<div class="ai-summary-copy">' + escapeHtml(String(value)) + '</div>';
    }

    function renderList(items) {
        if (!Array.isArray(items) || !items.length) {
            return '<span class="text-muted">Not available</span>';
        }
        var html = '<ul style="padding-left:18px; margin-bottom:0;">';
        items.forEach(function (item) {
            html += '<li>' + escapeHtml(item) + '</li>';
        });
        html += '</ul>';
        return html;
    }
});

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
</script>
<?php include 'inc/template_end.php'; ?>
