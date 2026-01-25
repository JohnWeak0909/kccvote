<?php
header('Content-Type: application/json');
require_once '../../includes/db.php';

try {
    // Get real-time stats
    $student_count = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $candidate_count = $pdo->query("SELECT COUNT(*) FROM candidates")->fetchColumn();
    $vote_count = $pdo->query("SELECT COUNT(*) FROM votes")->fetchColumn();

    // Get current election
    $election = $pdo->query("SELECT * FROM elections WHERE status = 'open' ORDER BY id DESC LIMIT 1")->fetch();
    $election_status = $election ? ucfirst($election['status']) : 'No Active Election';

    // Get recent votes (last 10)
    $recent_votes = $pdo->query("
        SELECT v.created_at, s.full_name as student_name, c.name as candidate_name, p.title as position_title
        FROM votes v
        JOIN students s ON v.student_id = s.id
        JOIN candidates c ON v.candidate_id = c.id
        JOIN positions p ON v.position_id = p.id
        ORDER BY v.created_at DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'students' => $student_count,
        'candidates' => $candidate_count,
        'votes' => $vote_count,
        'election_status' => $election_status,
        'recent_votes' => $recent_votes,
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>