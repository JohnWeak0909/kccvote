<?php
require_once '../includes/db.php';
$stmt = $pdo->query('SELECT name FROM parties');
while($row = $stmt->fetch()) {
    echo $row['name'] . PHP_EOL;
}
?>