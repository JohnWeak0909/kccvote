<?php
header('Content-Type: application/json');
require_once '../../includes/db.php';

try {
    $election = $pdo->query("SELECT * FROM elections ORDER BY id DESC LIMIT 1")->fetch();

    if (!$election) {
        echo json_encode(['success' => false, 'message' => 'No election found']);
        exit;
    }

    // Get results
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
    $raw_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group by position
    $results = [];
    foreach ($raw_results as $row) {
        $results[$row['position']][] = $row;
    }

    // Calculate total votes
    $total_votes = 0;
    foreach ($raw_results as $row) {
        $total_votes += $row['votes'];
    }

    // Generate HTML
    ob_start();
    foreach($results as $position => $candidates):
        $position_total = array_sum(array_column($candidates, 'votes'));
        $max_votes = max(array_column($candidates, 'votes'));
    ?>
        <div class="result-card animate-in">
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
    <?php endforeach;

    $html = ob_get_clean();

    echo json_encode([
        'success' => true,
        'total_votes' => $total_votes,
        'html' => $html,
        'timestamp' => date('H:i:s'),
        'election_title' => $election['title']
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>