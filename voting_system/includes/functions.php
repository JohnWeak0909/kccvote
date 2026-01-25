<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['admin_id']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function isCampusIP($pdo) {
    $client_ip = getClientIP();

    // For development/testing purposes, we can allow localhost
    if ($client_ip == '127.0.0.1' || $client_ip == '::1') return true;

    // Get all campus IPs/networks from database
    $stmt = $pdo->query("SELECT ip_address FROM campus_ips");
    $campus_ips = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Check each IP/network
    foreach ($campus_ips as $network) {
        if (ipInNetwork($client_ip, $network)) {
            return true;
        }
    }

    return false;
}

function ipInNetwork($ip, $network) {
    // Handle CIDR notation (e.g., 192.168.1.0/24)
    if (strpos($network, '/') !== false) {
        list($subnet, $mask) = explode('/', $network);
        $mask = (int)$mask;

        // Convert IPs to long integers
        $ip_long = ip2long($ip);
        $subnet_long = ip2long($subnet);

        if ($ip_long === false || $subnet_long === false) {
            return false;
        }

        // Calculate network mask
        $mask_long = -1 << (32 - $mask);
        $network_long = $subnet_long & $mask_long;

        return ($ip_long & $mask_long) == $network_long;
    } else {
        // Exact IP match
        return $ip === $network;
    }
}

function restrictToCampus($pdo) {
    // Check if location restrictions are disabled (Open Access mode)
    if (file_exists(__DIR__ . '/location_config.php')) {
        include __DIR__ . '/location_config.php';
        if (isset($location_restrictions_enabled) && $location_restrictions_enabled === false) {
            return; // Open Access mode - allow access from anywhere
        }
    }

    // Campus Only mode - now allows any internet connection (WiFi, mobile data, etc.)
    // Physical presence verification relies on face recognition, school hours, and other security measures
    // IP restrictions have been removed to allow flexibility in campus connectivity
    return; // Allow access from any network
}

function validateElectionTime($election) {
    if (!$election) return false;

    $now = new DateTime();
    $start_date = $election['start_date'] ? new DateTime($election['start_date']) : null;
    $end_date = $election['end_date'] ? new DateTime($election['end_date']) : null;

    // If no dates set, allow if status is open
    if (!$start_date && !$end_date) {
        return $election['status'] === 'open';
    }

    // Check if election has started
    if ($start_date && $now < $start_date) {
        return false;
    }

    // Check if election has ended
    if ($end_date && $now > $end_date) {
        return false;
    }

    // Election is within time window and status is open
    return $election['status'] === 'open';
}

function isSchoolHours() {
    // Get configuration from database
    global $pdo;

    try {
        $school_days = $pdo->query("SELECT config_value FROM school_config WHERE config_key = 'school_days'")->fetchColumn() ?: 'Monday,Tuesday,Wednesday,Thursday,Friday';
        $start_time = $pdo->query("SELECT config_value FROM school_config WHERE config_key = 'start_time'")->fetchColumn() ?: '07:00';
        $end_time = $pdo->query("SELECT config_value FROM school_config WHERE config_key = 'end_time'")->fetchColumn() ?: '17:00';
        $timezone = $pdo->query("SELECT config_value FROM school_config WHERE config_key = 'timezone'")->fetchColumn() ?: 'Asia/Manila';

        $school_days_array = explode(',', $school_days);

        $now = new DateTime('now', new DateTimeZone($timezone));
        $current_day = $now->format('l'); // Full day name
        $current_time = $now->format('H:i');

        // Check if it's a school day
        if (!in_array($current_day, $school_days_array)) {
            return false;
        }

        // Check if it's within school hours
        if ($current_time < $start_time || $current_time > $end_time) {
            return false;
        }

        return true;
    } catch (Exception $e) {
        // Fallback to default behavior if database error
        error_log("School hours check failed: " . $e->getMessage());
        return true; // Allow voting if configuration fails
    }
}

function logVotingAttempt($pdo, $student_id, $action, $details = '') {
    // Log all voting attempts for audit purposes
    try {
        $ip = getClientIP();
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $stmt = $pdo->prepare("INSERT INTO voting_logs (student_id, action, ip_address, user_agent, details, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$student_id, $action, $ip, $user_agent, $details]);
    } catch (Exception $e) {
        // Log to file if database logging fails
        error_log("Voting log failed: " . $e->getMessage());
    }
}

function getDeviceFingerprint() {
    // Create a device fingerprint based on browser characteristics
    $fingerprint = [
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'accept_language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
        'screen_resolution' => $_POST['screen_resolution'] ?? '',
        'timezone' => $_POST['timezone'] ?? '',
        'platform' => $_POST['platform'] ?? ''
    ];

    return md5(implode('|', $fingerprint));
}

function validateVotingSession($pdo, $student_id) {
    // Check for suspicious voting patterns
    $recent_votes = $pdo->prepare("SELECT COUNT(*) as vote_count FROM votes WHERE student_id = ? AND voted_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    $recent_votes->execute([$student_id]);
    $count = $recent_votes->fetchColumn();

    if ($count > 0) {
        logVotingAttempt($pdo, $student_id, 'suspicious_multiple_votes', "Multiple votes in short time: $count");
        return false;
    }

    return true;
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>
