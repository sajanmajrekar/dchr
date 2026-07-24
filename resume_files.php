<?php
include 'inc/config.php';
$template['header_link'] = 'RESUME FILES';

function resumeFilesEsc($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function resumeFilesFormatBytes($bytes)
{
    $bytes = (float) $bytes;
    if ($bytes >= 1048576) {
        return rtrim(rtrim(number_format($bytes / 1048576, 2, '.', ''), '0'), '.') . ' MB';
    }
    if ($bytes >= 1024) {
        return rtrim(rtrim(number_format($bytes / 1024, 1, '.', ''), '0'), '.') . ' KB';
    }
    return (int) $bytes . ' B';
}

function resumeFilesUrl($overrides = array())
{
    $params = array(
        'q' => isset($_GET['q']) ? trim((string) $_GET['q']) : '',
        'type' => isset($_GET['type']) ? trim((string) $_GET['type']) : 'pdf',
        'per_page' => isset($_GET['per_page']) ? (int) $_GET['per_page'] : 50,
        'page' => isset($_GET['page']) ? (int) $_GET['page'] : 1
    );

    foreach ($overrides as $key => $value) {
        $params[$key] = $value;
    }

    $params = array_filter($params, function ($value) {
        return $value !== '' && $value !== null;
    });

    return 'resume_files.php?' . http_build_query($params);
}

$resumeDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'resume';
$query = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$type = isset($_GET['type']) ? strtolower(trim((string) $_GET['type'])) : 'pdf';
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage = isset($_GET['per_page']) ? (int) $_GET['per_page'] : 50;
$perPage = max(25, min(100, $perPage));

$allowedTypes = array(
    'pdf' => array('pdf'),
    'docx' => array('docx'),
    'doc' => array('doc'),
    'all' => array('pdf', 'docx', 'doc', 'rtf', 'txt')
);
if (!isset($allowedTypes[$type])) {
    $type = 'pdf';
}

$matchingFiles = array();
$totalFilesInFolder = 0;

if (is_dir($resumeDir) && is_readable($resumeDir)) {
    try {
        $iterator = new DirectoryIterator($resumeDir);
        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }

            $fileName = $fileInfo->getFilename();
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (!in_array($extension, $allowedTypes['all'], true)) {
                continue;
            }

            $totalFilesInFolder++;
            if (!in_array($extension, $allowedTypes[$type], true)) {
                continue;
            }
            if ($query !== '' && stripos($fileName, $query) === false) {
                continue;
            }

            $matchingFiles[] = $fileName;
        }
    } catch (Exception $e) {
        $matchingFiles = array();
    }
}

natcasesort($matchingFiles);
$matchingFiles = array_values($matchingFiles);
$totalMatches = count($matchingFiles);
$pages = max(1, (int) ceil($totalMatches / $perPage));
if ($page > $pages) {
    $page = $pages;
}
$offset = ($page - 1) * $perPage;
$pageFiles = array_slice($matchingFiles, $offset, $perPage);

