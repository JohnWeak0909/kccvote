<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isAdmin()) redirect('login.php');

// Fetch some stats
$student_count = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$candidate_count = $pdo->query("SELECT COUNT(*) FROM candidates")->fetchColumn();
$vote_count = $pdo->query("SELECT COUNT(*) FROM votes")->fetchColumn();
$election = $pdo->query("SELECT * FROM elections ORDER BY id DESC LIMIT 1")->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - KCC Online Voting</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/main.js" defer></script>
</head>
<body>
    <div class="admin-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/images/kcclogo.jpg" alt="KCC Online Voting Logo" class="sidebar-logo">
                <h2>KCC Online Voting</h2>
            </div>
            <ul>
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="elections.php">Elections</a></li>
                <li><a href="positions.php">Positions</a></li>
                <li><a href="parties.php">Parties</a></li>
                <li><a href="candidates.php">Candidates</a></li>
                <li><a href="students.php">Students</a></li>
                <li><a href="admins.php">Admins</a></li>
                <li><a href="campus_ips.php">Network Settings</a></li>
                <li><a href="results.php">Results</a></li>
                <li><a href="location_settings.php">Access Control</a></li>
                <li><a href="security_monitor.php">Security Monitor</a></li>
                <li><a href="school_hours.php">School Hours</a></li>
                <li><a href="../public/logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="content">
            <header class="dashboard-header">
                <div class="header-content">
                    <h1>Dashboard Overview</h1>
                    <p>Welcome back, <?php echo $_SESSION['admin_name']; ?>!</p>
                </div>
                <div class="header-actions">
                    <div class="current-time" id="current-time"></div>
                    <button class="btn-refresh" onclick="refreshStats()">
                        <span class="icon">🔄</span> Refresh
                    </button>
                </div>
            </header>

            <!-- Quick Stats Row -->
            <div class="stats-overview">
                <div class="stat-card primary">
                    <div class="stat-icon">👥</div>
                    <div class="stat-content">
                        <h3 id="student-count"><?php echo $student_count; ?></h3>
                        <p>Total Students</p>
                        <span class="stat-change">+12% from last month</span>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon">🗳️</div>
                    <div class="stat-content">
                        <h3 id="vote-count"><?php echo $vote_count; ?></h3>
                        <p>Votes Cast</p>
                        <span class="stat-change live">Live counting active</span>
                    </div>
                </div>
                <div class="stat-card info">
                    <div class="stat-icon">👤</div>
                    <div class="stat-content">
                        <h3 id="candidate-count"><?php echo $candidate_count; ?></h3>
                        <p>Total Candidates</p>
                        <span class="stat-change">Across all positions</span>
                    </div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon">📊</div>
                    <div class="stat-content">
                        <h3 id="election-status">
                            <?php echo $election ? ucfirst($election['status']) : 'None'; ?>
                        </h3>
                        <p>Election Status</p>
                        <span class="stat-change">
                            <?php echo $election ? $election['title'] : 'No active election'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Charts and Analytics Row -->
            <div class="dashboard-grid">
                <!-- Real-time Vote Progress -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Real-time Vote Progress</h3>
                        <div class="card-actions">
                            <span class="live-indicator">
                                <span class="pulse"></span> Live
                            </span>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="progress-container">
                            <div class="progress-bar">
                                <div class="progress-fill" id="vote-progress" style="width: <?php echo $student_count > 0 ? min(100, ($vote_count / $student_count) * 100) : 0; ?>%"></div>
                            </div>
                            <div class="progress-text">
                                <span id="progress-text"><?php echo $vote_count; ?>/<?php echo $student_count; ?> votes cast</span>
                                <span class="percentage"><?php echo $student_count > 0 ? round(($vote_count / $student_count) * 100, 1) : 0; ?>%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Recent Activity</h3>
                    </div>
                    <div class="card-content">
                        <div class="activity-list" id="recent-activity">
                            <div class="activity-item">
                                <div class="activity-icon">🗳️</div>
                                <div class="activity-content">
                                    <p>New vote recorded</p>
                                    <span>2 minutes ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon">👤</div>
                                <div class="activity-content">
                                    <p>Student registered</p>
                                    <span>15 minutes ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon">📊</div>
                                <div class="activity-content">
                                    <p>Election status updated</p>
                                    <span>1 hour ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="card-content">
                        <div class="quick-actions">
                            <a href="elections.php" class="action-btn">
                                <span class="action-icon">📅</span>
                                <span>Manage Elections</span>
                            </a>
                            <a href="candidates.php" class="action-btn">
                                <span class="action-icon">👥</span>
                                <span>Add Candidates</span>
                            </a>
                            <a href="results.php" class="action-btn">
                                <span class="action-icon">📊</span>
                                <span>View Results</span>
                            </a>
                            <a href="students.php" class="action-btn">
                                <span class="action-icon">🎓</span>
                                <span>Student Management</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>System Status</h3>
                    </div>
                    <div class="card-content">
                        <div class="status-grid">
                            <div class="status-item">
                                <span class="status-label">Database</span>
                                <span class="status-value online">● Online</span>
                            </div>
                            <div class="status-item">
                                <span class="status-label">Face Recognition</span>
                                <span class="status-value online">● Active</span>
                            </div>
                            <div class="status-item">
                                <span class="status-label">Voting System</span>
                                <span class="status-value online">● Running</span>
                            </div>
                            <div class="status-item">
                                <span class="status-label">Last Backup</span>
                                <span class="status-value">2 hours ago</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Real-time Update Script -->
        <script>
            function updateCurrentTime() {
                const now = new Date();
                document.getElementById('current-time').textContent = now.toLocaleString();
            }

            function refreshStats() {
                fetch('api/get_stats.php')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('student-count').textContent = data.students;
                        document.getElementById('candidate-count').textContent = data.candidates;
                        document.getElementById('vote-count').textContent = data.votes;
                        document.getElementById('election-status').textContent = data.election_status;

                        // Update progress bar
                        const percentage = data.students > 0 ? Math.min(100, (data.votes / data.students) * 100) : 0;
                        document.getElementById('vote-progress').style.width = percentage + '%';
                        document.getElementById('progress-text').textContent = data.votes + '/' + data.students + ' votes cast';

                        // Add visual feedback
                        const refreshBtn = document.querySelector('.btn-refresh');
                        refreshBtn.innerHTML = '<span class="icon">✓</span> Updated!';
                        setTimeout(() => {
                            refreshBtn.innerHTML = '<span class="icon">🔄</span> Refresh';
                        }, 2000);
                    })
                    .catch(error => console.error('Error fetching stats:', error));
            }

            // Auto-refresh every 30 seconds
            setInterval(refreshStats, 30000);

            // Update time every second
            setInterval(updateCurrentTime, 1000);

            // Initialize
            document.addEventListener('DOMContentLoaded', function() {
                updateCurrentTime();
            });
        </script>
    </div>
</body>
</html>
