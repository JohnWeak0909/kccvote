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
    $face_descriptor = $_POST['face_descriptor'] ?? null;

    try {
        $stmt = $pdo->prepare("INSERT INTO students (student_id, full_name, course, year_level, username, password, face_descriptor) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$student_id, $full_name, $course, $year_level, $username, $password, $face_descriptor]);
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
    <title>Student Registration - KCC Online Voting</title>
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
            </div>
            <?php if($error): ?> <div class="alert alert-danger"><?php echo $error; ?></div> <?php endif; ?>
            <?php if($success): ?> <div class="alert alert-success"><?php echo $success; ?></div> <?php endif; ?>
            <form action="register.php" method="POST" id="regForm">
                <div class="form-group">
                    <label>Student ID</label>
                    <input type="text" name="student_id" required>
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" required>
                </div>
                <div class="form-group">
                    <label>Course</label>
                    <input type="text" name="course" required>
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
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required id="password">
                </div>
                <div class="form-group">
                    <label>Facial Registration <span style="font-weight: normal; color: #666;"></span></label>
                    <div id="camera-container">
                        <video id="video" width="320" height="240" autoplay></video>
                        <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
                        <br>
                        <button type="button" id="capture-btn" class="btn" disabled>Loading face detection...</button>
                        <img id="captured-image" style="display:none; max-width:320px; max-height:240px;">
                    </div>
                    <div id="face-status" style="background: #e3f2fd; border: 1px solid #2196f3; border-radius: 8px; padding: 10px; text-align: center; margin-top: 10px;">
                        <p style="margin: 0; color: #1976d2; font-size: 0.9em;">Initializing face detection... This may take a moment.</p>
                    </div>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script src="../assets/js/validation.js"></script>
</body>
</html>
