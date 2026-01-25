<?php
require_once '../includes/db.php';

$stmt = $pdo->query('SELECT COUNT(*) FROM candidates');
echo 'Total candidates: ' . $stmt->fetchColumn() . "\n";

$stmt = $pdo->query('SELECT COUNT(*) FROM positions');
echo 'Total positions: ' . $stmt->fetchColumn() . "\n";

$stmt = $pdo->query('SELECT COUNT(*) FROM parties');
echo 'Total parties: ' . $stmt->fetchColumn() . "\n";

$stmt = $pdo->query('SELECT p.title, COUNT(c.id) as candidate_count FROM positions p LEFT JOIN candidates c ON p.id = c.position_id GROUP BY p.id, p.title ORDER BY p.priority DESC');
$results = $stmt->fetchAll();

echo "\n" . 'Position breakdown:' . "\n";
foreach ($results as $row) {
    echo $row['title'] . ': ' . $row['candidate_count'] . ' candidates' . "\n";
}
?>