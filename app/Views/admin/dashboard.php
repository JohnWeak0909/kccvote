<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - KEVS (KCC e-Voting System)</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('css/theme-unified.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/burger-menu.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
    <script>window.dashboardStatsUrl = <?= json_encode(base_url('api/stats')) ?>;</script>
    <script src="<?= base_url('js/burger-menu.js') ?>" defer></script>
    <script src="<?= base_url('js/main.js') ?>" defer></script>
</head>
<body class="dashboard-page dashboard-home">
    <?= view('admin/_sidebar') ?>
    <div class="admin-container">
        <main class="content">
            <header class="dashboard-header">
                <div class="header-content">
                    <h1 style="color: #FFFFFF;">Dashboard Overview</h1>
                    <p>Welcome back, <?= htmlspecialchars($admin_name ?? 'Admin') ?>!</p>
                </div>
                <div class="header-actions">
                    <div class="current-time" id="current-time"></div>
                    <button id="dashboard-refresh" class="btn btn-secondary" style="margin-right: 0.75rem;">Refresh</button>
                </div>
            </header>

            <div class="stats-overview">
                <div class="stat-card primary">
                    <div class="stat-icon">👥</div>
                    <div class="stat-content">
                        <h3 id="student-count"><?= esc($student_count) ?></h3>
                        <p>Total Students</p>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon">🗳️</div>
                    <div class="stat-content">
                        <h3 id="vote-count"><?= esc($vote_count) ?></h3>
                        <p>Votes Cast</p>
                    </div>
                </div>
                <div class="stat-card info">
                    <div class="stat-icon">👤</div>
                    <div class="stat-content">
                        <h3 id="candidate-count"><?= esc($candidate_count) ?></h3>
                        <p>Total Candidates</p>
                    </div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon">📊</div>
                    <div class="stat-content">
                        <h3 id="election-status"><?= esc($election_status ?? 'NONE') ?></h3>
                        <p>Election Status</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <div class="card-header"><h3>Vote Progress</h3></div>
                    <div class="card-content">
                            <div class="progress" style="height: 18px; background: rgba(229,231,235,0.8); border-radius: 12px; overflow: hidden; margin-bottom: 1rem;">
                            <div id="vote-progress-bar" class="progress-bar" role="progressbar" style="width: <?= esc($vote_progress) ?>%; background: var(--primary); color: #fff; line-height: 18px; text-align: center; min-width: 40px;"><?= esc($vote_progress) ?>%</div>
                        </div>
                        <p id="vote-progress-text"><?= esc($vote_count) ?>/<?= esc($student_count ?: 0) ?> votes cast</p>
                    </div>
                </div>
                <div class="dashboard-card">
                    <div class="card-header"><h3>Recent Activity</h3></div>
                    <div class="card-content" id="recent-activity-list">
                        <ul style="list-style: none; padding: 0; margin: 0;" id="activity-items">
                            <li style="padding: 0.75rem 0; border-bottom: 1px solid rgba(229,231,235,0.6);">Loading recent activity...</li>
                        </ul>
                    </div>
                </div>
                <div class="dashboard-card">
                    <div class="card-header"><h3>System Status</h3></div>
                    <div class="card-content" id="system-status-list">
                        <ul style="list-style: none; padding: 0; margin: 0;" id="status-items">
                            <li style="padding: 0.75rem 0; border-bottom: 1px solid rgba(229,231,235,0.6);">Loading system status...</li>
                        </ul>
                    </div>
                </div>
                <div class="dashboard-card">
                    <div class="card-header"><h3>Navigation</h3></div>
                    <div class="card-content">
                        <ul style="list-style: none; padding: 0;">
                            <li><a href="<?= base_url('admin/candidates') ?>">Manage Candidates</a></li>
                            <li><a href="<?= base_url('admin/elections') ?>">Manage Elections</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

