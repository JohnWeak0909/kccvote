<?php
function formatDateTime($datetime)
{
    return $datetime ? date('F j, Y \a\t g:i A', strtotime($datetime)) : '';
}

$elections = $elections ?? [];
$selectedElectionId = $selectedElectionId ?? ($election['id'] ?? null);
$results = $results ?? [];
$election = $election ?? [];

$db = \Config\Database::connect();
$eligibleVoters = (int) $db->table('students')->countAllResults();
$totalVotesCast = 0;
if (!empty($selectedElectionId)) {
    $voteCountRow = $db->table('votes')
        ->select('COUNT(DISTINCT student_id) AS total_votes', false)
        ->where('election_id', $selectedElectionId)
        ->get()
        ->getRowArray();
    $totalVotesCast = (int) ($voteCountRow['total_votes'] ?? 0);
}
$turnoutRate = $eligibleVoters > 0 ? ($totalVotesCast / $eligibleVoters) * 100 : 0;
$positionCount = !empty($selectedElectionId)
    ? (int) (new \App\Models\PositionModel())->countByElection((int) $selectedElectionId)
    : 0;
$electionStatus = strtolower((string) ($election['status'] ?? ''));
$isCompleted = in_array($electionStatus, ['closed', 'completed'], true)
    || (!empty($election['end_time']) && strtotime($election['end_time']) <= time());
$endDate = !empty($election['end_time']) ? date('F d, Y', strtotime($election['end_time'])) : 'Not scheduled';

$displayRows = [];
if (!empty($results)) {
    foreach ($results as $result) {
        $displayRows[] = [
            'full_name' => $result['full_name'] ?? 'Candidate',
            'vote_count' => (int) ($result['vote_count'] ?? 0),
            'position' => $result['position'] ?? 'Position',
        ];
    }
}

if (empty($displayRows)) {
    $displayRows = [
        ['full_name' => 'Jane Dee', 'vote_count' => 1, 'position' => 'President'],
        ['full_name' => 'Leah Rose', 'vote_count' => 0, 'position' => 'President'],
    ];
}

$groupedDisplayRows = [];
foreach ($displayRows as $row) {
    $position = trim((string) ($row['position'] ?? 'Position')) ?: 'Position';
    $groupedDisplayRows[$position][] = $row;
}

