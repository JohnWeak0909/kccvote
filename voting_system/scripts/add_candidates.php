<?php
require_once '../includes/db.php';

// Add candidates for President position
$pdo->exec("INSERT INTO candidates (full_name, party_id, position_id, photo_url) VALUES ('John Doe', 1, 1, '')");
$pdo->exec("INSERT INTO candidates (full_name, party_id, position_id, photo_url) VALUES ('Jane Smith', 2, 1, '')");

// Add candidates for Vice President position
$pdo->exec("INSERT INTO candidates (full_name, party_id, position_id, photo_url) VALUES ('Bob Johnson', 1, 2, '')");
$pdo->exec("INSERT INTO candidates (full_name, party_id, position_id, photo_url) VALUES ('Alice Brown', 3, 2, '')");

echo 'Added sample candidates for pres and vp positions' . PHP_EOL;
?>