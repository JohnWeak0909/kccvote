<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isAdmin()) redirect('login.php');

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_hours'])) {
        $school_days = isset($_POST['school_days']) ? implode(',', $_POST['school_days']) : '';
        $start_time = sanitize($_POST['start_time']);
        $end_time = sanitize($_POST['end_time']);
        $timezone = sanitize($_POST['timezone']);

        try {
            // Update or insert school hours configuration
            $stmt = $pdo->prepare("INSERT INTO school_config (config_key, config_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE config_value = ?");
            $stmt->execute(['school_days', $school_days, $school_days]);
            $stmt->execute(['start_time', $start_time, $start_time]);
            $stmt->execute(['end_time', $end_time, $end_time]);
            $stmt->execute(['timezone', $timezone, $timezone]);

            $message = "School hours configuration updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating configuration: " . $e->getMessage();
        }
    }
}

// Get current configuration
$school_days = $pdo->query("SELECT config_value FROM school_config WHERE config_key = 'school_days'")->fetchColumn() ?: 'Monday,Tuesday,Wednesday,Thursday,Friday';
$start_time = $pdo->query("SELECT config_value FROM school_config WHERE config_key = 'start_time'")->fetchColumn() ?: '07:00';
$end_time = $pdo->query("SELECT config_value FROM school_config WHERE config_key = 'end_time'")->fetchColumn() ?: '17:00';
$timezone = $pdo->query("SELECT config_value FROM school_config WHERE config_key = 'timezone'")->fetchColumn() ?: 'Asia/Manila';

$current_school_days = explode(',', $school_days);
$school_hours_active = isSchoolHours();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Hours Configuration - Admin</title>
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
                <li><a href="results.php">Results</a></li>
                <li><a href="location_settings.php">Access Control</a></li>
                <li><a href="security_monitor.php">Security Monitor</a></li>
                <li><a href="school_hours.php" class="active">School Hours</a></li>
                <li><a href="../public/logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="content">
            <header class="page-header">
                <h1>School Hours Configuration</h1>
                <p>Configure when voting is allowed based on school operating hours</p>
            </header>

            <?php if($message): ?> <div class="alert alert-success"><?php echo $message; ?></div> <?php endif; ?>
            <?php if($error): ?> <div class="alert alert-danger"><?php echo $error; ?></div> <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3>Current Status</h3>
                </div>
                <div class="card-body">
                    <div class="status-display">
                        <div class="status-item">
                            <span class="status-label">School Hours Status:</span>
                            <span class="status-value <?php echo $school_hours_active ? 'active' : 'inactive'; ?>">
                                <?php echo $school_hours_active ? '🟢 ACTIVE - Voting Allowed' : '🔴 INACTIVE - Voting Blocked'; ?>
                            </span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Current Time:</span>
                            <span class="status-value"><?php echo date('l, F j, Y - g:i A T'); ?></span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Configured Hours:</span>
                            <span class="status-value"><?php echo date('g:i A', strtotime($start_time)); ?> - <?php echo date('g:i A', strtotime($end_time)); ?> (<?php echo $timezone; ?>)</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">School Days:</span>
                            <span class="status-value"><?php echo str_replace(',', ', ', $school_days); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Configure School Hours</h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>School Days:</label>
                                <div class="checkbox-group">
                                    <?php
                                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                    foreach ($days as $day):
                                    ?>
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="school_days[]" value="<?php echo $day; ?>"
                                                   <?php echo in_array($day, $current_school_days) ? 'checked' : ''; ?>>
                                            <?php echo $day; ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Start Time:</label>
                                <input type="time" name="start_time" value="<?php echo $start_time; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>End Time:</label>
                                <input type="time" name="end_time" value="<?php echo $end_time; ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Timezone:</label>
                                <select name="timezone" required>
                                    <option value="Asia/Manila" <?php echo $timezone == 'Asia/Manila' ? 'selected' : ''; ?>>Asia/Manila (PHT)</option>
                                    <option value="Asia/Singapore" <?php echo $timezone == 'Asia/Singapore' ? 'selected' : ''; ?>>Asia/Singapore (SGT)</option>
                                    <option value="Asia/Tokyo" <?php echo $timezone == 'Asia/Tokyo' ? 'selected' : ''; ?>>Asia/Tokyo (JST)</option>
                                    <option value="America/New_York" <?php echo $timezone == 'America/New_York' ? 'selected' : ''; ?>>America/New_York (EST)</option>
                                    <option value="Europe/London" <?php echo $timezone == 'Europe/London' ? 'selected' : ''; ?>>Europe/London (GMT)</option>
                                    <option value="UTC" <?php echo $timezone == 'UTC' ? 'selected' : ''; ?>>UTC</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" name="update_hours" class="btn">Update School Hours</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>How School Hours Restriction Works</h3>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <h4>🕐 Time-Based Access</h4>
                            <p>Voting is only allowed during configured school hours. Outside these hours, students see a "Voting is only allowed during school hours" message.</p>
                        </div>
                        <div class="info-item">
                            <h4>📅 Day Restrictions</h4>
                            <p>Voting is restricted to school days only. Weekends and holidays are automatically blocked.</p>
                        </div>
                        <div class="info-item">
                            <h4>🌍 Timezone Support</h4>
                            <p>The system uses your configured timezone to determine local school hours, ensuring accuracy across different regions.</p>
                        </div>
                        <div class="info-item">
                            <h4>📊 Activity Logging</h4>
                            <p>All attempts to access voting outside school hours are logged for security monitoring and audit purposes.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <style>
        .status-display { display: flex; flex-direction: column; gap: 15px; }
        .status-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; background: #f8f9fa; border-radius: 6px; }
        .status-label { font-weight: 500; color: #333; }
        .status-value { font-weight: 600; }
        .status-value.active { color: #28a745; }
        .status-value.inactive { color: #dc3545; }

        .form-row { display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap; }
        .form-row .form-group { flex: 1; min-width: 200px; }

        .checkbox-group { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin-top: 10px; }
        .checkbox-label { display: flex; align-items: center; gap: 8px; padding: 8px; background: #f8f9fa; border-radius: 4px; cursor: pointer; }
        .checkbox-label:hover { background: #e9ecef; }
        .checkbox-label input[type="checkbox"] { margin: 0; }

        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .info-item { padding: 20px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff; }
        .info-item h4 { margin: 0 0 10px 0; color: #333; }
        .info-item p { margin: 0; color: #666; line-height: 1.5; }
    </style>
</body>
</html>