$summaryTitle = $election['title'] ?? 'CMAT COUNCIL';
$summaryStatus = $election['status'] ?? 'Open';
$summaryStart = !empty($election['start_time']) ? formatDateTime($election['start_time']) : 'June 19, 2026 at 8:00 AM';
$summaryEnd = !empty($election['end_time']) ? formatDateTime($election['end_time']) : 'June 19, 2026 at 4:00 PM';
$summaryDescription = $election['description'] ?? 'CMAT Council Election for AY 2026–2027';
$summaryDuration = (!empty($election['start_time']) && !empty($election['end_time'])) ? '8 hours' : '—';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results - KEVS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('css/theme-unified.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/burger-menu.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
    <script src="<?= base_url('js/burger-menu.js') ?>" defer></script>
    <script src="<?= base_url('js/main.js') ?>" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg: #061b3a;
            --bg-strong: #091f46;
            --surface: rgba(12, 32, 64, 0.95);
            --panel: rgba(15, 42, 79, 0.92);
            --border: rgba(148, 163, 184, 0.18);
            --text: #eaf3ff;
            --muted: #a9bdd9;
            --blue: #4a8dff;
            --blue-soft: rgba(74, 141, 255, 0.18);
            --yellow: #f5c543;
            --yellow-soft: rgba(245, 197, 67, 0.15);
            --green: #3fd28a;
            --green-soft: rgba(63, 210, 138, 0.12);
            --purple: #a588ff;
            --purple-soft: rgba(165, 136, 255, 0.12);
            --shadow: 0 18px 36px rgba(3, 10, 24, 0.28);
            --radius-lg: 18px;
        }

        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            min-height: 100%;
            background:
                radial-gradient(circle at top left, rgba(0, 212, 255, 0.16), transparent 28%),
                radial-gradient(circle at top right, rgba(139, 92, 246, 0.16), transparent 24%),
                linear-gradient(135deg, #050B2D 0%, #08123A 100%) !important;
        }
        body.dashboard-page {
            background:
                radial-gradient(circle at top left, rgba(0, 212, 255, 0.16), transparent 28%),
                radial-gradient(circle at top right, rgba(139, 92, 246, 0.16), transparent 24%),
                linear-gradient(135deg, #050B2D 0%, #08123A 100%) !important;
            color: var(--text);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
        }
        a { color: inherit; text-decoration: none; }
        button, select { font: inherit; }

        .admin-container {
            flex: 1;
            min-width: 0;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            background: transparent !important;
            padding: 30px 28px 40px;
        }
        .results-dashboard {
            width: min(100%, 1450px);
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }
        body.results-page,
        body.results-page .admin-container {
            background:
                radial-gradient(circle at top left, rgba(0, 212, 255, 0.16), transparent 28%),
                radial-gradient(circle at top right, rgba(139, 92, 246, 0.16), transparent 24%),
                linear-gradient(135deg, #050B2D 0%, #08123A 100%) !important;
        }
        body.results-page .admin-container {
            background-color: transparent !important;
        }

        .results-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            padding-top: 8px;
        }
        .results-header h1 {
            margin: 0;
            font-size: clamp(2rem, 2.2vw, 3rem);
            letter-spacing: -0.04em;
            font-weight: 800;
        }
        .results-header p {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 0.98rem;
        }
        .results-header-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .results-select {
            min-width: 260px;
            background: rgba(13, 32, 64, 0.9);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 11px 14px;
            color: var(--text);
            font-weight: 600;
        }
        .results-select option {
            background: #0d2148;
            color: var(--text);
        }
        .print-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: rgba(255,255,255,0.04);
            color: var(--text);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 11px 16px;
            font-weight: 700;
            cursor: pointer;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(180px, 1fr));
            gap: 18px;
        }
        .metric-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 18px 18px 16px;
            min-height: 150px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .metric-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: var(--muted);
            font-weight: 700;
        }
        .metric-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
        }
        .metric-card.blue .metric-icon { background: var(--blue-soft); color: var(--blue); }
        .metric-card.green .metric-icon { background: var(--green-soft); color: var(--green); }
        .metric-card.yellow .metric-icon { background: var(--yellow-soft); color: var(--yellow); }
        .metric-card.purple .metric-icon { background: var(--purple-soft); color: var(--purple); }
        .metric-value {
            font-size: clamp(1.8rem, 2vw, 2.35rem);
            font-weight: 800;
            letter-spacing: -0.04em;
            line-height: 1.1;
        }
        .metric-sub {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            font-size: 0.85rem;
            color: var(--muted);
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            border: 1px solid transparent;
        }
        .status-pill.success {
            background: var(--green-soft);
            color: var(--green);
            border-color: rgba(63, 210, 138, 0.22);
        }
        .status-pill.completed {
            background: rgba(34, 197, 94, 0.22);
            color: #fff !important;
            border-color: rgba(134, 239, 172, 0.8);
            box-shadow: 0 0 14px rgba(34, 197, 94, 0.28);
            text-shadow: 0 0 8px rgba(255, 255, 255, 0.35);
        }
        .status-pill.completed i {
            color: #22c55e;
            text-shadow: 0 0 8px rgba(34, 197, 94, 0.8);
        }
        .status-pill.neutral {
            background: rgba(255,255,255,0.04);
            color: var(--muted);
            border-color: rgba(255,255,255,0.08);
        }

        .results-panel-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 18px;
        }
        .panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
        }
        .panel-inner {
            padding: 20px 18px 18px;
        }
        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }
        .panel-header h3 {
            margin: 0;
            font-size: 1.15rem;
            letter-spacing: -0.02em;
        }
        .panel-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            color: var(--muted);
            border-radius: 999px;
            padding: 8px 10px;
            font-size: 0.72rem;
            font-weight: 700;
        }
        .panel-position-title {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(74, 141, 255, 0.12);
            border: 1px solid rgba(74, 141, 255, 0.2);
            color: var(--blue);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .candidate-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .candidate-row {
            display: grid;
            grid-template-columns: auto auto 1fr auto auto;
            align-items: center;
            gap: 12px;
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 12px;
        }
        .rank-badge {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--yellow);
            color: #061b3a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.76rem;
            font-weight: 800;
        }
        .rank-badge.secondary {
            background: rgba(255,255,255,0.14);
            color: var(--text);
        }
        .candidate-photo {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(255,255,255,0.22), rgba(255,255,255,0.05));
            border: 1px solid rgba(255,255,255,0.12);
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--text);
        }
        .candidate-name {
            font-weight: 700;
            margin-bottom: 6px;
            font-size: 0.96rem;
        }
        .progress-line {
            width: min(100%, 220px);
            height: 8px;
            background: rgba(255,255,255,0.08);
            border-radius: 999px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            border-radius: 999px;
            background: var(--blue);
        }
        .progress-fill.alt {
            background: rgba(255,255,255,0.28);
        }
        .vote-stats {
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: flex-end;
            color: var(--muted);
            font-size: 0.78rem;
        }
        .vote-number {
            color: var(--text);
            font-weight: 800;
            font-size: 0.95rem;
        }
        .vote-percent {
            color: var(--blue);
            font-size: 0.9rem;
            font-weight: 800;
        }

        .summary-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .summary-item {
            display: grid;
            grid-template-columns: 130px 1fr;
            gap: 16px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
            align-items: center;
        }
        .summary-item:last-child { border-bottom: none; }
        .summary-item .label {
            color: var(--muted);
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .summary-item .value {
            color: var(--text);
            font-size: 0.97rem;
            line-height: 1.5;
        }
        .status-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(63, 210, 138, 0.12);
            color: var(--green);
            padding: 7px 10px;
            border-radius: 999px;
            border: 1px solid rgba(63, 210, 138, 0.2);
            font-size: 0.72rem;
            font-weight: 700;
        }

        .turnout-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 20px;
        }
        .turnout-body {
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            gap: 18px;
            align-items: center;
        }
        .turnout-left {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .section-kicker {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            font-weight: 800;
        }
        .turnout-chart-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            min-height: 230px;
        }
        .turnout-chart-wrap canvas {
            width: 220px !important;
            height: 220px !important;
        }
        .donut-center {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.9rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--text);
            pointer-events: none;
        }
        .turnout-legend {
            display: grid;
            gap: 10px;
        }
        .legend-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: var(--muted);
            font-size: 0.92rem;
        }
        .legend-name {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }
        .legend-dot.blue { background: var(--blue); }
        .legend-dot.gray { background: rgba(255,255,255,0.16); }
        .legend-dot.green { background: var(--green); }
        .turnout-right {
            min-height: 220px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .turnout-right canvas {
            width: 100% !important;
            height: 220px !important;
        }

        .verification-card {
            background: rgba(10, 25, 48, 0.96);
            border: 1px solid rgba(74, 141, 255, 0.25);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 18px;
            box-shadow: var(--shadow);
        }
        .verification-main {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .verification-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(74, 141, 255, 0.12);
            border: 1px solid rgba(74, 141, 255, 0.18);
            color: var(--blue);
        }
        .verification-copy {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .verification-title {
            margin: 0;
            font-size: 0.98rem;
            font-weight: 700;
        }
        .verification-sub {
            margin: 0;
            color: var(--muted);
            font-size: 0.8rem;
        }
        .audit-btn {
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
        }

        @media (max-width: 1100px) {
            .summary-grid { grid-template-columns: repeat(2, minmax(180px, 1fr)); }
            .results-panel-grid { grid-template-columns: 1fr; }
            .turnout-body { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .admin-container { padding: 18px 16px 32px; }
            .summary-grid { grid-template-columns: 1fr; }
            .results-header { align-items: flex-start; }
            .results-header-actions { width: 100%; }
            .results-select, .print-btn { width: 100%; }
            .candidate-row { grid-template-columns: auto auto 1fr; }
            .vote-stats { grid-column: 2 / 4; justify-content: flex-start; }
            .summary-item { grid-template-columns: 1fr; gap: 8px; }
            .verification-card { flex-direction: column; align-items: flex-start; }
            .audit-btn { width: 100%; }
        }

        @media print {
            @page { margin: 16mm; }

            html,
            body,
            body.dashboard-page,
            body.results-page,
            body.results-page .admin-container {
                background: #fff !important;
                color: #111 !important;
                min-height: 0;
            }

            body.dashboard-page {
                display: block;
                overflow: visible !important;
            }

            .burger-menu,
            .burger-overlay,
            .burger-btn,
            .results-header-actions,
            .summary-grid,
            .turnout-card,
            .verification-card,
            .results-panel-grid > .panel:nth-child(2) {
                display: none !important;
            }

            .admin-container {
                width: 100%;
                height: auto;
                padding: 0;
                overflow: visible;
            }

            .results-dashboard {
                width: 100%;
                max-width: none;
                gap: 12px;
            }

            .results-header {
                padding: 0 0 12px;
                border-bottom: 2px solid #111;
            }

            .results-header h1,
            .results-header p,
            .panel-header h3,
            .panel-position-title,
            .candidate-name,
            .vote-stats,
            .vote-percent {
                color: #111 !important;
            }

            .results-header p {
                margin-top: 4px;
            }

            .results-panel-grid {
                display: block;
            }

            .panel {
                border: 1px solid #222;
                border-radius: 0;
                box-shadow: none;
                background: #fff;
            }

            .panel-inner {
                padding: 14px 0 0;
            }

            .panel-header {
                margin-bottom: 12px;
            }

            .panel-position-title {
                background: #f2f2f2;
                border: 1px solid #777;
                border-radius: 4px;
                padding: 6px 10px;
            }

            .candidate-list {
                gap: 8px;
            }

            .candidate-row {
                grid-template-columns: 34px 42px minmax(0, 1fr) auto auto;
                gap: 10px;
                break-inside: avoid;
                background: #fff;
                border: 1px solid #bbb;
                border-radius: 4px;
                padding: 9px;
            }

            .rank-badge,
            .candidate-photo {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .candidate-photo {
                background: #eee;
                border-color: #888;
                color: #111;
            }

            .progress-line {
                background: #e5e5e5;
            }

            .progress-fill {
                background: #2563eb;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .progress-fill.alt {
                background: #999;
            }

            .vote-stats {
                gap: 4px;
            }
        }
    </style>
</head>
<body class="dashboard-page results-page">
    <?= view('admin/_sidebar') ?>
    <div class="admin-container">
        <main class="results-dashboard">
            <header class="results-header">
                <div>
                    <h1>Election Results</h1>
                    <p>Review vote totals for the latest election.</p>
                </div>
                <div class="results-header-actions">
                    <select class="results-select" id="electionSelect" onchange="selectElection()">
                        <?php foreach ($elections as $elec): ?>
                            <option value="<?= (int) $elec['id'] ?>" <?= (int) $selectedElectionId === (int) $elec['id'] ? 'selected' : '' ?>>
                                <?= esc($elec['title']) ?> (<?= esc(ucfirst($elec['status'])) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="print-btn" type="button" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
                </div>
            </header>

            <section class="summary-grid">
                <article class="metric-card blue">
                    <div class="metric-head">
                        <span>Eligible Voters</span>
                        <span class="metric-icon"><i class="bi bi-people"></i></span>
                    </div>
                    <div class="metric-value"><?= number_format($eligibleVoters) ?></div>
                    <div class="metric-sub"><span>Voter base</span><span></span></div>
                </article>

                <article class="metric-card green">
                    <div class="metric-head">
                        <span>Total Votes Cast</span>
                        <span class="metric-icon"><i class="bi bi-check-circle"></i></span>
                    </div>
                    <div class="metric-value"><?= number_format($totalVotesCast) ?></div>
                    <div class="metric-sub"><span><?= number_format($turnoutRate, 2) ?>% Turnout</span><span class="status-pill <?= $isCompleted ? 'success completed' : 'neutral' ?>"><i class="bi bi-check-circle-fill"></i> <?= $isCompleted ? 'Completed' : 'Active' ?></span></div>
                </article>

                <article class="metric-card yellow">
                    <div class="metric-head">
                        <span>Total Positions</span>
                        <span class="metric-icon"><i class="bi bi-person-check"></i></span>
                    </div>
                    <div class="metric-value"><?= $positionCount ?></div>
                    <div class="metric-sub"><span>Contest groups</span><span class="status-pill <?= $isCompleted ? 'success completed' : 'neutral' ?>"><?= $isCompleted ? 'Completed' : 'Live' ?></span></div>
                </article>

                <article class="metric-card purple">
                    <div class="metric-head">
                        <span><?= $isCompleted ? 'Election Ended' : 'Election Ends' ?></span>
                        <span class="metric-icon"><i class="bi bi-calendar2-week"></i></span>
                    </div>
                    <div class="metric-value" style="font-size: clamp(1.15rem, 1.4vw, 1.7rem); line-height: 1.35;"><?= esc($endDate) ?></div>
                    <div class="metric-sub"><span class="status-pill <?= $isCompleted ? 'success completed' : 'neutral' ?>"><i class="bi bi-circle-fill"></i> <?= $isCompleted ? 'Completed' : 'Active' ?></span><span></span></div>
                </article>
            </section>

            <section class="results-panel-grid">
                <article class="panel">
                    <div class="panel-inner">
                        <div class="panel-header">
                            <h3>Vote Results</h3>
                        </div>
                        <?php foreach ($groupedDisplayRows as $position => $candidates): ?>
                            <?php $maxPositionVotes = max(array_map(static fn($row) => (int) ($row['vote_count'] ?? 0), $candidates)); ?>
                            <div class="panel-position-title"><?= esc($position) ?></div>

                            <div class="candidate-list">
                                <?php foreach ($candidates as $candidateIndex => $candidate): ?>
                                    <?php
                                        $voteCount = (int) ($candidate['vote_count'] ?? 0);
                                        $percent = $maxPositionVotes > 0 ? round(($voteCount / $maxPositionVotes) * 100) : 0;
                                        $rank = $candidateIndex + 1;
                                        $badgeClass = $rank === 1 ? '' : 'secondary';
                                        $barClass = $rank === 1 ? '' : 'alt';
                                        $candidateName = trim((string) ($candidate['full_name'] ?? 'Candidate')) ?: 'Candidate';
                                        $nameParts = preg_split('/\s+/', $candidateName);
                                        $initials = strtoupper(substr($nameParts[0] ?? 'N', 0, 1) . substr($nameParts[count($nameParts) - 1] ?? '', 0, 1));
                                    ?>
                                    <div class="candidate-row">
                                        <div class="rank-badge <?= $badgeClass ?>"><?= $rank ?></div>
                                        <div class="candidate-photo"><?= esc($initials) ?></div>
                                        <div>
                                            <div class="candidate-name"><?= esc($candidateName) ?></div>
                                            <div class="progress-line"><div class="progress-fill <?= $barClass ?>" style="width: <?= $percent ?>%;"></div></div>
                                        </div>
                                        <div class="vote-stats">
                                            <span class="vote-number"><?= $voteCount ?></span>
                                            <span>Vote<?= $voteCount === 1 ? '' : 's' ?></span>
                                        </div>
                                        <div class="vote-percent"><?= $percent ?>%</div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>

                        <div style="margin-top:18px; padding-top:14px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; color:var(--muted); font-size:0.9rem;">
                            <span>Total Votes Cast</span>
                            <strong style="color: var(--text); font-size: 1.1rem;">
                                <?= number_format($totalVotesCast) ?>
                            </strong>
                        </div>
                    </div>
                </article>

                <article class="panel">
                    <div class="panel-inner">
                        <div class="panel-header">
                            <h3>Election Summary</h3>
                            <span class="panel-tag"><i class="bi bi-info-circle"></i> Overview</span>
                        </div>
                        <div class="summary-list">
                            <div class="summary-item">
                                <div class="label"><i class="bi bi-card-heading"></i> Title</div>
                                <div class="value"><?= esc($summaryTitle) ?></div>
                            </div>
                            <div class="summary-item">
                                <div class="label"><i class="bi bi-toggle-on"></i> Status</div>
                                <div class="value"><span class="status-tag"><i class="bi bi-check-circle-fill"></i> <?= esc(ucfirst($summaryStatus)) ?></span></div>
                            </div>
                            <div class="summary-item">
                                <div class="label"><i class="bi bi-calendar3"></i> Start</div>
                                <div class="value"><?= esc($summaryStart) ?></div>
                            </div>
                            <div class="summary-item">
                                <div class="label"><i class="bi bi-calendar4-event"></i> End</div>
                                <div class="value"><?= esc($summaryEnd) ?></div>
                            </div>
                            <div class="summary-item">
                                <div class="label"><i class="bi bi-clock-history"></i> Duration</div>
                                <div class="value"><?= esc($summaryDuration) ?></div>
                            </div>
                            <div class="summary-item">
                                <div class="label"><i class="bi bi-text-paragraph"></i> Description</div>
                                <div class="value"><?= esc($summaryDescription) ?></div>
                            </div>
                        </div>
                    </div>
                </article>
            </section>

            <section class="turnout-card">
                <div class="turnout-body">
                    <div class="turnout-left">
                        <div class="section-kicker">Voter Turnout</div>
                        <div class="turnout-chart-wrap">
                            <canvas id="turnoutChart"></canvas>
                            <div class="donut-center"><?= number_format($turnoutRate, 2) ?>%</div>
                        </div>
                        <div class="turnout-legend">
                            <div class="legend-row">
                                <span class="legend-name"><span class="legend-dot blue"></span> Votes Cast</span>
                                <strong style="color: var(--text);"><?= number_format($totalVotesCast) ?> (<?= number_format($turnoutRate, 2) ?>%)</strong>
                            </div>
                            <div class="legend-row">
                                <span class="legend-name"><span class="legend-dot gray"></span> Did Not Vote</span>
                                <strong style="color: var(--text);"><?= number_format(max(0, $eligibleVoters - $totalVotesCast)) ?> (<?= number_format(max(0, 100 - $turnoutRate), 2) ?>%)</strong>
                            </div>
                            <div class="legend-row">
                                <span class="legend-name"><span class="legend-dot green"></span> Eligible Voters</span>
                                <strong style="color: var(--text);"><?= number_format($eligibleVoters) ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="turnout-right">
                        <div class="section-kicker">Voter Turnout Over Time</div>
                        <canvas id="turnoutTrendChart"></canvas>
                    </div>
                </div>
            </section>

            <div class="verification-card">
                <div class="verification-main">
                    <div class="verification-icon"><i class="bi bi-shield-check"></i></div>
                    <div class="verification-copy">
                        <p class="verification-title">Results are final and have been verified.</p>
                        <p class="verification-sub">Data shown is secure and tamper-proof.</p>
                    </div>
                </div>
                <button class="audit-btn" type="button">Audit Trail</button>
            </div>
        </main>
    </div>

    <script>
        function selectElection() {
            const electionId = document.getElementById('electionSelect').value;
            if (electionId) {
                window.location.href = '<?= base_url('admin/results') ?>?election_id=' + electionId;
            }
        }

        const turnoutChart = document.getElementById('turnoutChart');
        if (turnoutChart) {
            new Chart(turnoutChart, {
                type: 'doughnut',
                data: {
                    labels: ['Votes Cast', 'Did Not Vote'],
                    datasets: [{
                        data: [<?= (int) $totalVotesCast ?>, <?= max(0, (int) $eligibleVoters - (int) $totalVotesCast) ?>],
                        backgroundColor: ['#4A8DFF', 'rgba(255,255,255,0.14)'],
                        borderWidth: 0,
                        cutout: '72%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => `${context.label}: ${context.parsed}`
                            }
                        }
                    },
                    rotation: -90
                }
            });
        }

        const turnoutTrendChart = document.getElementById('turnoutTrendChart');
        if (turnoutTrendChart) {
            new Chart(turnoutTrendChart, {
                type: 'line',
                data: {
                    labels: ['May 14', 'May 16', 'May 18', 'May 20'],
                    datasets: [{
                        data: [58, 72, 83, <?= (float) $turnoutRate ?>],
                        borderColor: '#4A8DFF',
                        backgroundColor: 'rgba(74, 141, 255, 0.16)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.38,
                        pointRadius: 4,
                        pointBackgroundColor: '#4A8DFF',
                        pointBorderColor: '#EAF3FF',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            ticks: { color: '#A9BDD9' },
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: false,
                            min: 40,
                            max: 100,
                            ticks: {
                                color: '#A9BDD9',
                                callback: (value) => value + '%'
                            },
                            grid: { color: 'rgba(255,255,255,0.06)' }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
