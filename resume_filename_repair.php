<?php
include 'inc/config.php';
$template['header_link'] = 'RESUME FILENAME REPAIR';

include 'inc/template_start.php';
include 'inc/page_head.php';
require_once 'includes/resume_intelligence.php';

function resumeRepairEsc($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function resumeRepairSupportedExtensions()
{
    return array('pdf', 'docx', 'doc', 'rtf', 'txt');
}

function resumeRepairBuildFileIndex()
{
    $files = array();
    foreach (resumeStorageDirectories() as $directory) {
        if (!is_dir($directory) || !is_readable($directory)) {
            continue;
        }

        try {
            $iterator = new DirectoryIterator($directory);
            foreach ($iterator as $fileInfo) {
                if (!$fileInfo->isFile()) {
                    continue;
                }

                $fileName = $fileInfo->getFilename();
                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                if (!in_array($extension, resumeRepairSupportedExtensions(), true)) {
                    continue;
                }

                $lowerName = strtolower($fileName);
                if (!isset($files[$lowerName])) {
                    $files[$lowerName] = array(
                        'name' => $fileName,
                        'path' => $fileInfo->getPathname(),
                        'extension' => $extension
                    );
                }
            }
        } catch (Exception $e) {
            continue;
        }
    }

    return array_values($files);
}

function resumeRepairFindMatches($storedResumeName, $fileIndex)
{
    $storedResumeName = basename(str_replace('\\', '/', trim((string) $storedResumeName)));
    if ($storedResumeName === '') {
        return array();
    }

    $needle = strtolower($storedResumeName);
    $needleWithoutExtension = strtolower(pathinfo($storedResumeName, PATHINFO_FILENAME));
    $matches = array();

    foreach ($fileIndex as $file) {
        $candidate = strtolower($file['name']);
        $candidateWithoutExtension = strtolower(pathinfo($file['name'], PATHINFO_FILENAME));

        if (strpos($candidate, $needle) === 0 || ($needleWithoutExtension !== '' && strpos($candidateWithoutExtension, $needleWithoutExtension) === 0)) {
            $matches[] = $file;
        }
    }

    return $matches;
}

function resumeRepairFetchBrokenRows($connect, $limit = 500)
{
    $rows = array();
    if (!($connect instanceof mysqli)) {
        return $rows;
    }

    $limit = max(1, min(2000, (int) $limit));
    $sql = "SELECT id, name, email, phonenumber, resume
            FROM tblleads
            WHERE resume IS NOT NULL
              AND TRIM(resume) <> ''
            ORDER BY id DESC
            LIMIT " . $limit;

    $result = $connect->query($sql);
    if (!$result) {
        return $rows;
    }

    while ($row = $result->fetch_assoc()) {
        $resumeName = isset($row['resume']) ? (string) $row['resume'] : '';
        if (resolveResumeAbsolutePath($resumeName) !== '') {
            continue;
        }
        $rows[] = $row;
    }

    $result->free();
    return $rows;
}

function resumeRepairUpdateLeadResume($connect, $lead, $matchedFile)
{
    $leadId = isset($lead['id']) ? (int) $lead['id'] : 0;
    $oldName = isset($lead['resume']) ? (string) $lead['resume'] : '';
    $newName = isset($matchedFile['name']) ? (string) $matchedFile['name'] : '';
    $newPath = isset($matchedFile['path']) ? (string) $matchedFile['path'] : '';
    $newExtension = strtolower(pathinfo($newName, PATHINFO_EXTENSION));

    if ($leadId <= 0 || $oldName === '' || $newName === '') {
        return false;
    }

    $stmt = $connect->prepare("UPDATE tblleads SET resume = ? WHERE id = ? AND resume = ?");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('sis', $newName, $leadId, $oldName);
    $ok = $stmt->execute();
    $stmt->close();

    if (!$ok) {
        return false;
    }

    $stmt = $connect->prepare("UPDATE resume_documents
        SET original_resume_name = ?,
            file_path = ?,
            file_extension = ?,
            extraction_status = CASE WHEN extraction_status = 'missing' THEN 'pending' ELSE extraction_status END,
            last_error = CASE WHEN extraction_status = 'missing' THEN NULL ELSE last_error END
        WHERE lead_id = ? AND original_resume_name = ?");
    if ($stmt) {
        $stmt->bind_param('sssis', $newName, $newPath, $newExtension, $leadId, $oldName);
        $stmt->execute();
        $stmt->close();
    }

    if (function_exists('processResumeLead')) {
        processResumeLead($connect, array(
            'id' => $leadId,
            'name' => isset($lead['name']) ? (string) $lead['name'] : '',
            'email' => isset($lead['email']) ? (string) $lead['email'] : '',
            'phonenumber' => isset($lead['phonenumber']) ? (string) $lead['phonenumber'] : '',
            'resume' => $newName
        ));
    }

    return true;
}

$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 500;
$limit = max(100, min(2000, $limit));
$fileIndex = resumeRepairBuildFileIndex();
$brokenRows = resumeRepairFetchBrokenRows($connect, $limit);
$repairRows = array();
$appliedCount = 0;
$applyRequested = isset($_POST['action']) && $_POST['action'] === 'repair';

foreach ($brokenRows as $lead) {
    $matches = resumeRepairFindMatches(isset($lead['resume']) ? $lead['resume'] : '', $fileIndex);
    $status = 'No match';
    if (count($matches) === 1) {
        $status = 'Ready';
        if ($applyRequested && resumeRepairUpdateLeadResume($connect, $lead, $matches[0])) {
            $status = 'Repaired';
            $appliedCount++;
        }
    } elseif (count($matches) > 1) {
        $status = 'Ambiguous';
    }

    $repairRows[] = array(
        'lead' => $lead,
        'matches' => $matches,
        'status' => $status
    );
}

$readyCount = 0;
$ambiguousCount = 0;
$missingCount = 0;
foreach ($repairRows as $row) {
    if ($row['status'] === 'Ready' || $row['status'] === 'Repaired') {
        $readyCount++;
    } elseif ($row['status'] === 'Ambiguous') {
        $ambiguousCount++;
    } else {
        $missingCount++;
    }
}
?>
<style>
    .repair-hero {
        background: linear-gradient(135deg, #1f5f3c 0%, #0e2f28 100%);
        border-radius: 18px;
        color: #fff;
        padding: 28px;
        margin-bottom: 22px;
    }
    .repair-hero h2 {
        margin: 0 0 8px;
        font-weight: 700;
    }
    .repair-panel {
        background: #fff;
        border: 1px solid #e1eadf;
        border-radius: 14px;
        padding: 22px;
        margin-bottom: 20px;
    }
    .repair-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }
    .repair-stat {
        background: #f7fbf7;
        border: 1px solid #ddeadd;
        border-radius: 14px;
        padding: 16px;
    }
    .repair-stat strong {
        display: block;
        font-size: 28px;
        color: #0e4630;
    }
    .repair-file-cell {
        word-break: break-all;
        max-width: 460px;
    }
    @media (max-width: 991px) {
        .repair-stats {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>

<div id="page-content">
    <div class="content-header">
        <div class="header-section">
            <h1>
                Resume Filename Repair<br>
                <small>Find and repair database resume names that were truncated before the column size was increased.</small>
            </h1>
        </div>
    </div>

    <ul class="breadcrumb breadcrumb-top">
        <li>HR CRM</li>
        <li><a href="resume_library.php">Resume Library</a></li>
        <li><a href="resume_filename_repair.php">Filename Repair</a></li>
    </ul>

    <div class="repair-hero">
        <h2>Safe Repair for Old Truncated Resume Names</h2>
        <p>This scans candidates whose saved resume file is missing, then matches the saved truncated value against real files in the resume folder. Only one-exact-prefix matches are repairable automatically.</p>
    </div>

    <?php if ($applyRequested) { ?>
        <div class="alert alert-success">Repair applied to <?php echo (int) $appliedCount; ?> unambiguous resume filename<?php echo $appliedCount === 1 ? '' : 's'; ?>.</div>
    <?php } ?>

    <div class="repair-stats">
        <div class="repair-stat"><span>Files indexed</span><strong><?php echo count($fileIndex); ?></strong></div>
        <div class="repair-stat"><span>Broken DB rows checked</span><strong><?php echo count($brokenRows); ?></strong></div>
        <div class="repair-stat"><span>Auto repairable</span><strong><?php echo (int) $readyCount; ?></strong></div>
        <div class="repair-stat"><span>Needs manual review</span><strong><?php echo (int) ($ambiguousCount + $missingCount); ?></strong></div>
    </div>

    <div class="repair-panel">
        <form method="get" action="resume_filename_repair.php" class="form-inline" style="margin-bottom:16px;">
            <label for="limit">Rows to scan</label>
            <select id="limit" name="limit" class="form-control">
                <option value="500"<?php echo $limit === 500 ? ' selected' : ''; ?>>500</option>
                <option value="1000"<?php echo $limit === 1000 ? ' selected' : ''; ?>>1000</option>
                <option value="2000"<?php echo $limit === 2000 ? ' selected' : ''; ?>>2000</option>
            </select>
            <button type="submit" class="btn btn-default">Refresh</button>
        </form>

        <form method="post" action="resume_filename_repair.php?limit=<?php echo (int) $limit; ?>" onsubmit="return confirm('Repair all unambiguous filename matches shown on this page?');">
            <input type="hidden" name="action" value="repair">
            <button type="submit" class="btn btn-success"<?php echo $readyCount < 1 ? ' disabled' : ''; ?>>
                <i class="fa fa-wrench"></i> Repair <?php echo (int) $readyCount; ?> Safe Match<?php echo $readyCount === 1 ? '' : 'es'; ?>
            </button>
            <a href="resume_files.php" class="btn btn-default"><i class="fa fa-folder-open"></i> Resume Files</a>
        </form>
    </div>

    <div class="repair-panel">
        <?php if (empty($repairRows)) { ?>
            <div class="alert alert-info">No broken resume filename rows were found in this scan window.</div>
        <?php } else { ?>
            <div class="table-responsive">
                <table class="table table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th style="width:90px;">Lead ID</th>
                            <th>Candidate</th>
                            <th>Saved DB Resume</th>
                            <th>Matched File</th>
                            <th style="width:120px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($repairRows as $row) {
                            $lead = $row['lead'];
                            $matches = $row['matches'];
                            $status = $row['status'];
                            $labelClass = $status === 'Ready' || $status === 'Repaired' ? 'success' : ($status === 'Ambiguous' ? 'warning' : 'default');
                        ?>
                            <tr>
                                <td>#<?php echo (int) $lead['id']; ?></td>
                                <td>
                                    <strong><?php echo resumeRepairEsc(isset($lead['name']) ? $lead['name'] : ''); ?></strong><br>
                                    <span class="text-muted"><?php echo resumeRepairEsc(isset($lead['email']) ? $lead['email'] : ''); ?></span>
                                </td>
                                <td class="repair-file-cell"><?php echo resumeRepairEsc(isset($lead['resume']) ? $lead['resume'] : ''); ?></td>
                                <td class="repair-file-cell">
                                    <?php if (count($matches) === 1) { ?>
                                        <?php echo resumeRepairEsc($matches[0]['name']); ?>
                                    <?php } elseif (count($matches) > 1) { ?>
                                        <?php echo count($matches); ?> possible matches
                                    <?php } else { ?>
                                        <span class="text-muted">No file starts with this saved value.</span>
                                    <?php } ?>
                                </td>
                                <td><span class="label label-<?php echo $labelClass; ?>"><?php echo resumeRepairEsc($status); ?></span></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </div>
</div>

<?php include 'inc/template_scripts.php'; ?>
<?php include 'inc/template_end.php'; ?>
