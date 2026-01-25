<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (isLoggedIn()) redirect('index.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = sanitize($_POST['student_id']);
    $full_name = sanitize($_POST['full_name']);
    $course = sanitize($_POST['course']);
    $year_level = sanitize($_POST['year_level']);
    $username = sanitize($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO students (student_id, full_name, course, year_level, username, password, face_descriptor) VALUES (?, ?, ?, ?, ?, ?, NULL)");
        $stmt->execute([$student_id, $full_name, $course, $year_level, $username, $password]);
        $success = "Registration successful! You can now login.";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "Student ID or Username already exists.";
        } else {
            $error = "An error occurred. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Student Registration - KCC Online Voting</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="form-wrapper">
            <div class="logo-container">
                <img src="../assets/images/kcclogo.jpg" alt="KCC Online Voting Logo" class="logo">
                <h1 class="main-title">KCC Online Voting</h1>
                <h2 style="color: #666; font-size: 1.2em; margin-top: 10px;">Simple Registration (No Face Recognition)</h2>
            </div>
            <?php if($error): ?> <div class="alert alert-danger"><?php echo $error; ?></div> <?php endif; ?>
            <?php if($success): ?> <div class="alert alert-success"><?php echo $success; ?></div> <?php endif; ?>
            <form action="register_simple.php" method="POST" id="regForm">
                <div class="form-group">
                    <label>Student ID</label>
                    <input type="text" name="student_id" required placeholder="e.g., 2024-00123">
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" required placeholder="e.g., Juan Dela Cruz">
                </div>
                <div class="form-group">
                    <label>Course</label>
                    <input type="text" name="course" required placeholder="e.g., BS Computer Science">
                </div>
                <div class="form-group">
                    <label>Year Level</label>
                    <select name="year_level" required>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Choose a username">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required id="password" placeholder="Create a password">
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required id="confirm_password" placeholder="Confirm your password">
                </div>
                <div class="alert alert-info" style="margin-bottom: 20px;">
                    <strong>Note:</strong> This registration method skips face recognition for testing purposes.
                    Students registered here will need to use password-only login.
                </div>
                <button type="submit" class="btn">Register Student</button>
            </form>
            <p>Need face recognition? <a href="../public/register.php">Use full registration</a></p>
            <p>Already have an account? <a href="../public/login.php">Login here</a></p>
        </div>
    </div>
    <script src="../assets/js/validation.js"></script>
    <script>
        // Simple password confirmation validation
        document.getElementById('regForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });
    </script>
</body>
</html>