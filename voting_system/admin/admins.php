<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isAdmin()) redirect('login.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_admin'])) {
        $username = sanitize($_POST['username']);
        $full_name = sanitize($_POST['full_name']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $face_image = $_POST['face_image'];

        try {
            $stmt = $pdo->prepare("INSERT INTO admins (username, password, full_name, face_image) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $password, $full_name, $face_image]);
            $message = "Admin added successfully.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "Username already exists.";
            } else {
                $message = "An error occurred. Please try again.";
            }
        }
    } elseif (isset($_POST['edit_admin'])) {
        $id = $_POST['admin_id'];
        $username = sanitize($_POST['username']);
        $full_name = sanitize($_POST['full_name']);
        $face_image = $_POST['face_image'] ?? $_POST['existing_face_image'];

        try {
            $stmt = $pdo->prepare("UPDATE admins SET username = ?, full_name = ?, face_image = ? WHERE id = ?");
            $stmt->execute([$username, $full_name, $face_image, $id]);
            $message = "Admin updated successfully.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "Username already exists.";
            } else {
                $message = "An error occurred. Please try again.";
            }
        }
    } elseif (isset($_POST['delete_admin'])) {
        $id = $_POST['admin_id'];
        if ($id != $_SESSION['admin_id']) { // Prevent deleting self
            $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Admin account removed.";
        } else {
            $message = "Cannot delete your own account.";
        }
    } elseif (isset($_POST['reset_admin_password'])) {
        $id = $_POST['admin_id'];
        $new_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
        $stmt->execute([$new_password, $id]);
        $message = "Admin password reset to 'admin123'.";
    }
}

$admins = $pdo->query("SELECT * FROM admins ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/main.js" defer></script>
    <script>
        function openEditModal(type, id, username, name, faceImage) {
            if (type === 'admin') {
                document.getElementById('editAdminId').value = id;
                document.getElementById('editAdminUsername').value = username;
                document.getElementById('editAdminName').value = name;
                document.getElementById('editAdminFaceImage').value = faceImage;
                document.getElementById('edit-face-image').value = faceImage;

                const faceContainer = document.getElementById('currentFaceContainer');
                if (faceImage) {
                    faceContainer.innerHTML = '<p><strong>Current Face Data:</strong> Registered</p>';
                } else {
                    faceContainer.innerHTML = '<p><strong>Current Face Data:</strong> Not registered</p>';
                }

                document.getElementById('editAdminModal').style.display = 'block';
            }
        }

        function closeEditModal(type) {
            if (type === 'admin') {
                document.getElementById('editAdminModal').style.display = 'none';
                // Reset camera elements
                document.getElementById('edit-video').style.display = 'none';
                document.getElementById('edit-captured-image').style.display = 'none';
            }
        }

        function resetAdminPassword(id, name) {
            if (confirm(`Are you sure you want to reset the password for ${name}? The new password will be 'admin123'.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';

                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'admin_id';
                idInput.value = id;

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'reset_admin_password';
                actionInput.value = '1';

                form.appendChild(idInput);
                form.appendChild(actionInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = document.getElementsByClassName('modal');
            for (let modal of modals) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
        }
    </script>
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
                <li><a href="admins.php" class="active">Admins</a></li>
                <li><a href="campus_ips.php">Network Settings</a></li>
                <li><a href="results.php">Results</a></li>
                <li><a href="location_settings.php">Access Control</a></li>
                <li><a href="security_monitor.php">Security Monitor</a></li>
                <li><a href="school_hours.php">School Hours</a></li>
                <li><a href="../public/logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="content">
            <h1>Manage Admins</h1>
            <?php if($message): ?> <div class="alert alert-success"><?php echo $message; ?></div> <?php endif; ?>
            
            <section class="card">
                <h3>Add New Admin</h3>
                <form method="POST" id="addAdminForm">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required id="password">
                    </div>
                    <div class="form-group">
                        <label>Facial Registration</label>
                        <div id="camera-container">
                            <video id="video" width="320" height="240" autoplay></video>
                            <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
                            <br>
                            <button type="button" id="capture-btn" class="btn">Capture Face</button>
                            <img id="captured-image" style="display:none; max-width:320px; max-height:240px;">
                        </div>
                        <input type="hidden" name="face_image" id="face_image" required>
                    </div>
                    <button type="submit" name="add_admin" class="btn">Add Admin</button>
                </form>
            </section>
            
            <section class="card">
                <h3>Registered Admins</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($admins as $a): ?>
                        <tr>
                            <td><?php echo $a['username']; ?></td>
                            <td><?php echo $a['full_name']; ?></td>
                            <td class="action-buttons">
                                <button type="button" class="btn btn-edit" onclick="openEditModal('admin', <?php echo $a['id']; ?>, '<?php echo addslashes($a['username']); ?>', '<?php echo addslashes($a['full_name']); ?>', '<?php echo addslashes($a['face_image'] ?? ''); ?>')">Edit</button>
                                <button type="button" class="btn btn-warning" onclick="resetAdminPassword(<?php echo $a['id']; ?>, '<?php echo addslashes($a['full_name']); ?>')">Reset Password</button>
                                <?php if ($a['id'] != $_SESSION['admin_id']): ?>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this admin?');" style="display: inline;">
                                    <input type="hidden" name="admin_id" value="<?php echo $a['id']; ?>">
                                    <button type="submit" name="delete_admin" class="btn btn-danger">Delete</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <!-- Edit Admin Modal -->
            <div id="editAdminModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeEditModal('admin')">&times;</span>
                    <h3>Edit Admin</h3>
                    <form method="POST" id="editAdminForm">
                        <input type="hidden" name="admin_id" id="editAdminId">
                        <input type="hidden" name="existing_face_image" id="editAdminFaceImage">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" id="editAdminUsername" required>
                        </div>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" id="editAdminName" required>
                        </div>
                        <div class="form-group">
                            <label>Facial Registration</label>
                            <div id="edit-camera-container">
                                <video id="edit-video" width="320" height="240" autoplay style="display:none;"></video>
                                <canvas id="edit-canvas" width="320" height="240" style="display:none;"></canvas>
                                <br>
                                <button type="button" id="edit-capture-btn" class="btn">Capture New Face</button>
                                <img id="edit-captured-image" style="display:none; max-width:320px; max-height:240px;">
                                <div id="currentFaceContainer" style="margin-top: 10px;"></div>
                            </div>
                            <input type="hidden" name="face_image" id="edit-face-image">
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn" onclick="closeEditModal('admin')">Cancel</button>
                            <button type="submit" name="edit_admin" class="btn btn-primary">Update Admin</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script src="../assets/js/validation.js"></?php>
</body>
</html>
