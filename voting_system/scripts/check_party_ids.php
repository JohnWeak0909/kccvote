<?php
require_once '../includes/db.php';
$stmt = $pdo->query('SELECT id, name FROM parties');
while($row = $stmt->fetch()) {
    echo $row['id'] . ': ' . $row['name'] . PHP_EOL;
}
?>