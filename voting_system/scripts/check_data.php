<?php
require_once '../includes/db.php';

$stmt = $pdo->query('SELECT COUNT(*) as count FROM positions');
echo 'Total positions: ' . $stmt->fetch()['count'] . PHP_EOL;

$stmt = $pdo->query('SELECT COUNT(*) as count FROM candidates');
echo 'Total candidates: ' . $stmt->fetch()['count'] . PHP_EOL;

$stmt = $pdo->query('SELECT c.id, c.full_name, c.position_id, p.title as position FROM candidates c LEFT JOIN positions p ON c.position_id = p.id');
$candidates = $stmt->fetchAll();
echo "Candidates:\n";
foreach ($candidates as $c) {
    echo "ID: {$c['id']}, Name: {$c['full_name']}, Position ID: {$c['position_id']}, Position: " . ($c['position'] ?: 'NULL') . "\n";
}
?>