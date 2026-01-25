<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isAdmin()) redirect('login.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['edit_student'])) {
        $id = $_POST['student_id'];
        $student_id = sanitize($_POST['student_id_field']);
        $full_name = sanitize($_POST['full_name']);
        $course = sanitize($_POST['course']);
        $year_level = (int)$_POST['year_level'];
        $username = sanitize($_POST['username']);

        $stmt = $pdo->prepare("UPDATE students SET student_id = ?, full_name = ?, course = ?, year_level = ?, username = ? WHERE id = ?");
        $stmt->execute([$student_id, $full_name, $course, $year_level, $username, $id]);
        $message = "Student updated successfully.";
    } elseif (isset($_POST['delete_student'])) {
        $id = $_POST['student_id'];

        // Check if student has votes
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE student_id = ?");
        $stmt->execute([$id]);
        $vote_count = $stmt->fetchColumn();

        // Get student name for logging
        $stmt = $pdo->prepare("SELECT full_name FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $student_name = $stmt->fetchColumn();

        if ($vote_count > 0) {
            // Allow deletion but remove associated votes
            $stmt = $pdo->prepare("DELETE FROM votes WHERE student_id = ?");
            $stmt->execute([$id]);

            $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            $stmt->execute([$id]);

            // Log this action
            logVotingAttempt($pdo, $_SESSION['admin_id'] ?? 0, 'student_deleted_with_votes', "Student '{$student_name}' deleted with {$vote_count} votes removed");

            $message = "⚠️ Student '{$student_name}' deleted successfully. {$vote_count} associated votes were also removed.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Student removed successfully.";
        }
    } elseif (isset($_POST['reset_password'])) {
        $id = $_POST['student_id'];
        $new_password = password_hash('password123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE students SET password = ? WHERE id = ?");
        $stmt->execute([$new_password, $id]);
        $message = "Student password reset to 'password123'.";
    }
}

$students = $pdo->query("SELECT s.*, (SELECT COUNT(*) FROM votes WHERE student_id = s.id) as vote_count, CASE WHEN v.id IS NOT NULL THEN 'Voted' ELSE 'Not Voted' END as status FROM students s LEFT JOIN votes v ON s.id = v.student_id GROUP BY s.id ORDER BY s.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/main.js" defer></script>
    <script>
        function openEditModal(type, id, studentId, name, course, year, username) {
            if (type === 'student') {
                document.getElementById('editStudentId').value = id;
                document.getElementById('editStudentIdField').value = studentId;
                document.getElementById('editStudentName').value = name;
                document.getElementById('editStudentCourse').value = course;
                document.getElementById('editStudentYear').value = year;
                document.getElementById('editStudentUsername').value = username;
                document.getElementById('editStudentModal').style.display = 'block';
            }
        }

        function closeEditModal(type) {
            if (type === 'student') {
                document.getElementById('editStudentModal').style.display = 'none';
            }
        }

        function resetPassword(id, name) {
            if (confirm(`Are you sure you want to reset the password for ${name}? The new password will be 'password123'.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';

                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'student_id';
                idInput.value = id;

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'reset_password';
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
                <li><a href="students.php" class="active">Students</a></li>
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
            <h1>Manage Students</h1>
            <?php if($message): ?> <div class="alert alert-success"><?php echo $message; ?></div> <?php endif; ?>
            
            <section class="card">
                <h3>Registered Students</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Status</th>
                            <th>Votes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($students as $s): ?>
                        <tr>
                            <td><?php echo $s['student_id']; ?></td>
                            <td><?php echo $s['full_name']; ?></td>
                            <td><?php echo $s['course']; ?></td>
                            <td><?php echo $s['year_level']; ?></td>
                            <td><span class="badge <?php echo $s['status'] == 'Voted' ? 'open' : 'pending'; ?>"><?php echo $s['status']; ?></span></td>
                            <td><?php echo $s['vote_count']; ?></td>
                            <td class="action-buttons">
                                <button type="button" class="btn btn-edit" onclick="openEditModal('student', <?php echo $s['id']; ?>, '<?php echo addslashes($s['student_id']); ?>', '<?php echo addslashes($s['full_name']); ?>', '<?php echo addslashes($s['course']); ?>', <?php echo $s['year_level']; ?>, '<?php echo addslashes($s['username']); ?>')">Edit</button>
                                <button type="button" class="btn btn-warning" onclick="resetPassword(<?php echo $s['id']; ?>, '<?php echo addslashes($s['full_name']); ?>')">Reset Password</button>
                                <form method="POST" onsubmit="return confirmDeleteStudent(<?php echo $s['vote_count']; ?>, '<?php echo addslashes($s['full_name']); ?>');" style="display: inline;">
                                    <input type="hidden" name="student_id" value="<?php echo $s['id']; ?>">
                                    <button type="submit" name="delete_student" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <!-- Edit Student Modal -->
            <div id="editStudentModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeEditModal('student')">&times;</span>
                    <h3>Edit Student</h3>
                    <form method="POST" id="editStudentForm">
                        <input type="hidden" name="student_id" id="editStudentId">
                        <div class="form-group">
                            <label>Student ID</label>
                            <input type="text" name="student_id_field" id="editStudentIdField" required>
                        </div>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" id="editStudentName" required>
                        </div>
                        <div class="form-group">
                            <label>Course</label>
                            <input type="text" name="course" id="editStudentCourse">
                        </div>
                        <div class="form-group">
                            <label>Year Level</label>
                            <input type="number" name="year_level" id="editStudentYear" min="1" max="4">
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" id="editStudentUsername" required>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn" onclick="closeEditModal('student')">Cancel</button>
                            <button type="submit" name="edit_student" class="btn btn-primary">Update Student</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function confirmDeleteStudent(voteCount, studentName) {
            if (voteCount > 0) {
                return confirm('⚠️ WARNING: Student "' + studentName + '" has cast ' + voteCount + ' vote(s)!\n\nDeleting this student will permanently remove their votes from the election results.\n\nThis action CANNOT be undone!\n\nAre you absolutely sure you want to proceed?');
            } else {
                return confirm('Are you sure you want to delete student "' + studentName + '"?\n\nThis action cannot be undone.');
            }
        }

        function resetPassword(studentId, studentName) {
            if (confirm('Reset password for ' + studentName + ' to "password123"?')) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';

                var idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'student_id';
                idInput.value = studentId;
                form.appendChild(idInput);

                var actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'reset_password';
                actionInput.value = '1';
                form.appendChild(actionInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function openEditModal(type, id, studentId, fullName, course, yearLevel, username) {
            // ... existing modal code ...
        }

        function closeEditModal(type) {
            // ... existing modal code ...
        }
    </script>
</body>
</html>
