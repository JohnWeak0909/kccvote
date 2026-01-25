<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    http_response_code(401);
    exit('Unauthorized');
}

// Store device fingerprint for monitoring
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input) {
        $student_id = $_SESSION['user_id'];
        $fingerprint = json_encode($input);

        try {
            $stmt = $pdo->prepare("INSERT INTO device_fingerprints (student_id, fingerprint, ip_address, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$student_id, $fingerprint, getClientIP()]);
            http_response_code(200);
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
?>