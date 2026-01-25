<?php
require_once '../includes/db.php';
$pdo->exec('DELETE FROM votes');
echo 'Votes table cleared\n';
?>