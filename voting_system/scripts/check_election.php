<?php
require_once '../includes/db.php';
$stmt = $pdo->query("SELECT * FROM elections WHERE status = 'open' ORDER BY id DESC LIMIT 1");
$election = $stmt->fetch();
if ($election) {
    echo 'Current election: ' . $election['title'] . PHP_EOL;
} else {
    echo 'No active election' . PHP_EOL;
}
?>