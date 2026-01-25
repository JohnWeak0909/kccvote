<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (isAdmin()) redirect('index.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        redirect('index.php');
    } else {
        $error = "Invalid admin credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - KCC Online Voting</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/main.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="form-wrapper">
            <div class="logo-container">
                <div class="logo-wrapper">
                    <img src="../assets/images/kcclogo.jpg" alt="KCC Online Voting Logo" class="logo">
                    <div class="logo-glow"></div>
                </div>
                <h1 class="main-title">KCC Online Voting</h1>
                <p class="subtitle">Admin Portal</p>
            </div>
            <h2>Admin Login</h2>
            <?php if($error): ?> <div class="alert alert-danger"><?php echo $error; ?></div> <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-admin">Admin Login</button>
            </form>
            <p><a href="../public/login.php">Student Login</a></p>
        </div>
    </div>
</body>
</html>
