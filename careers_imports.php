<?php
include 'inc/config.php';
$template['header_link'] = 'CAREERS IMPORTS';
require_once 'includes/resume_intelligence.php';

ensureResumeIntelligenceTables($connect);

function careersImportsEsc($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function careersImportsImportedRows($connect, $search = '', $limit = 100)
{
    $rows = array();
    if (!($connect instanceof mysqli)) {
        return $rows;
    }

    $where = "COALESCE(l.ainfo, '') LIKE '%Imported from careers email.%'";
    $search = trim((string) $search);
    if ($search !== '') {
        $escaped = $connect->real_escape_string($search);
        $like = "'%" . $escaped . "%'";
        $where .= " AND (l.name LIKE $like OR l.email LIKE $like OR l.phonenumber LIKE $like OR l.resume LIKE $like OR l.ainfo LIKE $like)";
    }

    $limit = max(10, min(300, (int) $limit));
    $sql = "SELECT
                l.id,
                l.name,
                l.email,
                l.phonenumber,
                l.resume,
                l.ainfo,
                l.dateadded,
                ls.name AS status_name,
                src.name AS source_name,
                rd.extraction_status,
                rd.extracted_skills,
                rd.last_error,
                rd.updated_at AS indexed_at
            FROM tblleads l
            LEFT JOIN tblleadsstatus ls ON ls.id = l.status
            LEFT JOIN tblleadssources src ON src.id = l.source
            LEFT JOIN resume_documents rd ON rd.lead_id = l.id AND rd.original_resume_name = l.resume
            WHERE $where
            ORDER BY l.dateadded DESC, l.id DESC
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

function careersImportsCount($connect)
{
    if (!($connect instanceof mysqli)) {
        return 0;
    }

    $result = $connect->query("SELECT COUNT(*) AS total FROM tblleads WHERE COALESCE(ainfo, '') LIKE '%Imported from careers email.%'");
    if (!$result) {
        return 0;
    }

    $row = $result->fetch_assoc();
    $result->free();

    return isset($row['total']) ? (int) $row['total'] : 0;
}

$search = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$rows = careersImportsImportedRows($connect, $search);
$totalImports = careersImportsCount($connect);
$completed = 0;
$needsReview = 0;
foreach ($rows as $row) {
    if (isset($row['extraction_status']) && $row['extraction_status'] === 'completed') {
        $completed++;
    } else {
        $needsReview++;
    }
}
?>
<?php include 'inc/template_start.php'; ?>
<?php include 'inc/page_head.php'; ?>

<style type="text/css">
.careers-shell {
    background: #f4f7f3;
    border-radius: 22px;
    padding: 22px;
}
.careers-hero {
    background: linear-gradient(135deg, #0f6b43 0%, #123f2d 100%);
    border-radius: 22px;
    color: #fff;
    padding: 24px;
    margin-bottom: 18px;
    box-shadow: 0 18px 45px rgba(15, 107, 67, .18);
}
.careers-hero h1 {
    margin: 0 0 8px;
    font-size: 30px;
    font-weight: 700;
}
.careers-hero p {
    max-width: 760px;
    margin: 0;
    color: rgba(255,255,255,.78);
}
.careers-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    margin-bottom: 18px;
}
.careers-stat {
    flex: 1 1 180px;
    background: #fff;
    border: 1px solid #e1eadf;
    border-radius: 18px;
    padding: 18px;
}
.careers-stat strong {
    display: block;
    font-size: 30px;
    color: #123f2d;
}
.careers-panel {
    background: #fff;
    border: 1px solid #e1eadf;
    border-radius: 20px;
    padding: 18px;
}
.careers-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}
.careers-search {
    display: flex;
    gap: 8px;
    flex: 1 1 420px;
}
.careers-search input {
    border-radius: 12px;
    min-height: 42px;
}
.careers-badge {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 5px 10px;
    font-size: 12px;
    font-weight: 700;
    background: #edf8e9;
    color: #0f6b43;
}
.careers-badge.warn {
    background: #fff4dc;
    color: #9a6200;
}
.careers-badge.db {
    background: #e8f5ff;
    color: #1769aa;
}
.careers-explainer {
    background: #fff;
    border: 1px solid #dfead9;
    border-left: 5px solid #0f6b43;
    border-radius: 16px;
    padding: 14px 16px;
    margin-bottom: 18px;
    color: #2d3b32;
}
.careers-explainer strong {
    color: #123f2d;
}
.careers-table td {
    vertical-align: middle !important;
}
.careers-note {
    max-width: 360px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #6b7280;
}
@media (max-width: 767px) {
    .careers-shell {
        padding: 14px;
    }
    .careers-search {
        flex-basis: 100%;
    }
}
</style>

<div id="page-content">
    <div class="content-header">
        <div class="header-section">
            <h1>Careers Resume Imports<br><small>Review resumes imported automatically from careers@digichefs.com.</small></h1>
        </div>
    </div>

    <div class="careers-shell">
        <div class="careers-hero">
            <h1>Resume Intake Queue</h1>
            <p>Emails with resume attachments are stored as CRM candidates, then indexed into the resume library when possible. This page keeps those auto-created records easy to audit.</p>
        </div>

        <div class="careers-explainer">
            <strong>How to read this page:</strong>
            <span>if a row appears here, it is already saved in the CRM database inside <code>tblleads</code>. The import status only tells whether the resume text was also indexed inside <code>resume_documents</code> for AI/search.</span>
        </div>

        <div class="careers-stats">
            <div class="careers-stat">
                <span>Total imported</span>
                <strong><?php echo (int) $totalImports; ?></strong>
            </div>
            <div class="careers-stat">
                <span>Indexed in current view</span>
                <strong><?php echo (int) $completed; ?></strong>
            </div>
            <div class="careers-stat">
                <span>Needs review in current view</span>
                <strong><?php echo (int) $needsReview; ?></strong>
            </div>
        </div>

        <div class="careers-panel">
            <div class="careers-toolbar">
                <h3 style="margin:0;">Imported Candidates</h3>
                <form method="get" class="careers-search">
                    <input type="text" name="q" class="form-control" value="<?php echo careersImportsEsc($search); ?>" placeholder="Search name, email, phone, resume file">
                    <button type="submit" class="btn btn-success">Search</button>
                    <a href="careers_imports.php" class="btn btn-default">Reset</a>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-vcenter careers-table">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Contact</th>
                            <th>Resume</th>
                            <th>CRM DB</th>
                            <th>Import Status</th>
                            <th>Skills</th>
                            <th>Imported</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)) { ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No careers email imports found yet.</td>
                            </tr>
                        <?php } ?>
                        <?php foreach ($rows as $row) {
                            $status = isset($row['extraction_status']) && trim((string) $row['extraction_status']) !== '' ? (string) $row['extraction_status'] : 'not indexed';
                            $isCompleted = $status === 'completed';
                            $resume = isset($row['resume']) ? (string) $row['resume'] : '';
                        ?>
                            <tr>
                                <td>
                                    <strong><?php echo careersImportsEsc($row['name']); ?></strong><br>
                                    <small class="text-muted">Lead #<?php echo (int) $row['id']; ?><?php echo !empty($row['status_name']) ? ' · ' . careersImportsEsc($row['status_name']) : ''; ?></small>
                                </td>
                                <td>
                                    <?php echo careersImportsEsc($row['email']); ?><br>
                                    <small class="text-muted"><?php echo careersImportsEsc($row['phonenumber']); ?></small>
                                </td>
                                <td>
                                    <?php echo careersImportsEsc($resume); ?><br>
                                    <small class="text-muted"><?php echo !empty($row['source_name']) ? careersImportsEsc($row['source_name']) : 'Careers email'; ?></small>
                                </td>
                                <td><span class="careers-badge db">Stored</span></td>
                                <td>
                                    <span class="careers-badge<?php echo $isCompleted ? '' : ' warn'; ?>"><?php echo careersImportsEsc(ucwords(str_replace('_', ' ', $status))); ?></span>
                                    <?php if (!$isCompleted && !empty($row['last_error'])) { ?>
                                        <div class="careers-note" title="<?php echo careersImportsEsc($row['last_error']); ?>"><?php echo careersImportsEsc($row['last_error']); ?></div>
                                    <?php } ?>
                                </td>
                                <td><?php echo !empty($row['extracted_skills']) ? careersImportsEsc($row['extracted_skills']) : '<span class="text-muted">Not detected</span>'; ?></td>
                                <td><?php echo careersImportsEsc($row['dateadded']); ?></td>
                                <td>
                                    <a class="btn btn-xs btn-primary" href="candidates.php?leadid=<?php echo (int) $row['id']; ?>">Open Candidate</a>
                                    <?php if ($resume !== '') { ?>
                                        <a class="btn btn-xs btn-default" href="view_resume.php?file=<?php echo urlencode($resume); ?>" target="_blank">View Resume</a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'inc/template_scripts.php'; ?>
<?php include 'inc/template_end.php'; ?>
