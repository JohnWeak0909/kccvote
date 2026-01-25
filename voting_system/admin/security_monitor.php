<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isAdmin()) redirect('login.php');

// Get security statistics
$today_votes = $pdo->query("SELECT COUNT(*) FROM votes WHERE DATE(voted_at) = CURDATE()")->fetchColumn();
$suspicious_activities = $pdo->query("SELECT COUNT(*) FROM voting_logs WHERE action LIKE '%suspicious%' AND DATE(created_at) = CURDATE()")->fetchColumn();
$outside_hours_attempts = $pdo->query("SELECT COUNT(*) FROM voting_logs WHERE action = 'access_outside_school_hours' AND DATE(created_at) = CURDATE()")->fetchColumn();

// Get recent voting logs
$recent_logs = $pdo->query("SELECT vl.*, s.full_name, s.student_id as student_number FROM voting_logs vl JOIN students s ON vl.student_id = s.id ORDER BY vl.created_at DESC LIMIT 20")->fetchAll();

// Get current school hours status
$school_hours_active = isSchoolHours();
$current_time = date('H:i');
$current_day = date('l');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Monitoring - Admin</title>
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
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="elections.php">Elections</a></li>
                <li><a href="positions.php">Positions</a></li>
                <li><a href="parties.php">Parties</a></li>
                <li><a href="candidates.php">Candidates</a></li>
                <li><a href="students.php">Students</a></li>
                <li><a href="admins.php">Admins</a></li>
                <li><a href="campus_ips.php">Network Settings</a></li>
                <li><a href="results.php">Results</a></li>
                <li><a href="location_settings.php">Access Control</a></li>
                <li><a href="security_monitor.php" class="active">Security Monitor</a></li>
                <li><a href="school_hours.php">School Hours</a></li>
                <li><a href="../public/logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="content">
            <header class="page-header">
                <h1>Security Monitoring Dashboard</h1>
                <p>Monitor voting security and ensure campus-only access</p>
            </header>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">🗳️</div>
                    <div class="stat-content">
                        <h3><?php echo $today_votes; ?></h3>
                        <p>Votes Today</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">⚠️</div>
                    <div class="stat-content">
                        <h3><?php echo $suspicious_activities; ?></h3>
                        <p>Suspicious Activities</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">🚫</div>
                    <div class="stat-content">
                        <h3><?php echo $outside_hours_attempts; ?></h3>
                        <p>Outside Hours Attempts</p>
                    </div>
                </div>

                <div class="stat-card <?php echo $school_hours_active ? 'active' : 'inactive'; ?>">
                    <div class="stat-icon">🕐</div>
                    <div class="stat-content">
                        <h3><?php echo $school_hours_active ? 'Active' : 'Inactive'; ?></h3>
                        <p>School Hours Status</p>
                        <small><?php echo $current_day . ' ' . $current_time; ?></small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Recent Security Events</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Student</th>
                                    <th>Action</th>
                                    <th>IP Address</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_logs as $log): ?>
                                    <tr class="<?php echo strpos($log['action'], 'suspicious') !== false || strpos($log['action'], 'blocked') !== false ? 'danger' : 'info'; ?>">
                                        <td><?php echo date('H:i:s', strtotime($log['created_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($log['full_name']); ?><br><small><?php echo htmlspecialchars($log['student_number']); ?></small></td>
                                        <td>
                                            <span class="action-badge <?php echo $log['action']; ?>">
                                                <?php echo ucwords(str_replace('_', ' ', $log['action'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                        <td><?php echo htmlspecialchars($log['details']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Security Verification Layers</h3>
                </div>
                <div class="card-body">
                    <div class="verification-layers">
                        <div class="layer inactive">
                            <div class="layer-icon">🌐</div>
                            <div class="layer-content">
                                <h4>Network Access Control</h4>
                                <p>Allows access from any internet connection (WiFi, mobile data)</p>
                                <span class="status-badge inactive">Open Access</span>
                            </div>
                        </div>

                        <div class="layer active">
                            <div class="layer-icon">🕐</div>
                            <div class="layer-content">
                                <h4>School Hours Restriction</h4>
                                <p>Limits voting to school operating hours (7 AM - 5 PM, weekdays)</p>
                                <span class="status-badge <?php echo $school_hours_active ? 'active' : 'inactive'; ?>">
                                    <?php echo $school_hours_active ? 'Active' : 'Inactive'; ?>
                                </span>
                            </div>
                        </div>

                        <div class="layer active">
                            <div class="layer-icon">👤</div>
                            <div class="layer-content">
                                <h4>Face Recognition</h4>
                                <p>Biometric verification ensures the registered student is voting</p>
                                <span class="status-badge active">Active</span>
                            </div>
                        </div>

                        <div class="layer active">
                            <div class="layer-icon">📊</div>
                            <div class="layer-content">
                                <h4>Election Time Windows</h4>
                                <p>Voting only allowed during configured election periods</p>
                                <span class="status-badge active">Active</span>
                            </div>
                        </div>

                        <div class="layer active">
                            <div class="layer-icon">🔍</div>
                            <div class="layer-content">
                                <h4>Activity Monitoring</h4>
                                <p>Logs all voting attempts and detects suspicious patterns</p>
                                <span class="status-badge active">Active</span>
                            </div>
                        </div>

                        <div class="layer active">
                            <div class="layer-icon">🖥️</div>
                            <div class="layer-content">
                                <h4>Device Fingerprinting</h4>
                                <p>Tracks device characteristics for additional verification</p>
                                <span class="status-badge active">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; align-items: center; }
        .stat-icon { font-size: 2em; margin-right: 15px; }
        .stat-content h3 { margin: 0; font-size: 2em; font-weight: 600; color: #333; }
        .stat-content p { margin: 5px 0 0 0; color: #666; }
        .stat-card.active { border-left: 4px solid #4caf50; }
        .stat-card.inactive { border-left: 4px solid #ff9800; }

        .action-badge { padding: 3px 8px; border-radius: 12px; font-size: 0.8em; font-weight: 500; }
        .action-badge.vote_completed { background: #d4edda; color: #155724; }
        .action-badge.blocked_outside_hours { background: #f8d7da; color: #721c24; }
        .action-badge.suspicious_multiple_votes { background: #fff3cd; color: #856404; }

        .verification-layers { display: flex; flex-direction: column; gap: 15px; }
        .layer { display: flex; align-items: center; padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff; }
        .layer-icon { font-size: 1.5em; margin-right: 15px; }
        .layer-content { flex: 1; }
        .layer-content h4 { margin: 0 0 5px 0; color: #333; }
        .layer-content p { margin: 0; color: #666; font-size: 0.9em; }
        .status-badge { float: right; padding: 3px 8px; border-radius: 12px; font-size: 0.8em; font-weight: 500; }
        .status-badge.active { background: #d4edda; color: #155724; }
        .status-badge.inactive { background: #fff3cd; color: #856404; }
    </style>
</body>
</html>
