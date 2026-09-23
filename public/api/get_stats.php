<?php

header('Content-Type: application/json; charset=utf-8');

session_start();

$hostname = 'localhost';
$database_default = 'voting_system';
$username = 'root';
$password = '';

if (file_exists(__DIR__ . '/../.env')) {
    $contents = file_get_contents(__DIR__ . '/../.env');
    foreach (preg_split('/\r?\n/', $contents) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        if (! str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, '\"');

        if ($key === 'database.default.hostname') {
            $hostname = $value;
        }
        if ($key === 'database.default.database') {
            $database_default = $value;
        }
        if ($key === 'database.default.username') {
            $username = $value;
        }
        if ($key === 'database.default.password') {
            $password = $value;
        }
    }
}

$mysqli = new mysqli($hostname, $username, $password, $database_default);
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed.']);
    exit;
}

$students = $mysqli->query('SELECT COUNT(*) AS total FROM students')->fetch_assoc()['total'];
$votes = $mysqli->query('SELECT COUNT(*) AS total FROM votes')->fetch_assoc()['total'];
$candidates = $mysqli->query('SELECT COUNT(*) AS total FROM candidates')->fetch_assoc()['total'];
$election_status = 'NONE';
$result = $mysqli->query("SELECT status FROM elections WHERE status = 'open' ORDER BY start_time DESC LIMIT 1");
if ($result && $row = $result->fetch_assoc()) {
    $election_status = strtoupper($row['status']);
} else {
    $result = $mysqli->query('SELECT status FROM elections ORDER BY id DESC LIMIT 1');
    if ($result && $row = $result->fetch_assoc()) {
        $election_status = strtoupper($row['status']);
    }
}

echo json_encode([
    'students' => (int) $students,
    'votes' => (int) $votes,
    'candidates' => (int) $candidates,
    'election_status' => $election_status,
]);
