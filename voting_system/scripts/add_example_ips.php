<?php
require_once '../includes/db.php';

echo "Adding example campus IP ranges...\n";

$example_ips = [
    ['192.168.1.0/24', 'School WiFi Network (Example)'],
    ['10.0.0.0/8', 'Campus LAN Network (Example)'],
    ['172.16.0.0/16', 'School VPN Network (Example)']
];

try {
    $stmt = $pdo->prepare("INSERT IGNORE INTO campus_ips (ip_address, description) VALUES (?, ?)");

    foreach ($example_ips as $ip) {
        $stmt->execute($ip);
        echo "Added: {$ip[0]} - {$ip[1]}\n";
    }

    echo "\nExample IP ranges added successfully!\n";
    echo "You can now access the voting system from these networks.\n";
    echo "Modify or remove these IPs in the admin panel: admin/campus_ips.php\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>