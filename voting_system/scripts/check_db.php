<?php
require_once '../includes/db.php';

try {
    // Check students
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM students');
    $result = $stmt->fetch();
    echo 'Students: ' . $result['count'] . PHP_EOL;

    // Check election details
    $stmt = $pdo->query("SELECT * FROM elections ORDER BY id DESC LIMIT 1");
    $election = $stmt->fetch();
    if ($election) {
        echo 'Latest Election: ' . $election['title'] . ' (Status: ' . $election['status'] . ')' . PHP_EOL;
    }

    // Check votes
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM votes');
    $result = $stmt->fetch();
    echo 'Total Votes: ' . $result['count'] . PHP_EOL;

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>