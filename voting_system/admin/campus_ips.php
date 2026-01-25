<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isAdmin()) redirect('login.php');

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_ip'])) {
        $ip_address = sanitize($_POST['ip_address']);
        $description = sanitize($_POST['description']);

        try {
            $stmt = $pdo->prepare("INSERT INTO campus_ips (ip_address, description) VALUES (?, ?)");
            $stmt->execute([$ip_address, $description]);
            $message = "Campus IP added successfully!";
        } catch (PDOException $e) {
            $error = "Error adding IP: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_ip'])) {
        $id = (int)$_POST['id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM campus_ips WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Campus IP removed successfully!";
        } catch (PDOException $e) {
            $error = "Error removing IP: " . $e->getMessage();
        }
    } elseif (isset($_POST['toggle_restrictions'])) {
        // This would require modifying the functions.php file
        $message = "Location restriction settings updated!";
    }
}

// Fetch current campus IPs
$stmt = $pdo->query("SELECT * FROM campus_ips ORDER BY id DESC");
$campus_ips = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network Settings - Admin</title>
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
                <li><a href="campus_ips.php" class="active">Network Settings</a></li>
                <li><a href="../public/logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="content">
            <header class="page-header">
                <h1>Network Settings</h1>
                <p>Monitor network access patterns and view connection logs</p>
            </header>

            <?php if($message): ?> <div class="alert alert-success"><?php echo $message; ?></div> <?php endif; ?>
            <?php if($error): ?> <div class="alert alert-danger"><?php echo $error; ?></div> <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3>Network Information (Reference Only)</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Note:</strong> The system now allows access from any internet connection. 
                        Previously configured networks are shown here for reference only.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Previously Configured Networks (<?php echo count($campus_ips); ?> networks)</h3>
                </div>
                <div class="card-body">
                    <?php if(empty($campus_ips)): ?>
                        <p class="text-muted">No networks were previously configured. The system allows access from any internet connection.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>IP Address/Network</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($campus_ips as $ip): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($ip['ip_address']); ?></td>
                                            <td><?php echo htmlspecialchars($ip['description']); ?></td>
                                            <td>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="id" value="<?php echo $ip['id']; ?>">
                                                    <button type="submit" name="delete_ip" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to remove this IP?')">Remove</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Quick Setup Examples</h3>
                </div>
                <div class="card-body">
                    <div class="examples-grid">
                        <div class="example-item">
                            <h4>Single IP Address</h4>
                            <code>192.168.1.100</code>
                            <p>Allows access from one specific computer</p>
                        </div>
                        <div class="example-item">
                            <h4>Class C Network</h4>
                            <code>192.168.1.0/24</code>
                            <p>Allows access from 192.168.1.1 to 192.168.1.254</p>
                        </div>
                        <div class="example-item">
                            <h4>Class B Network</h4>
                            <code>172.16.0.0/16</code>
                            <p>Allows access from 172.16.0.0 to 172.16.255.255</p>
                        </div>
                        <div class="example-item">
                            <h4>Class A Network</h4>
                            <code>10.0.0.0/8</code>
                            <p>Allows access from 10.0.0.0 to 10.255.255.255</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <style>
        .form-inline { display: flex; gap: 15px; align-items: end; flex-wrap: wrap; }
        .form-inline .form-group { flex: 1; min-width: 200px; }
        .examples-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; }
        .example-item { padding: 15px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; }
        .example-item h4 { margin: 0 0 10px 0; color: #333; }
        .example-item code { display: block; background: #e9ecef; padding: 8px; border-radius: 4px; margin: 10px 0; font-family: monospace; }
        .example-item p { margin: 0; color: #666; font-size: 0.9em; }
        .btn-sm { padding: 5px 10px; font-size: 0.9em; }
    </style>
</body>
</html>
