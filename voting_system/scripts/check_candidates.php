<?php
require_once '../includes/db.php';

$stmt = $pdo->query('SELECT c.id, c.full_name, c.position_id, p.title as position_title FROM candidates c LEFT JOIN positions p ON c.position_id = p.id ORDER BY c.id');
$candidates = $stmt->fetchAll();

echo 'All candidates in database:' . "\n";
foreach ($candidates as $c) {
    echo 'ID: ' . $c['id'] . ', Name: ' . $c['full_name'] . ', Position: ' . ($c['position_title'] ?: 'None') . "\n";
}
?>