<?php include 'inc/config.php'; ?>
<?php
$template['header_link'] = 'RESUME INTELLIGENCE';
require_once 'includes/resume_intelligence.php';

ensureResumeIntelligenceTables($connect);

function resumeMonitorJson($payload)
{
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit();
}

$requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
$action = isset($_POST['action']) ? trim((string) $_POST['action']) : '';

if ($requestMethod === 'POST' && $action !== '') {
    if ($action === 'save_parser_config') {
        $config = array(
            'java_path' => isset($_POST['java_path']) ? trim((string) $_POST['java_path']) : '',
            'tika_jar_path' => isset($_POST['tika_jar_path']) ? trim((string) $_POST['tika_jar_path']) : ''
        );

        $ok = saveResumeIntelligenceConfig($config);
        resumeMonitorJson(array(
            'ok' => $ok,
            'message' => $ok ? 'Parser config saved.' : 'Could not save parser config.',
            'parser' => getResumeParserStatus()
        ));
    }

    if ($action === 'poll_worker') {
        resumeMonitorJson(array(
            'ok' => true,
            'worker' => fetchResumeWorkerState($connect),
            'stats' => fetchResumeDocumentStats($connect),
            'queue' => fetchResumeQueueStats($connect)
        ));
    }
}

$workerState = fetchResumeWorkerState($connect);
$stats = fetchResumeDocumentStats($connect);
$queueStats = fetchResumeQueueStats($connect);
$parserStatus = getResumeParserStatus();

$defaultWorkerLimit = !empty($workerState['worker_limit']) ? (int) $workerState['worker_limit'] : 200;
if ($defaultWorkerLimit <= 0) {
    $defaultWorkerLimit = 200;
}
if ($defaultWorkerLimit > 500) {
    $defaultWorkerLimit = 500;
}

$manualWorkerCommand = 'C:\\xampp\\php\\php.exe C:\\xampp\\htdocs\\hr\\resume_worker.php --limit=' . $defaultWorkerLimit . ' --token=manualrun_' . date('YmdHis');
?>
<?php include 'inc/template_start.php'; ?>
<?php include 'inc/page_head.php'; ?>

<style type="text/css">
    .resume-card {
        background: #ffffff;
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }
    .resume-stat {
        text-align: center;
        padding: 18px 10px;
        border-radius: 6px;
        background: #f8f9fb;
        border: 1px solid #eceff3;
        margin-bottom: 15px;
    }
    .resume-stat h3 {
        margin: 0 0 6px 0;
        font-size: 28px;
    }
    .resume-stat p {
        margin: 0;
        color: #666;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .monitor-progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
        color: #555;
    }
    .monitor-meta {
        margin-top: 10px;
        font-size: 12px;
        color: #777;
    }
    .monitor-note {
        padding: 14px 16px;
        border-radius: 6px;
        background: #eef9fd;
        border-left: 4px solid #46b8da;
        margin-top: 15px;
    }
    .live-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
    }
    .live-box {
        border: 1px solid #eceff3;
        border-radius: 6px;
        background: #fbfcfe;
        padding: 14px 12px;
        text-align: center;
    }
    .live-box-value {
        font-size: 26px;
        line-height: 1.1;
        color: #2f3b4c;
        margin-bottom: 6px;
    }
    .live-box-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #777;
    }
    .progress-section {
        margin-top: 18px;
    }
    .progress-section-title {
        font-size: 13px;
        font-weight: bold;
        color: #555;
        margin-bottom: 8px;
    }
    .parser-ready {
        color: #2f855a;
        font-weight: bold;
    }
    .parser-not-ready {
        color: #c53030;
        font-weight: bold;
    }
    .command-box {
        width: 100%;
        min-height: 84px;
        font-family: Consolas, monospace;
        font-size: 13px;
        resize: vertical;
    }
    .manual-help {
        margin-top: 12px;
        color: #666;
        font-size: 13px;
    }
</style>

