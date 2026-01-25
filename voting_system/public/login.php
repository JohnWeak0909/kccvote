<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (isLoggedIn()) redirect('index.php');

$error = '';
$action = $_GET['action'] ?? 'student';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == 'student') {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = sanitize($_POST['username']);
            $password = $_POST['password'];

            $stmt = $pdo->prepare("SELECT * FROM students WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                if ($user['face_descriptor']) {
                    $_SESSION['temp_user_id'] = $user['id'];
                    $_SESSION['temp_student_id'] = $user['student_id'];
                    $_SESSION['temp_full_name'] = $user['full_name'];
                    $_SESSION['temp_face_descriptor'] = $user['face_descriptor'];
                    // Don't redirect, show face verification
                } else {
                    // No face descriptor, login directly
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['student_id'] = $user['student_id'];
                    $_SESSION['full_name'] = $user['full_name'];
                    redirect('index.php');
                }
            } else {
                $error = "Invalid username or password.";
            }
        } elseif (isset($_POST['face_verified'])) {
            // Face verified, complete login
            $_SESSION['user_id'] = $_SESSION['temp_user_id'];
            $_SESSION['student_id'] = $_SESSION['temp_student_id'];
            $_SESSION['full_name'] = $_SESSION['temp_full_name'];
            unset($_SESSION['temp_user_id'], $_SESSION['temp_student_id'], $_SESSION['temp_full_name'], $_SESSION['temp_face_descriptor']);
            redirect('index.php');
        }
    } elseif ($action == 'admin') {
        $username = sanitize($_POST['username']);
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            redirect('../admin/index.php');
        } else {
            $error = "Invalid admin credentials.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($action); ?> Login - KCC Online Voting</title>
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
            <div class="tabs">
                <div class="tab <?php echo $action == 'student' ? 'active' : ''; ?>"><a href="?action=student" class="tab-link">Student Login</a></div>
                <div class="tab <?php echo $action == 'admin' ? 'active' : ''; ?>"><a href="?action=admin" class="tab-link">Admin Login</a></div>
                <div class="tab"><a href="register.php" class="tab-link">Create Account</a></div>
            </div>
            <?php if($error): ?> <div class="alert alert-danger"><?php echo $error; ?></div> <?php endif; ?>
            <?php if($action == 'student'): ?>
                <h2>Student Login</h2>
                <?php if(!isset($_SESSION['temp_user_id'])): ?>
                <form action="?action=student" method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" class="btn">Login</button>
                </form>
                <?php else: ?>
                <div id="face-recognition">
                    <h3>Face Verification</h3>
                    <p>Please look directly at the camera and click "Verify Face" to complete login.</p>
                    <div id="camera-container">
                        <video id="video" width="320" height="240" autoplay></video>
                        <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
                        <br>
                        <button type="button" id="capture-btn" class="btn">Verify Face</button>
                        <div id="status" style="margin-top: 10px; display: none;"></div>
                    </div>
                </div>
                <form action="?action=student" method="POST" id="face-form" style="display: none;">
                    <input type="hidden" name="face_verified" value="1">
                </form>
                <p><a href="?action=student">Back to Login</a></p>
                <?php endif; ?>
            <?php elseif($action == 'admin'): ?>
                <h2>Admin Login</h2>
                <form action="?action=admin" method="POST">
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
            <?php endif; ?>
        </div>
    </div>
    <?php if($action == 'student' && isset($_SESSION['temp_user_id'])): ?>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const captureBtn = document.getElementById('capture-btn');
            const faceForm = document.getElementById('face-form');

            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(function(stream) {
                        video.srcObject = stream;
                        video.play();
                    })
                    .catch(function(err) {
                        console.log("An error occurred: " + err);
                    });
            }

            // Load face-api.js models
            Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri('../assets/js/models'),
                faceapi.nets.faceLandmark68Net.loadFromUri('https://unpkg.com/face-api.js@0.22.2/weights'),
                faceapi.nets.faceRecognitionNet.loadFromUri('https://unpkg.com/face-api.js@0.22.2/weights')
            ]).then(() => {
                console.log('Face detection models loaded');
            }).catch(err => {
                console.error('Error loading models:', err);
            });

            captureBtn.addEventListener('click', async function() {
                const status = document.getElementById('status');
                status.style.display = 'block';
                status.textContent = 'Detecting face...';
                captureBtn.disabled = true;
                captureBtn.textContent = 'Processing...';

                const context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, 320, 240);
                
                try {
                    const detection = await faceapi.detectSingleFace(canvas, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
                    if (!detection) {
                        status.textContent = 'No face detected. Please ensure your face is clearly visible and try again.';
                        captureBtn.disabled = false;
                        captureBtn.textContent = 'Verify Face';
                        return;
                    }

                    status.textContent = 'Verifying face...';

                    // Send descriptor to server for verification
                    const formData = new FormData();
                    formData.append('descriptor', JSON.stringify(detection.descriptor));

                    const response = await fetch('face_login.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        status.textContent = 'Face verified successfully! Completing login...';
                        faceForm.submit();
                    } else {
                        status.textContent = data.error || 'Face verification failed. Please try again.';
                        captureBtn.disabled = false;
                        captureBtn.textContent = 'Try Again';
                    }
                } catch (err) {
                    console.error('Error during face verification:', err);
                    status.textContent = 'An error occurred during face verification. Please try again.';
                    captureBtn.disabled = false;
                    captureBtn.textContent = 'Verify Face';
                }
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>