include 'inc/template_start.php';
include 'inc/page_head.php';
?>
<style>
    .resume-files-hero {
        background: linear-gradient(135deg, #0e5a3a 0%, #0b3227 100%);
        border-radius: 24px;
        color: #fff;
        padding: 30px 34px;
        margin-bottom: 24px;
        box-shadow: 0 16px 40px rgba(12, 64, 45, .18);
    }
    .resume-files-hero h2 {
        margin: 0 0 8px;
        font-size: 34px;
        font-weight: 700;
    }
    .resume-files-hero p {
        margin: 0;
        color: rgba(255,255,255,.82);
        font-size: 16px;
    }
    .resume-files-panel {
        background: #fff;
        border: 1px solid #dde8df;
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 10px 28px rgba(18, 43, 31, .07);
    }
    .resume-files-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }
    .resume-files-stat {
        background: #f7fbf8;
        border: 1px solid #dfece3;
        border-radius: 16px;
        padding: 18px;
    }
    .resume-files-stat strong {
        display: block;
        color: #0d3f2b;
        font-size: 30px;
        line-height: 1.1;
    }
    .resume-files-table td {
        vertical-align: middle !important;
    }
    .resume-file-name {
        font-weight: 700;
        color: #21354d;
        word-break: break-all;
    }
    .resume-file-ext {
        display: inline-block;
        border-radius: 999px;
        padding: 4px 9px;
        background: #eaf7ee;
        color: #0a6a42;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .resume-files-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 18px;
    }
    @media (max-width: 767px) {
        .resume-files-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<div id="page-content">
    <div class="content-header">
        <div class="header-section">
            <h1>
                Resume Files<br>
                <small>Browse resume uploads safely with search and pagination.</small>
            </h1>
        </div>
    </div>

    <ul class="breadcrumb breadcrumb-top">
        <li>HR CRM</li>
        <li><a href="resume_library.php">Resume Library</a></li>
        <li><a href="resume_files.php">Resume Files</a></li>
    </ul>

    <div class="resume-files-hero">
        <h2>Resume Folder Browser</h2>
        <p>This page loads only one page of files at a time, so the server does not try to render 10k+ resumes together.</p>
    </div>

    <div class="resume-files-stats">
        <div class="resume-files-stat">
            <span>Total supported files</span>
            <strong><?php echo (int) $totalFilesInFolder; ?></strong>
        </div>
        <div class="resume-files-stat">
            <span>Matching this search</span>
            <strong><?php echo (int) $totalMatches; ?></strong>
        </div>
        <div class="resume-files-stat">
            <span>Showing page</span>
            <strong><?php echo (int) $page; ?> / <?php echo (int) $pages; ?></strong>
        </div>
    </div>

    <div class="resume-files-panel">
        <form method="get" action="resume_files.php">
            <div class="row">
                <div class="col-md-6">
                    <label for="q">Search file name</label>
                    <input type="text" id="q" name="q" class="form-control input-lg" value="<?php echo resumeFilesEsc($query); ?>" placeholder="candidate name, file id, .pdf">
                </div>
                <div class="col-md-2">
                    <label for="type">File type</label>
                    <select id="type" name="type" class="form-control input-lg">
                        <option value="pdf"<?php echo $type === 'pdf' ? ' selected' : ''; ?>>PDF only</option>
                        <option value="docx"<?php echo $type === 'docx' ? ' selected' : ''; ?>>DOCX only</option>
                        <option value="doc"<?php echo $type === 'doc' ? ' selected' : ''; ?>>DOC only</option>
                        <option value="all"<?php echo $type === 'all' ? ' selected' : ''; ?>>All supported</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="per_page">Per page</label>
                    <select id="per_page" name="per_page" class="form-control input-lg">
                        <option value="25"<?php echo $perPage === 25 ? ' selected' : ''; ?>>25</option>
                        <option value="50"<?php echo $perPage === 50 ? ' selected' : ''; ?>>50</option>
                        <option value="100"<?php echo $perPage === 100 ? ' selected' : ''; ?>>100</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-search"></i> Search</button>
                        <a href="resume_files.php" class="btn btn-default btn-lg">Reset</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="resume-files-panel">
        <?php if (!is_dir($resumeDir) || !is_readable($resumeDir)) { ?>
            <div class="alert alert-danger">Resume folder is not readable: <?php echo resumeFilesEsc($resumeDir); ?></div>
        <?php } elseif (empty($pageFiles)) { ?>
            <div class="alert alert-info">No resume files matched this search.</div>
        <?php } else { ?>
            <div class="table-responsive">
                <table class="table table-striped table-vcenter resume-files-table">
                    <thead>
                        <tr>
                            <th>File</th>
                            <th style="width:100px;">Type</th>
                            <th style="width:130px;">Size</th>
                            <th style="width:180px;">Modified</th>
                            <th style="width:140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pageFiles as $fileName) {
                            $filePath = $resumeDir . DIRECTORY_SEPARATOR . $fileName;
                            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            $fileSize = is_file($filePath) ? @filesize($filePath) : false;
                            $modified = is_file($filePath) ? @filemtime($filePath) : false;
                        ?>
                            <tr>
                                <td>
                                    <div class="resume-file-name"><?php echo resumeFilesEsc($fileName); ?></div>
                                </td>
                                <td><span class="resume-file-ext"><?php echo resumeFilesEsc($extension); ?></span></td>
                                <td><?php echo $fileSize !== false ? resumeFilesFormatBytes($fileSize) : '-'; ?></td>
                                <td><?php echo $modified !== false ? date('d M Y H:i', $modified) : '-'; ?></td>
                                <td>
                                    <a href="view_resume.php?file=<?php echo rawurlencode($fileName); ?>" target="_blank" class="btn btn-success btn-sm">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="resume-files-pagination">
                <div class="text-muted">
                    Showing <?php echo (int) ($offset + 1); ?>-<?php echo (int) min($offset + count($pageFiles), $totalMatches); ?> of <?php echo (int) $totalMatches; ?> files
                </div>
                <div>
                    <a class="btn btn-default<?php echo $page <= 1 ? ' disabled' : ''; ?>" href="<?php echo $page <= 1 ? 'javascript:void(0)' : resumeFilesUrl(array('page' => $page - 1)); ?>">Prev</a>
                    <span class="btn btn-default disabled">Page <?php echo (int) $page; ?> of <?php echo (int) $pages; ?></span>
                    <a class="btn btn-default<?php echo $page >= $pages ? ' disabled' : ''; ?>" href="<?php echo $page >= $pages ? 'javascript:void(0)' : resumeFilesUrl(array('page' => $page + 1)); ?>">Next</a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php include 'inc/template_end.php'; ?>