<div id="page-content">
    <div class="content-header">
        <div class="header-section">
            <h1>
                Resume Intelligence<br>
                <small>Manual worker monitor for this server. Run the CLI worker from terminal and watch live status here.</small>
            </h1>
        </div>
    </div>

    <ul class="breadcrumb breadcrumb-top">
        <li>HR CRM</li>
        <li><a href="">Resume Intelligence</a></li>
    </ul>

    <div class="row">
        <div class="col-md-8">
            <div class="resume-card">
                <h3 style="margin-top:0;">Manual Worker Command</h3>
                <p>This server does not reliably launch background PHP workers from the browser, so the safe workflow is: run the worker from terminal, and use this page as a live monitor.</p>

                <div class="form-group">
                    <label for="worker_limit">Worker chunk size</label>
                    <input type="number" min="10" max="500" class="form-control" id="worker_limit" value="<?php echo (int) $defaultWorkerLimit; ?>" style="max-width:180px;">
                    <p style="margin-top:8px; color:#777; font-size:12px;">Allowed range: 10 to 500 resumes per batch.</p>
                </div>

                <div class="form-group">
                    <label for="manualWorkerCommand">Run this command in Command Prompt / PowerShell</label>
                    <textarea id="manualWorkerCommand" class="form-control command-box" readonly><?php echo htmlspecialchars($manualWorkerCommand); ?></textarea>
                </div>

                <button type="button" id="prepareNextBatchBtn" class="btn btn-success">Prepare Next Batch</button>
                <button type="button" id="copyCommandBtn" class="btn btn-primary">Copy Command</button>
                <button type="button" id="refreshStatusBtn" class="btn btn-default">Refresh Status</button>
                <label style="margin-left:15px; font-weight:normal;">
                    <input type="checkbox" id="autoRefreshToggle" checked> Auto refresh every 3s
                </label>
                <span id="copyCommandMessage" style="margin-left:12px; color:#666;"></span>

                <div class="manual-help">
                    Example flow:
                    1. Click <strong>Prepare Next Batch</strong>
                    2. Copy the command
                    3. Run it in terminal
                    4. When that batch finishes, click <strong>Prepare Next Batch</strong> again
                </div>

                <div style="margin-top:20px;">
                    <div class="monitor-progress-label">
                        <span id="workerStatusText"><?php echo !empty($workerState['is_running']) ? 'Worker is running.' : 'Worker is idle.'; ?></span>
                        <span id="workerProgressPercent">0%</span>
                    </div>
                    <div class="progress progress-striped active" style="margin-bottom:0;">
                        <div id="workerProgressBar" class="progress-bar progress-bar-success" role="progressbar" style="width: 0%;">
                            0%
                        </div>
                    </div>
                    <div class="monitor-meta">
                        <strong>Worker token:</strong> <span id="workerToken"><?php echo htmlspecialchars((string) $workerState['worker_token']); ?></span>
                        &nbsp;|&nbsp;
                        <strong>Current file:</strong> <span id="workerCurrentFile"><?php echo htmlspecialchars((string) $workerState['current_file']); ?></span>
                        &nbsp;|&nbsp;
                        <strong>Last heartbeat:</strong> <span id="workerHeartbeat"><?php echo htmlspecialchars((string) $workerState['heartbeat_at']); ?></span>
                    </div>
                </div>

                <div class="live-grid">
                    <div class="live-box">
                        <div class="live-box-value" id="liveProcessed"><?php echo (int) $workerState['processed_total']; ?></div>
                        <div class="live-box-label">Processed In Run</div>
                    </div>
                    <div class="live-box">
                        <div class="live-box-value" id="liveCompleted"><?php echo (int) $workerState['completed_total']; ?></div>
                        <div class="live-box-label">Completed In Run</div>
                    </div>
                    <div class="live-box">
                        <div class="live-box-value" id="livePending"><?php echo (int) $workerState['pending_total']; ?></div>
                        <div class="live-box-label">Pending In Run</div>
                    </div>
                    <div class="live-box">
                        <div class="live-box-value" id="liveRemaining"><?php echo max(0, (int) $workerState['worker_limit'] - (int) $workerState['processed_total']); ?></div>
                        <div class="live-box-label">Remaining In Run</div>
                    </div>
                </div>

                <div class="progress-section">
                    <div class="progress-section-title">Current Worker Run Progress</div>
                    <div class="monitor-progress-label">
                        <span id="runProgressText"><?php echo (int) $workerState['processed_total']; ?> / <?php echo (int) $workerState['worker_limit']; ?> processed</span>
                        <span id="runProgressPercent"><?php echo ((int) $workerState['worker_limit'] > 0) ? min(100, round(((int) $workerState['processed_total'] / (int) $workerState['worker_limit']) * 100)) : 0; ?>%</span>
                    </div>
                    <div class="progress progress-striped active" style="margin-bottom:0;">
                        <div id="runProgressBar" class="progress-bar progress-bar-info" role="progressbar" style="width: 0%;">
                            0%
                        </div>
                    </div>
                </div>

                <div class="progress-section">
                    <div class="progress-section-title">Overall Queue Progress</div>
                    <div class="monitor-progress-label">
                        <span id="queueProgressText"><?php echo (int) $queueStats['indexed']; ?> / <?php echo (int) $queueStats['with_resume']; ?> indexed</span>
                        <span id="queueProgressPercent">0%</span>
                    </div>
                    <div class="progress progress-striped active" style="margin-bottom:0;">
                        <div id="queueProgressBar" class="progress-bar progress-bar-warning" role="progressbar" style="width: 0%;">
                            0%
                        </div>
                    </div>
                </div>

                <div class="monitor-note">
                    <strong>Worker message:</strong>
                    <span id="workerMessage"><?php echo htmlspecialchars((string) $workerState['last_message']); ?></span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="resume-card">
                <h4 style="margin-top:0;">Queue Snapshot</h4>
                <p style="margin-bottom:6px;"><strong>Total leads with resumes:</strong> <span id="queueWithResume"><?php echo (int) $queueStats['with_resume']; ?></span></p>
                <p style="margin-bottom:6px;"><strong>Already indexed:</strong> <span id="queueIndexed"><?php echo (int) $queueStats['indexed']; ?></span></p>
                <p style="margin-bottom:0;"><strong>Remaining queue:</strong> <span id="queueRemaining"><?php echo (int) $queueStats['remaining']; ?></span></p>
            </div>

            <div class="resume-card">
                <h4 style="margin-top:0;">Apache Tika Setup</h4>
                <p>
                    Tika status:
                    <span id="tikaStatusLabel" class="<?php echo !empty($parserStatus['tika_available']) ? 'parser-ready' : 'parser-not-ready'; ?>">
                        <?php echo !empty($parserStatus['tika_available']) ? 'Ready' : 'Not Ready'; ?>
                    </span>
                </p>
                <form id="parserConfigForm">
                    <div class="form-group">
                        <label for="java_path">Java executable path</label>
                        <input type="text" class="form-control" id="java_path" name="java_path" value="<?php echo htmlspecialchars((string) $parserStatus['config']['java_path']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="tika_jar_path">Apache Tika jar path</label>
                        <input type="text" class="form-control" id="tika_jar_path" name="tika_jar_path" value="<?php echo htmlspecialchars((string) $parserStatus['config']['tika_jar_path']); ?>">
                    </div>
                    <button type="submit" class="btn btn-default">Save Parser Config</button>
                    <span id="parserConfigMessage" style="margin-left:12px; color:#666;"></span>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 col-lg-2">
            <div class="resume-stat">
                <h3 id="statCompleted"><?php echo (int) $stats['completed']; ?></h3>
                <p>Completed</p>
            </div>
        </div>
        <div class="col-sm-4 col-lg-2">
            <div class="resume-stat">
                <h3 id="statPending"><?php echo (int) $stats['pending']; ?></h3>
                <p>Pending PDF</p>
            </div>
        </div>
        <div class="col-sm-4 col-lg-2">
            <div class="resume-stat">
                <h3 id="statMissing"><?php echo (int) $stats['missing']; ?></h3>
                <p>Missing</p>
            </div>
        </div>
        <div class="col-sm-4 col-lg-2">
            <div class="resume-stat">
                <h3 id="statUnsupported"><?php echo (int) $stats['unsupported']; ?></h3>
                <p>Unsupported</p>
            </div>
        </div>
        <div class="col-sm-4 col-lg-2">
            <div class="resume-stat">
                <h3 id="statError"><?php echo (int) $stats['error']; ?></h3>
                <p>Error</p>
            </div>
        </div>
        <div class="col-sm-4 col-lg-2">
            <div class="resume-stat">
                <h3 id="workerProcessed"><?php echo (int) $workerState['processed_total']; ?></h3>
                <p>Processed In Run</p>
            </div>
        </div>
    </div>

    <div class="block full">
        <div class="block-title">
            <h2><strong>Current Worker Run</strong></h2>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-vcenter">
                <thead>
                    <tr>
                        <th>Worker Limit</th>
                        <th>Processed</th>
                        <th>Completed</th>
                        <th>Pending</th>
                        <th>Missing</th>
                        <th>Unsupported</th>
                        <th>Error</th>
                        <th>Started</th>
                        <th>Finished</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td id="runLimit"><?php echo (int) $workerState['worker_limit']; ?></td>
                        <td id="runProcessed"><?php echo (int) $workerState['processed_total']; ?></td>
                        <td id="runCompleted"><?php echo (int) $workerState['completed_total']; ?></td>
                        <td id="runPending"><?php echo (int) $workerState['pending_total']; ?></td>
                        <td id="runMissing"><?php echo (int) $workerState['missing_total']; ?></td>
                        <td id="runUnsupported"><?php echo (int) $workerState['unsupported_total']; ?></td>
                        <td id="runError"><?php echo (int) $workerState['error_total']; ?></td>
                        <td id="runStarted"><?php echo htmlspecialchars((string) $workerState['started_at']); ?></td>
                        <td id="runFinished"><?php echo htmlspecialchars((string) $workerState['finished_at']); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'inc/template_scripts.php'; ?>
