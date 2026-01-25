<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isAdmin()) redirect('login.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_election'])) {
        $title = sanitize($_POST['title']);
        $stmt = $pdo->prepare("INSERT INTO elections (title, status) VALUES (?, 'pending')");
        $stmt->execute([$title]);
        $message = "Election added successfully.";
    } elseif (isset($_POST['update_status'])) {
        $id = $_POST['election_id'];
        $status = $_POST['status'];
        $stmt = $pdo->prepare("UPDATE elections SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        $message = "Election status updated.";
    }
}

$elections = $pdo->query("SELECT * FROM elections ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Elections - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
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
                <li><a href="elections.php" class="active">Elections</a></li>
                <li><a href="positions.php">Positions</a></li>
                <li><a href="parties.php">Parties</a></li>
                <li><a href="candidates.php">Candidates</a></li>
                <li><a href="students.php">Students</a></li>
                <li><a href="admins.php">Admins</a></li>
                <li><a href="campus_ips.php">Network Settings</a></li>
                <li><a href="results.php">Results</a></li>
                <li><a href="location_settings.php">Access Control</a></li>
                <li><a href="security_monitor.php">Security Monitor</a></li>
                <li><a href="school_hours.php">School Hours</a></li>
                <li><a href="../public/logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="content">
            <h1>Manage Elections</h1>
            <?php if($message): ?> <div class="alert alert-success"><?php echo $message; ?></div> <?php endif; ?>
            
            <section class="card">
                <h3>Add New Election</h3>
                <form method="POST" class="inline-form">
                    <input type="text" name="title" placeholder="Election Title (e.g. SSC 2026)" required>
                    <button type="submit" name="add_election" class="btn">Add Election</button>
                </form>
            </section>

            <section class="card">
                <h3>Election List</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($elections as $e): ?>
                        <tr>
                            <td><?php echo $e['title']; ?></td>
                            <td><span class="badge <?php echo $e['status']; ?>"><?php echo ucfirst($e['status']); ?></span></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="election_id" value="<?php echo $e['id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="pending" <?php if($e['status']=='pending') echo 'selected'; ?>>Pending</option>
                                        <option value="open" <?php if($e['status']=='open') echo 'selected'; ?>>Open</option>
                                        <option value="closed" <?php if($e['status']=='closed') echo 'selected'; ?>>Closed</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
