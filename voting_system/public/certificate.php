<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) redirect('login.php');

// Check if just voted
if (!isset($_SESSION['just_voted'])) redirect('index.php');

$student = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$student->execute([$_SESSION['user_id']]);
$student = $student->fetch();

$election = $pdo->query("SELECT * FROM elections WHERE status = 'open' ORDER BY id DESC LIMIT 1")->fetch();

unset($_SESSION['just_voted']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Certificate - Voting System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .certificate {
            background: var(--white);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: var(--shadow);
            text-align: center;
            max-width: 600px;
            margin: 2rem auto;
            border: 5px solid var(--secondary-color);
        }
        .certificate h1 { color: var(--primary-color); }
        .certificate p { margin: 1rem 0; }
        .watermark { opacity: 0.1; font-size: 5rem; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); }
    </style>
</head>
<body>
    <div class="container">
        <div class="certificate">
            <div class="watermark">VOTED</div>
            <h1>Voting Certificate</h1>
            <p>This certifies that</p>
            <h2><?php echo $student['full_name']; ?></h2>
            <p>Student ID: <?php echo $student['student_id']; ?></p>
            <p>Has successfully participated in the</p>
            <h3><?php echo $election['title']; ?></h3>
            <p>Date: <?php echo date('F j, Y'); ?></p>
            <p>Thank you for your participation!</p>
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </div>
</body>
</html>