<script>
var workerState = <?php echo json_encode($workerState, JSON_UNESCAPED_SLASHES); ?>;
var resumeStats = <?php echo json_encode($stats, JSON_UNESCAPED_SLASHES); ?>;
var queueStats = <?php echo json_encode($queueStats, JSON_UNESCAPED_SLASHES); ?>;
var parserStatus = <?php echo json_encode($parserStatus, JSON_UNESCAPED_SLASHES); ?>;
var pollTimer = null;

function renderManualCommand() {
    var limit = parseInt($('#worker_limit').val() || 200, 10);
    if (!limit || limit < 10) {
        limit = 200;
    }
    if (limit > 500) {
        limit = 500;
    }
    $('#worker_limit').val(limit);
    var token = 'manualrun_' + Date.now();
    var command = 'C:\\xampp\\php\\php.exe C:\\xampp\\htdocs\\hr\\resume_worker.php --limit=' + limit + ' --token=' + token;
    $('#manualWorkerCommand').val(command);
    $('#copyCommandMessage').text('New batch command is ready.');
}

function renderWorkerState(state) {
    state = state || {};
    var isRunning = parseInt(state.is_running || 0, 10) === 1;
    var limit = parseInt(state.worker_limit || 0, 10);
    var processed = parseInt(state.processed_total || 0, 10);
    var percent = limit > 0 ? Math.min(100, Math.round((processed / limit) * 100)) : 0;
    var completed = parseInt(state.completed_total || 0, 10);
    var pending = parseInt(state.pending_total || 0, 10);
    var remaining = Math.max(0, limit - processed);

    var statusText = 'Worker is idle.';
    if (isRunning) {
        statusText = 'Worker is running.';
    } else if (processed > 0 && limit > 0 && processed >= limit) {
        statusText = 'Batch finished. Prepare the next batch.';
    }
    $('#workerStatusText').text(statusText);
    $('#workerProgressBar').css('width', percent + '%').text(percent + '%');
    $('#workerProgressPercent').text(percent + '%');
    $('#workerToken').text(state.worker_token || '');
    $('#workerCurrentFile').text(state.current_file || '');
    $('#workerHeartbeat').text(state.heartbeat_at || '');
    $('#workerMessage').text(state.last_message || '');
    $('#workerProcessed').text(processed);

    $('#runLimit').text(limit);
    $('#runProcessed').text(processed);
    $('#runCompleted').text(completed);
    $('#runPending').text(pending);
    $('#runMissing').text(parseInt(state.missing_total || 0, 10));
    $('#runUnsupported').text(parseInt(state.unsupported_total || 0, 10));
    $('#runError').text(parseInt(state.error_total || 0, 10));
    $('#runStarted').text(state.started_at || '');
    $('#runFinished').text(state.finished_at || '');

    $('#liveProcessed').text(processed);
    $('#liveCompleted').text(completed);
    $('#livePending').text(pending);
    $('#liveRemaining').text(remaining);
    $('#runProgressText').text(processed + ' / ' + limit + ' processed');
    $('#runProgressPercent').text(percent + '%');
    $('#runProgressBar').css('width', percent + '%').text(percent + '%');
}

