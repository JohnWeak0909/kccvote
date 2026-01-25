<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isAdmin()) redirect('login.php');

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['set_open_access'])) {
        // Create a config file to disable location restrictions
        $config_content = "<?php\n// Location restrictions disabled - Open Access Mode\n\$location_restrictions_enabled = false;\n\$access_mode = 'open';\n?>";
        if (file_put_contents('../includes/location_config.php', $config_content)) {
            $message = "✅ Access mode changed to OPEN ACCESS. Students can now vote from anywhere using any network.";
        } else {
            $error = "Failed to update configuration.";
        }
    } elseif (isset($_POST['set_campus_only'])) {
        // Remove the config file to re-enable restrictions
        if (file_exists('../includes/location_config.php')) {
            unlink('../includes/location_config.php');
        }
        $message = "✅ Access mode changed to CAMPUS ONLY. Students can vote using any internet connection (WiFi, mobile data, etc.) with enhanced security verification.";
    }
}

// Check current access mode
$access_mode = 'campus_only'; // Default
$restrictions_disabled = false; // Default - restrictions enabled
if (file_exists('../includes/location_config.php')) {
    include '../includes/location_config.php';
    $access_mode = isset($access_mode) ? $access_mode : 'open';
    $restrictions_disabled = isset($location_restrictions_enabled) ? !$location_restrictions_enabled : true;
}

