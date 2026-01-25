<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isAdmin()) redirect('login.php');

$election = $pdo->query("SELECT * FROM elections ORDER BY id DESC LIMIT 1")->fetch();
$results = [];

if ($election) {
    $stmt = $pdo->prepare("
        SELECT pos.title as position, c.full_name as candidate, p.name as party, COUNT(v.id) as votes
        FROM positions pos
        JOIN candidates c ON pos.id = c.position_id
        LEFT JOIN parties p ON c.party_id = p.id
        LEFT JOIN votes v ON c.id = v.candidate_id AND v.election_id = ?
        GROUP BY pos.id, c.id
        ORDER BY pos.priority DESC, votes DESC
    ");
    $stmt->execute([$election['id']]);
    $results = $stmt->fetchAll(PDO::FETCH_GROUP);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
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
                <li><a href="results.php" class="active">Results</a></li>
                <li><a href="location_settings.php">Access Control</a></li>
                <li><a href="security_monitor.php">Security Monitor</a></li>
                <li><a href="school_hours.php">School Hours</a></li>
                <li><a href="../public/logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="content">
            <header class="dashboard-header">
                <div class="header-content">
                    <h1>Election Results</h1>
                    <p>ssao election</p>
                </div>
                <div class="header-actions">
                    <div class="live-indicator">
                        <span class="pulse"></span> Live Results
                    </div>
                    <button class="btn-refresh" onclick="refreshResults()">
                        <span class="icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span> Refresh
                    </button>
                </div>
            </header>

            <?php if ($results): ?>
                <div class="results-dashboard">
                    <!-- Enhanced Summary Section -->
                    <div class="summary-grid">
                        <div class="summary-card total-votes">
                            <div class="card-icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="card-content">
                                <h3>Total Votes Cast</h3>
                                <div class="vote-count" id="total-votes">
                                    <?php
                                    $total_votes = 0;
                                    foreach($results as $position => $candidates) {
                                        foreach($candidates as $c) {
                                            $total_votes += $c['votes'];
                                        }
                                    }
                                    echo $total_votes;
                                    ?>
                                </div>
                                <p>Across all positions</p>
                            </div>
                            <div class="card-decoration">
                                <div class="decoration-circle"></div>
                            </div>
                        </div>

                        <div class="summary-card positions">
                            <div class="card-icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="card-content">
                                <h3>Election Positions</h3>
                                <div class="vote-count"><?php echo count($results); ?></div>
                                <p>Available positions</p>
                            </div>
                            <div class="card-decoration">
                                <div class="decoration-triangle"></div>
                            </div>
                        </div>

                        <div class="summary-card live-status">
                            <div class="card-icon">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="card-content">
                                <h3>Live Updates</h3>
                                <div class="last-update" id="last-update"><?php echo date('H:i:s'); ?></div>
                                <p>Real-time results</p>
                            </div>
                            <div class="live-pulse">
                                <div class="pulse-dot"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Election Progress Overview -->
                    <div class="progress-overview">
                        <h3>Election Progress Overview</h3>
                        <div class="progress-stats">
                            <div class="stat-item">
                                <span class="stat-label">Participation Rate</span>
                                <span class="stat-value">
                                    <?php
                                    // Assuming we have total registered students, for now using a placeholder
                                    $participation = $total_votes > 0 ? min(100, round(($total_votes / 100) * 100, 1)) : 0;
                                    echo $participation . '%';
                                    ?>
                                </span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Positions Filled</span>
                                <span class="stat-value"><?php echo count($results); ?>/<?php echo count($results); ?></span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Average Votes/Position</span>
                                <span class="stat-value">
                                    <?php
                                    $avg_votes = count($results) > 0 ? round($total_votes / count($results), 1) : 0;
                                    echo $avg_votes;
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Results Container -->
                    <div class="results-container" id="results-container">
                        <?php foreach($results as $position => $candidates):
                            $position_total = array_sum(array_column($candidates, 'votes'));
                            $max_votes = max(array_column($candidates, 'votes'));
                        ?>
                            <div class="result-card">
                                <div class="position-header">
                                    <div class="position-info">
                                        <h3><?php echo $position; ?></h3>
                                        <div class="position-meta">
                                            <span class="candidate-count"><?php echo count($candidates); ?> candidates</span>
                                            <span class="total-votes"><?php echo $position_total; ?> total votes</span>
                                        </div>
                                    </div>
                                    <div class="position-stats">
                                        <div class="stat-circle">
                                            <svg viewBox="0 0 36 36" class="circular-chart">
                                                <path d="m18,2.0845 a 15.9155,15.9155 0 0,1 0,31.831 a 15.9155,15.9155 0 0,1 0,-31.831"
                                                      fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="2"/>
                                                <path d="m18,2.0845 a 15.9155,15.9155 0 0,1 0,31.831 a 15.9155,15.9155 0 0,1 0,-31.831"
                                                      fill="none" stroke="var(--success-color)" stroke-width="2"
                                                      stroke-dasharray="<?php echo $position_total > 0 ? round(($max_votes / $position_total) * 100, 1) : 0; ?>, 100"/>
                                            </svg>
                                            <div class="stat-text">
                                                <span class="stat-number"><?php echo $position_total > 0 ? round(($max_votes / $position_total) * 100, 1) : 0; ?>%</span>
                                                <span class="stat-label">Lead</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="candidates-list">
                                    <?php
                                    usort($candidates, function($a, $b) {
                                        return $b['votes'] <=> $a['votes'];
                                    });
                                    foreach($candidates as $index => $c):
                                        $percentage = $position_total > 0 ? round(($c['votes'] / $position_total) * 100, 1) : 0;
                                        $is_winner = $c['votes'] === $max_votes && $max_votes > 0 && $index === 0;
                                        $rank = $index + 1;
                                    ?>
                                        <div class="candidate-row <?php echo $is_winner ? 'winner' : ''; ?>" data-rank="<?php echo $rank; ?>">
                                            <div class="candidate-rank">
                                                <div class="rank-badge <?php echo $rank <= 3 ? 'top-' . $rank : ''; ?>">
                                                    <?php if($rank == 1): ?>
                                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor"/>
                                                        </svg>
                                                    <?php elseif($rank == 2): ?>
                                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                                            <text x="12" y="16" text-anchor="middle" font-size="12" font-weight="bold" fill="currentColor">2</text>
                                                        </svg>
                                                    <?php elseif($rank == 3): ?>
                                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                                            <text x="12" y="16" text-anchor="middle" font-size="12" font-weight="bold" fill="currentColor">3</text>
                                                        </svg>
                                                    <?php else: ?>
                                                        <span class="rank-number"><?php echo $rank; ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="candidate-avatar">
                                                <div class="avatar-placeholder">
                                                    <?php echo strtoupper(substr($c['candidate'], 0, 1)); ?>
                                                </div>
                                            </div>

                                            <div class="candidate-info">
                                                <div class="candidate-name"><?php echo $c['candidate']; ?></div>
                                                <div class="candidate-party"><?php echo $c['party'] ?: 'Independent'; ?></div>
                                            </div>

                                            <?php if($is_winner): ?>
                                                <div class="winner-crown">
                                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="crown-icon">
                                                        <path d="M5 16L3 5l5.5 4L12 3l3.5 6L21 5l-2 11H5zM19 19H5v2h14v-2z" fill="currentColor"/>
                                                    </svg>
                                                    <span class="winner-text">Winner</span>
                                                </div>
                                            <?php endif; ?>

                                            <div class="vote-metrics">
                                                <div class="vote-count"><?php echo $c['votes']; ?></div>
                                                <div class="vote-percentage"><?php echo $percentage; ?>%</div>
                                            </div>

                                            <div class="progress-visual">
                                                <div class="progress-bar">
                                                    <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                                                </div>
                                                <div class="progress-glow" style="width: <?php echo $percentage; ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <div class="no-results-icon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3>No Election Results Available</h3>
                    <p>Election results will appear here once voting begins.</p>
                </div>
            <?php endif; ?>
        </main>

        <script>
            function refreshResults() {
                fetch('api/get_results.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('total-votes').textContent = data.total_votes;
                            document.getElementById('last-update').textContent = data.timestamp;
                            document.getElementById('results-container').innerHTML = data.html;

                            // Add visual feedback
                            const refreshBtn = document.querySelector('.btn-refresh');
                            refreshBtn.innerHTML = '<span class="icon"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span> Updated!';
                            setTimeout(() => {
                                refreshBtn.innerHTML = '<span class="icon"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span> Refresh';
                            }, 2000);
                        }
                    })
                    .catch(error => console.error('Error fetching results:', error));
            }

            // Auto-refresh every 15 seconds
            setInterval(refreshResults, 15000);

            // Initial load
            document.addEventListener('DOMContentLoaded', function() {
                // Add entrance animations
                const resultCards = document.querySelectorAll('.result-card');
                resultCards.forEach((card, index) => {
                    card.style.animationDelay = `${index * 0.1}s`;
                    card.classList.add('animate-in');
                });
            });
        </script>
    </div>
</body>
</html>