function renderGlobalStats(stats, queue) {
    stats = stats || {};
    queue = queue || {};

    $('#queueWithResume').text(parseInt(queue.with_resume || 0, 10));
    $('#queueIndexed').text(parseInt(queue.indexed || 0, 10));
    $('#queueRemaining').text(parseInt(queue.remaining || 0, 10));

    $('#statCompleted').text(parseInt(stats.completed || 0, 10));
    $('#statPending').text(parseInt(stats.pending || 0, 10));
    $('#statMissing').text(parseInt(stats.missing || 0, 10));
    $('#statUnsupported').text(parseInt(stats.unsupported || 0, 10));
    $('#statError').text(parseInt(stats.error || 0, 10));

    var withResume = parseInt(queue.with_resume || 0, 10);
    var indexed = parseInt(queue.indexed || 0, 10);
    var queuePercent = withResume > 0 ? Math.min(100, Math.round((indexed / withResume) * 100)) : 0;
    $('#queueProgressText').text(indexed + ' / ' + withResume + ' indexed');
    $('#queueProgressPercent').text(queuePercent + '%');
    $('#queueProgressBar').css('width', queuePercent + '%').text(queuePercent + '%');
}

function renderParserStatus(status) {
    status = status || {};
    var ready = !!status.tika_available;
    $('#tikaStatusLabel')
        .text(ready ? 'Ready' : 'Not Ready')
        .removeClass('parser-ready parser-not-ready')
        .addClass(ready ? 'parser-ready' : 'parser-not-ready');
}