// Get campus IP count
$campus_ip_count = $pdo->query("SELECT COUNT(*) FROM campus_ips")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Control Settings - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/main.js" defer></script>
</head>
<body>
    <div class="admin-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/images/kcclogo.jpg" alt="KCC Online Voting Logo" class="sidebar-logo">
                <h2>KCC Online Voting</h2>
            </div>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="elections.php">Elections</a></li>
                <li><a href="positions.php">Positions</a></li>
                <li><a href="parties.php">Parties</a></li>
                <li><a href="candidates.php">Candidates</a></li>
                <li><a href="students.php">Students</a></li>
                <li><a href="admins.php">Admins</a></li>
                <li><a href="results.php">Results</a></li>
                <li><a href="campus_ips.php">Network Settings</a></li>
                <li><a href="location_settings.php" class="active">Access Control</a></li>
                <li><a href="security_monitor.php">Security Monitor</a></li>
                <li><a href="school_hours.php">School Hours</a></li>
                <li><a href="../public/logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="content">
            <header class="page-header">
                <h1>Access Control Settings</h1>
                <p>Choose how students can access the voting system</p>
            </header>

            <?php if($message): ?> <div class="alert alert-success"><?php echo $message; ?></div> <?php endif; ?>
            <?php if($error): ?> <div class="alert alert-danger"><?php echo $error; ?></div> <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3>Current Access Mode</h3>
                </div>
                <div class="card-body">
                    <div class="current-mode">
                        <?php if($access_mode === 'open'): ?>
                            <div class="mode-indicator open">
                                <div class="mode-icon">🌐</div>
                                <div class="mode-details">
                                    <h4>OPEN ACCESS MODE</h4>
                                    <p>Students can vote from <strong>anywhere</strong> using any network (WiFi, mobile data, home internet)</p>
                                    <div class="mode-status">🟢 ACTIVE</div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mode-indicator campus">
                                <div class="mode-icon">🏫</div>
                                <div class="mode-details">
                                    <h4>CAMPUS ONLY MODE</h4>
                                    <p>Students can vote using <strong>any internet connection</strong> (WiFi, mobile data, etc.)</p>
                                    <div class="mode-status">🔒 ACTIVE</div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Choose Access Mode</h3>
                </div>
                <div class="card-body">
                    <div class="access-modes">
                        <div class="mode-option <?php echo $access_mode === 'open' ? 'selected' : ''; ?>">
                            <div class="mode-header">
                                <div class="mode-icon-large">🌐</div>
                                <h4>Open Access</h4>
                                <div class="mode-badge">Flexible</div>
                            </div>
                            <div class="mode-description">
                                <p>Allow students to vote from <strong>any location</strong> and <strong>any network</strong>:</p>
                                <ul>
                                    <li>✅ Home WiFi networks</li>
                                    <li>✅ Mobile data (4G/5G)</li>
                                    <li>✅ Public WiFi hotspots</li>
                                    <li>✅ Any internet connection</li>
                                    <li>✅ Remote learning scenarios</li>
                                </ul>
                                <div class="security-note">
                                    <strong>Security Note:</strong> Face recognition and other verification methods remain active for identity protection.
                                </div>
                            </div>
                            <form method="POST" style="margin-top: 20px;">
                                <button type="submit" name="set_open_access" class="btn btn-primary"
                                        <?php echo $access_mode === 'open' ? 'disabled' : ''; ?>>
                                    <?php echo $access_mode === 'open' ? '✓ Currently Active' : 'Switch to Open Access'; ?>
                                </button>
                            </form>
                        </div>

                        <div class="mode-option <?php echo $access_mode === 'campus_only' ? 'selected' : ''; ?>">
                            <div class="mode-header">
                                <div class="mode-icon-large">🏫</div>
                                <h4>Campus Only</h4>
                                <div class="mode-badge">Secure</div>
                            </div>
                            <div class="mode-description">
                                <p>Allow voting from <strong>any internet connection</strong> with enhanced security:</p>
                                <ul>
                                    <li>✅ Any WiFi network (school, home, public)</li>
                                    <li>✅ Mobile data (4G, 5G, LTE)</li>
                                    <li>✅ Face recognition verification</li>
                                    <li>✅ School hours restrictions</li>
                                    <li>✅ Device fingerprinting</li>
                                    <li>✅ Activity monitoring</li>
                                </ul>
                                <div class="campus-status">
                                    <strong>Security Features Active:</strong> Face recognition, school hours, device monitoring
                                </div>
                            </div>
                            <form method="POST" style="margin-top: 20px;">
                                <button type="submit" name="set_campus_only" class="btn btn-secondary"
                                        <?php echo $access_mode === 'campus_only' ? 'disabled' : ''; ?>>
                                    <?php echo $access_mode === 'campus_only' ? '✓ Currently Active' : 'Switch to Campus Only'; ?>
                                </button>
                            </form>
                            <?php if($campus_ip_count == 0): ?>
                                <div class="config-link">
                                    <a href="campus_ips.php" class="btn btn-sm">Configure Campus Networks →</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Security Comparison</h3>
                </div>
                <div class="card-body">
                    <div class="comparison-table">
                        <div class="comparison-row">
                            <div class="comparison-label">Physical Presence Verification</div>
                            <div class="comparison-value open">❌ Not guaranteed</div>
                            <div class="comparison-value campus">✅ Guaranteed</div>
                        </div>
                        <div class="comparison-row">
                            <div class="comparison-label">Network Security</div>
                            <div class="comparison-value open">⚠️ Lower</div>
                            <div class="comparison-value campus">🛡️ Higher</div>
                        </div>
                        <div class="comparison-row">
                            <div class="comparison-label">Accessibility</div>
                            <div class="comparison-value open">🌐 Maximum</div>
                            <div class="comparison-value campus">🏫 Limited</div>
                        </div>
                        <div class="comparison-row">
                            <div class="comparison-label">Face Recognition</div>
                            <div class="comparison-value open">✅ Active</div>
                            <div class="comparison-value campus">✅ Active</div>
                        </div>
                        <div class="comparison-row">
                            <div class="comparison-label">School Hours Check</div>
                            <div class="comparison-value open">✅ Active</div>
                            <div class="comparison-value campus">✅ Active</div>
                        </div>
                        <div class="comparison-row">
                            <div class="comparison-label">Activity Monitoring</div>
                            <div class="comparison-value open">✅ Active</div>
                            <div class="comparison-value campus">✅ Active</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="campus_ips.php" class="action-card">
                            <div class="action-icon">🌐</div>
                            <div class="action-content">
                                <h4>Network Monitoring</h4>
                                <p>View network access patterns and security logs</p>
                            </div>
                        </a>
                        <a href="school_hours.php" class="action-card">
                            <div class="action-icon">🕐</div>
                            <div class="action-content">
                                <h4>School Hours Settings</h4>
                                <p>Configure when voting is allowed</p>
                            </div>
                        </a>
                        <a href="security_monitor.php" class="action-card">
                            <div class="action-icon">🔍</div>
                            <div class="action-content">
                                <h4>Security Monitor</h4>
                                <p>View real-time security events and statistics</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <style>
        .current-mode { margin: 20px 0; }
        .mode-indicator { display: flex; align-items: center; padding: 20px; border-radius: 8px; border: 2px solid; }
        .mode-indicator.open { background: #e8f5e8; border-color: #4caf50; }
        .mode-indicator.campus { background: #fff3cd; border-color: #ffc107; }
        .mode-icon { font-size: 3em; margin-right: 20px; }
        .mode-details h4 { margin: 0 0 10px 0; font-size: 1.2em; }
        .mode-details p { margin: 10px 0; }
        .mode-status { font-weight: bold; font-size: 1.1em; }

        .access-modes { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px; }
        .mode-option { border: 2px solid #e9ecef; border-radius: 12px; padding: 25px; transition: all 0.3s ease; }
        .mode-option.selected { border-color: #007bff; background: #f8f9ff; }
        .mode-header { text-align: center; margin-bottom: 20px; }
        .mode-icon-large { font-size: 4em; margin-bottom: 10px; }
        .mode-header h4 { margin: 10px 0; font-size: 1.3em; }
        .mode-badge { display: inline-block; padding: 4px 12px; background: #007bff; color: white; border-radius: 20px; font-size: 0.8em; font-weight: 500; }
        .mode-description ul { margin: 15px 0; padding-left: 20px; }
        .mode-description li { margin: 5px 0; }
        .security-note { background: #e3f2fd; padding: 10px; border-radius: 6px; margin-top: 15px; font-size: 0.9em; }
        .campus-status { background: #f8f9fa; padding: 10px; border-radius: 6px; margin-top: 15px; font-size: 0.9em; }
        .config-link { text-align: center; margin-top: 15px; }

        .comparison-table { display: flex; flex-direction: column; gap: 10px; }
        .comparison-row { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 15px; padding: 12px; background: #f8f9fa; border-radius: 6px; align-items: center; }
        .comparison-label { font-weight: 500; }
        .comparison-value { text-align: center; font-weight: 600; }
        .comparison-value.open { color: #28a745; }
        .comparison-value.campus { color: #dc3545; }

        .quick-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .action-card { display: flex; align-items: center; padding: 20px; background: #f8f9fa; border-radius: 8px; text-decoration: none; color: inherit; transition: all 0.3s ease; }
        .action-card:hover { background: #e9ecef; transform: translateY(-2px); }
        .action-icon { font-size: 2em; margin-right: 15px; }
        .action-content h4 { margin: 0 0 5px 0; color: #333; }
        .action-content p { margin: 0; color: #666; font-size: 0.9em; }

        .btn-primary { background: #007bff; color: white; border: 1px solid #007bff; }
        .btn-primary:hover:not(:disabled) { background: #0056b3; }
        .btn-secondary { background: #6c757d; color: white; border: 1px solid #6c757d; }
        .btn-secondary:hover:not(:disabled) { background: #545b62; }
        .btn:disabled { opacity: 0.6; cursor: not-allowed; }
        .btn-sm { padding: 8px 16px; font-size: 0.9em; }
    </style>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Settings - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/main.js" defer></script>
</head>
<body>
    <div class="admin-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/images/kcclogo.jpg" alt="KCC Online Voting Logo" class="sidebar-logo">
                <h2>KCC Online Voting</h2>
            </div>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="elections.php">Elections</a></li>
                <li><a href="positions.php">Positions</a></li>
                <li><a href="parties.php">Parties</a></li>
                <li><a href="candidates.php">Candidates</a></li>
                <li><a href="students.php">Students</a></li>
                <li><a href="admins.php">Admins</a></li>
                <li><a href="campus_ips.php">Network Settings</a></li>
                <li><a href="results.php">Results</a></li>
                <li><a href="location_settings.php" class="active">Location Settings</a></li>
                <li><a href="../public/logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="content">
            <header class="page-header">
                <h1>Location Settings</h1>
                <p>Configure location-based access restrictions for the voting system</p>
            </header>

            <?php if($message): ?> <div class="alert alert-success"><?php echo $message; ?></div> <?php endif; ?>
            <?php if($error): ?> <div class="alert alert-danger"><?php echo $error; ?></div> <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3>Current Location Restriction Status</h3>
                </div>
                <div class="card-body">
                    <div class="status-indicator">
                        <?php if($restrictions_disabled): ?>
                            <div class="status status-disabled">
                                <h4>🌐 Open Access Mode</h4>
                                <p>Voting is allowed from <strong>any internet connection</strong> with standard security features.</p>
                                <p>Students can vote from any WiFi or mobile network.</p>
                            </div>
                        <?php else: ?>
                            <div class="status status-enabled">
                                <h4>🔒 Campus Only Mode Active</h4>
                                <p>Voting is allowed from <strong>any internet connection</strong> with enhanced security verification.</p>
                                <p>Face recognition, school hours, and device monitoring are active.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Location Restriction Options</h3>
                </div>
                <div class="card-body">
                    <div class="option-grid">
                        <div class="option-card">
                            <h4>Campus Only Mode</h4>
                            <p>Allow voting from any internet connection with enhanced security verification</p>
                            <ul>
                                <li>✅ Any WiFi or mobile network</li>
                                <li>✅ Face recognition required</li>
                                <li>✅ School hours enforced</li>
                                <li>✅ Device monitoring active</li>
                            </ul>
                            <form method="POST" style="margin-top: 15px;">
                                <button type="submit" name="set_campus_only" class="btn"
                                        <?php echo $restrictions_disabled ? '' : 'disabled'; ?>>
                                    <?php echo $restrictions_disabled ? 'Switch to Campus Only' : '✓ Currently Active'; ?>
                                </button>
                            </form>
                            <p style="margin-top: 10px; font-size: 0.9em; color: #666;">
                                <a href="security_monitor.php">View security logs</a>
                            </p>
                        </div>

                        <div class="option-card">
                            <h4>Open Access (Anywhere)</h4>
                            <p>Allow voting from any internet connection (recommended for remote learning)</p>
                            <ul>
                                <li>✅ Maximum accessibility</li>
                                <li>✅ Works with mobile networks</li>
                                <li>✅ No network configuration needed</li>
                                <li>❌ Lower security</li>
                            </ul>
                            <form method="POST" style="margin-top: 15px;">
                                <button type="submit" name="set_open_access" class="btn btn-warning"
                                        onclick="return confirm('Switch to Open Access mode?')"
                                        <?php echo $restrictions_disabled ? 'disabled' : ''; ?>>
                                    <?php echo $restrictions_disabled ? '✓ Currently Active' : 'Switch to Open Access'; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Security Considerations</h3>
                </div>
                <div class="card-body">
                    <div class="security-tips">
                        <div class="tip">
                            <h4>🔐 With Restrictions Enabled:</h4>
                            <ul>
                                <li>Use face recognition for additional security</li>
                                <li>Monitor voting logs for suspicious activity</li>
                                <li>Consider VPN access for remote students if needed</li>
                            </ul>
                        </div>
                        <div class="tip">
                            <h4>🌐 With Restrictions Disabled:</h4>
                            <ul>
                                <li>Enable face recognition for user verification</li>
                                <li>Use strong passwords and account security</li>
                                <li>Monitor for voting irregularities</li>
                                <li>Consider time-based voting windows</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <style>
        .status-indicator { margin: 20px 0; }
        .status { padding: 20px; border-radius: 8px; border-left: 4px solid; }
        .status-disabled { background: #e8f5e8; border-left-color: #4caf50; }
        .status-enabled { background: #fff3cd; border-left-color: #ffc107; }
        .status h4 { margin: 0 0 10px 0; font-size: 1.2em; }
        .status p { margin: 5px 0; }

        .option-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; }
        .option-card { border: 2px solid #e9ecef; border-radius: 8px; padding: 20px; }
        .option-card h4 { color: #333; margin-bottom: 10px; }
        .option-card ul { margin: 15px 0; padding-left: 20px; }
        .option-card li { margin: 5px 0; }

        .security-tips { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .tip { background: #f8f9fa; padding: 15px; border-radius: 8px; }
        .tip h4 { margin: 0 0 10px 0; color: #495057; }
        .tip ul { margin: 0; padding-left: 20px; }
        .tip li { margin: 3px 0; font-size: 0.9em; }

        .btn-warning { background: #ffc107; color: #212529; border: 1px solid #ffc107; }
        .btn-warning:hover { background: #e0a800; border-color: #d39e00; }
    </style>
</body>
</html>
