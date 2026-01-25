<?php
require_once '../includes/db.php';

try {
    // Get student IDs
    $stmt = $pdo->query('SELECT id FROM students ORDER BY id LIMIT 5');
    $student_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Set election status to open
    $stmt = $pdo->prepare("UPDATE elections SET status = 'open' WHERE id = (SELECT id FROM elections ORDER BY id DESC LIMIT 1)");
    $stmt->execute();

    // Add some sample votes - correct position IDs: pres=1, vp=2, sec=4
    $votes = [
        [$student_ids[0], 1, 1, 1], // student 1 votes for candidate 1 (pres)
        [$student_ids[1], 2, 1, 1], // student 2 votes for candidate 2 (pres)
        [$student_ids[2], 1, 1, 1], // student 3 votes for candidate 1 (pres)
        [$student_ids[3], 3, 2, 1], // student 4 votes for candidate 3 (vp)
        [$student_ids[4], 4, 2, 1], // student 5 votes for candidate 4 (vp)
        [$student_ids[0], 5, 4, 1], // student 1 votes for candidate 5 (sec)
        [$student_ids[1], 6, 4, 1], // student 2 votes for candidate 6 (sec)
    ];

    foreach ($votes as $vote) {
        try {
            $stmt = $pdo->prepare("INSERT INTO votes (student_id, candidate_id, position_id, election_id) VALUES (?, ?, ?, ?)");
            $stmt->execute($vote);
            echo "Vote added successfully\n";
        } catch (Exception $e) {
            echo "Failed to add vote: " . $e->getMessage() . "\n";
        }
    }

    echo "Sample votes added!\n";

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>