function pollWorkerState() {
    $.post('resume_intelligence.php', { action: 'poll_worker' }, function(response) {
        if (!response.ok) {
            return;
        }

        workerState = response.worker || workerState;
        resumeStats = response.stats || resumeStats;
        queueStats = response.queue || queueStats;

        renderWorkerState(workerState);
        renderGlobalStats(resumeStats, queueStats);

        if ($('#autoRefreshToggle').is(':checked')) {
            pollTimer = setTimeout(pollWorkerState, 3000);
        }
    }, 'json');
}

$(function() {
    renderWorkerState(workerState);
    renderGlobalStats(resumeStats, queueStats);
    renderParserStatus(parserStatus);
    renderManualCommand();

    $('#worker_limit').on('input change', function() {
        renderManualCommand();
    });

    $('#prepareNextBatchBtn').on('click', function() {
        renderManualCommand();
        $('#manualWorkerCommand').focus().select();
    });

    $('#copyCommandBtn').on('click', function() {
        var commandBox = document.getElementById('manualWorkerCommand');
        commandBox.select();
        commandBox.setSelectionRange(0, 99999);
        try {
            document.execCommand('copy');
            $('#copyCommandMessage').text('Command copied.');
        } catch (e) {
            $('#copyCommandMessage').text('Copy failed. Please copy manually.');
        }
    });

    $('#refreshStatusBtn').on('click', function() {
        if (pollTimer) {
            clearTimeout(pollTimer);
        }
        pollWorkerState();
    });

    $('#autoRefreshToggle').on('change', function() {
        if ($(this).is(':checked')) {
            if (pollTimer) {
                clearTimeout(pollTimer);
            }
            pollWorkerState();
        } else if (pollTimer) {
            clearTimeout(pollTimer);
        }
    });

    if ($('#autoRefreshToggle').is(':checked')) {
        pollWorkerState();
    }

    $('#parserConfigForm').on('submit', function(e) {
        e.preventDefault();

        $.post('resume_intelligence.php', {
            action: 'save_parser_config',
            java_path: $('#java_path').val(),
            tika_jar_path: $('#tika_jar_path').val()
        }, function(response) {
            $('#parserConfigMessage').text(response.message || '');
            if (response.parser) {
                parserStatus = response.parser;
                renderParserStatus(parserStatus);
            }
        }, 'json').fail(function() {
            $('#parserConfigMessage').text('Could not save parser config.');
        });
    });
});
</script>
<?php include 'inc/template_end.php'; ?>
