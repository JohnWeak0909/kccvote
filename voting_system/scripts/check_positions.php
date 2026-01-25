<?php
require_once '../includes/db.php';

$stmt = $pdo->query('SELECT id, title, priority FROM positions ORDER BY priority DESC');
$positions = $stmt->fetchAll();

echo 'Positions in database:' . "\n";
foreach ($positions as $pos) {
    echo 'ID: ' . $pos['id'] . ', Title: ' . $pos['title'] . ', Priority: ' . $pos['priority'] . "\n";
    
    $stmt2 = $pdo->prepare('SELECT c.full_name FROM candidates c WHERE c.position_id = ? ORDER BY c.full_name');
    $stmt2->execute([$pos['id']]);
    $candidates = $stmt2->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($candidates)) {
        echo '  -> Candidates: ' . implode(', ', $candidates) . "\n";
    } else {
        echo '  -> Candidates: None' . "\n";
    }
}

echo "\n" . 'Current election:' . "\n";
$stmt = $pdo->query("SELECT * FROM elections WHERE status = 'open' ORDER BY id DESC LIMIT 1");
$election = $stmt->fetch();
if ($election) {
    echo 'Election: ' . $election['title'] . ' (ID: ' . $election['id'] . ')' . "\n";
} else {
    echo 'No active election found' . "\n";
}

echo "\n" . 'Positions that will appear in voting (all positions, even without candidates):' . "\n";
$positions_with_candidates = [];
$positions_without_candidates = [];
foreach ($positions as $pos) {
    $stmt2 = $pdo->prepare('SELECT COUNT(*) FROM candidates WHERE position_id = ?');
    $stmt2->execute([$pos['id']]);
    $candidate_count = $stmt2->fetchColumn();
    
    if ($candidate_count > 0) {
        $positions_with_candidates[] = $pos['title'] . ' (' . $candidate_count . ' candidates)';
    } else {
        $positions_without_candidates[] = $pos['title'] . ' (no candidates)';
    }
}

echo 'With candidates: ' . implode(', ', $positions_with_candidates) . "\n";
echo 'Without candidates: ' . implode(', ', $positions_without_candidates) . "\n";
?>