<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isAdmin()) redirect('login.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_position'])) {
        $title = sanitize($_POST['title']);
        $priority = (int)$_POST['priority'];
        $stmt = $pdo->prepare("INSERT INTO positions (title, priority) VALUES (?, ?)");
        $stmt->execute([$title, $priority]);
        $message = "Position added.";
    } elseif (isset($_POST['edit_position'])) {
        $id = $_POST['position_id'];
        $title = sanitize($_POST['title']);
        $priority = (int)$_POST['priority'];
        $stmt = $pdo->prepare("UPDATE positions SET title = ?, priority = ? WHERE id = ?");
        $stmt->execute([$title, $priority, $id]);
        $message = "Position updated.";
    } elseif (isset($_POST['delete_position'])) {
        $id = $_POST['position_id'];

        // Check if position has candidates
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM candidates WHERE position_id = ?");
        $stmt->execute([$id]);
        $candidate_count = $stmt->fetchColumn();

        // Get position title for logging
        $stmt = $pdo->prepare("SELECT title FROM positions WHERE id = ?");
        $stmt->execute([$id]);
        $position_title = $stmt->fetchColumn();

        if ($candidate_count > 0) {
            // Allow deletion but remove associated candidates and their votes
            $stmt = $pdo->prepare("SELECT id FROM candidates WHERE position_id = ?");
            $stmt->execute([$id]);
            $candidate_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Delete votes for these candidates
            if (!empty($candidate_ids)) {
                $placeholders = str_repeat('?,', count($candidate_ids) - 1) . '?';
                $stmt = $pdo->prepare("DELETE FROM votes WHERE candidate_id IN ($placeholders)");
                $stmt->execute($candidate_ids);
            }

            // Delete candidates
            $stmt = $pdo->prepare("DELETE FROM candidates WHERE position_id = ?");
            $stmt->execute([$id]);

            // Delete position
            $stmt = $pdo->prepare("DELETE FROM positions WHERE id = ?");
            $stmt->execute([$id]);

            // Log this action
            logVotingAttempt($pdo, $_SESSION['admin_id'] ?? 0, 'position_deleted_with_data', "Position '{$position_title}' deleted with {$candidate_count} candidates and their votes removed");

            $message = "⚠️ Position '{$position_title}' deleted successfully. {$candidate_count} associated candidates and their votes were also removed.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM positions WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Position removed successfully.";
        }
    }
}

$positions = $pdo->query("SELECT p.*, (SELECT COUNT(*) FROM candidates WHERE position_id = p.id) as candidate_count FROM positions p ORDER BY priority DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Positions - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/main.js" defer></script>
    <script>
        function openEditModal(type, id, title, priority) {
            if (type === 'position') {
                document.getElementById('editPositionId').value = id;
                document.getElementById('editPositionTitle').value = title;
                document.getElementById('editPositionPriority').value = priority;
                document.getElementById('editPositionModal').style.display = 'block';
            }
        }

        function closeEditModal(type) {
            if (type === 'position') {
                document.getElementById('editPositionModal').style.display = 'none';
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
                <li><a href="positions.php" class="active">Positions</a></li>
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
            <h1>Manage Positions</h1>
            <?php if($message): ?> <div class="alert alert-success"><?php echo $message; ?></div> <?php endif; ?>
            
            <section class="card">
                <h3>Add New Position</h3>
                <form method="POST" class="inline-form">
                    <input type="text" name="title" placeholder="Position Title" required>
                    <input type="number" name="priority" placeholder="Priority (Higher = First)" required>
                    <button type="submit" name="add_position" class="btn">Add Position</button>
                </form>
            </section>

            <section class="card">
                <h3>Position List</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Priority</th>
                            <th>Candidates</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($positions as $p): ?>
                        <tr>
                            <td><?php echo $p['title']; ?></td>
                            <td><?php echo $p['priority']; ?></td>
                            <td><?php echo $p['candidate_count']; ?></td>
                            <td class="action-buttons">
                                <button type="button" class="btn btn-edit" onclick="openEditModal('position', <?php echo $p['id']; ?>, '<?php echo addslashes($p['title']); ?>', <?php echo $p['priority']; ?>)">Edit</button>
                                <form method="POST" onsubmit="return confirmDeletePosition(<?php echo $p['candidate_count']; ?>, '<?php echo addslashes($p['title']); ?>');" style="display: inline;">
                                    <input type="hidden" name="position_id" value="<?php echo $p['id']; ?>">
                                    <button type="submit" name="delete_position" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <!-- Edit Position Modal -->
            <div id="editPositionModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeEditModal('position')">&times;</span>
                    <h3>Edit Position</h3>
                    <form method="POST" id="editPositionForm">
                        <input type="hidden" name="position_id" id="editPositionId">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" id="editPositionTitle" required>
                        </div>
                        <div class="form-group">
                            <label>Priority</label>
                            <input type="number" name="priority" id="editPositionPriority" required>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn" onclick="closeEditModal('position')">Cancel</button>
                            <button type="submit" name="edit_position" class="btn btn-primary">Update Position</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function confirmDeletePosition(candidateCount, positionTitle) {
            if (candidateCount > 0) {
                return confirm('⚠️ WARNING: Position "' + positionTitle + '" has ' + candidateCount + ' candidate(s)!\n\nDeleting this position will permanently remove all associated candidates and their votes from the election results.\n\nThis action CANNOT be undone!\n\nAre you absolutely sure you want to proceed?');
            } else {
                return confirm('Are you sure you want to delete the position "' + positionTitle + '"?\n\nThis action cannot be undone.');
            }
        }

        function openEditModal(type, id, title, priority) {
            // ... existing modal code ...
        }

        function closeEditModal(type) {
            // ... existing modal code ...
        }
    </script>
</body>
</html>
