<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['descriptor'])) {
    if (!isset($_SESSION['temp_user_id']) || !isset($_SESSION['temp_face_descriptor'])) {
        echo json_encode(['error' => 'Session expired. Please login again.']);
        exit;
    }

    $descriptor = json_decode($_POST['descriptor'], true);
    if (!$descriptor) {
        echo json_encode(['error' => 'Invalid descriptor']);
        exit;
    }

    $storedDescriptor = json_decode($_SESSION['temp_face_descriptor'], true);
    if (!$storedDescriptor) {
        echo json_encode(['error' => 'No face registered for this user']);
        exit;
    }

    $distance = euclideanDistance($descriptor, $storedDescriptor);
    $threshold = 0.6; // Adjust threshold as needed

    if ($distance < $threshold) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Face verification failed']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

function euclideanDistance($a, $b) {
    $sum = 0;
    for ($i = 0; $i < count($a); $i++) {
        $sum += pow($a[$i] - $b[$i], 2);
    }
    return sqrt($sum);
}
?>