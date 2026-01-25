<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isAdmin()) redirect('login.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_party'])) {
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description'] ?? '');
        $stmt = $pdo->prepare("INSERT INTO parties (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $description]);
        $message = "Party added.";
    } elseif (isset($_POST['edit_party'])) {
        $id = $_POST['party_id'];
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description'] ?? '');
        $stmt = $pdo->prepare("UPDATE parties SET name = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $description, $id]);
        $message = "Party updated.";
    } elseif (isset($_POST['delete_party'])) {
        $id = $_POST['party_id'];

        // Check if party has candidates
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM candidates WHERE party_id = ?");
        $stmt->execute([$id]);
        $candidate_count = $stmt->fetchColumn();

        // Get party name for logging
        $stmt = $pdo->prepare("SELECT name FROM parties WHERE id = ?");
        $stmt->execute([$id]);
        $party_name = $stmt->fetchColumn();

        if ($candidate_count > 0) {
            // Allow deletion but remove associated candidates and their votes
            $stmt = $pdo->prepare("SELECT id FROM candidates WHERE party_id = ?");
            $stmt->execute([$id]);
            $candidate_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Delete votes for these candidates
            if (!empty($candidate_ids)) {
                $placeholders = str_repeat('?,', count($candidate_ids) - 1) . '?';
                $stmt = $pdo->prepare("DELETE FROM votes WHERE candidate_id IN ($placeholders)");
                $stmt->execute($candidate_ids);
            }

            // Delete candidates
            $stmt = $pdo->prepare("DELETE FROM candidates WHERE party_id = ?");
            $stmt->execute([$id]);

            // Delete party
            $stmt = $pdo->prepare("DELETE FROM parties WHERE id = ?");
            $stmt->execute([$id]);

            // Log this action
            logVotingAttempt($pdo, $_SESSION['admin_id'] ?? 0, 'party_deleted_with_data', "Party '{$party_name}' deleted with {$candidate_count} candidates and their votes removed");

            $message = "⚠️ Party '{$party_name}' deleted successfully. {$candidate_count} associated candidates and their votes were also removed.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM parties WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Party removed successfully.";
        }
    }
}

$parties = $pdo->query("SELECT p.*, (SELECT COUNT(*) FROM candidates WHERE party_id = p.id) as candidate_count FROM parties p ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Parties - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/main.js" defer></script>
    <script>
        function openEditModal(type, id, name, description) {
            if (type === 'party') {
                document.getElementById('editPartyId').value = id;
                document.getElementById('editPartyName').value = name;
                document.getElementById('editPartyDescription').value = description;
                document.getElementById('editPartyModal').style.display = 'block';
            }
        }

        function closeEditModal(type) {
            if (type === 'party') {
                document.getElementById('editPartyModal').style.display = 'none';
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
                <li><a href="parties.php" class="active">Parties</a></li>
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
            <h1>Manage Parties</h1>
            <?php if($message): ?> <div class="alert alert-success"><?php echo $message; ?></div> <?php endif; ?>
            
            <section class="card">
                <h3>Add New Party</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Party Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Description (Optional)</label>
                        <textarea name="description" rows="3"></textarea>
                    </div>
                    <button type="submit" name="add_party" class="btn">Add Party</button>
                </form>
            </section>

            <section class="card">
                <h3>Party List</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Candidates</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($parties as $p): ?>
                        <tr>
                            <td><?php echo $p['name']; ?></td>
                            <td><?php echo $p['description'] ?: 'No description'; ?></td>
                            <td><?php echo $p['candidate_count']; ?></td>
                            <td class="action-buttons">
                                <button type="button" class="btn btn-edit" onclick="openEditModal('party', <?php echo $p['id']; ?>, '<?php echo addslashes($p['name']); ?>', '<?php echo addslashes($p['description'] ?? ''); ?>')">Edit</button>
                                <form method="POST" onsubmit="return confirmDeleteParty(<?php echo $p['candidate_count']; ?>, '<?php echo addslashes($p['name']); ?>');" style="display: inline;">
                                    <input type="hidden" name="party_id" value="<?php echo $p['id']; ?>">
                                    <button type="submit" name="delete_party" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <!-- Edit Party Modal -->
            <div id="editPartyModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeEditModal('party')">&times;</span>
                    <h3>Edit Party</h3>
                    <form method="POST" id="editPartyForm">
                        <input type="hidden" name="party_id" id="editPartyId">
                        <div class="form-group">
                            <label>Party Name</label>
                            <input type="text" name="name" id="editPartyName" required>
                        </div>
                        <div class="form-group">
                            <label>Description (Optional)</label>
                            <textarea name="description" id="editPartyDescription" rows="3"></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn" onclick="closeEditModal('party')">Cancel</button>
                            <button type="submit" name="edit_party" class="btn btn-primary">Update Party</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function confirmDeleteParty(candidateCount, partyName) {
            if (candidateCount > 0) {
                return confirm('⚠️ WARNING: Party "' + partyName + '" has ' + candidateCount + ' candidate(s)!\n\nDeleting this party will permanently remove all associated candidates and their votes from the election results.\n\nThis action CANNOT be undone!\n\nAre you absolutely sure you want to proceed?');
            } else {
                return confirm('Are you sure you want to delete the party "' + partyName + '"?\n\nThis action cannot be undone.');
            }
        }

        function openEditModal(type, id, name, description) {
            // ... existing modal code ...
        }

        function closeEditModal(type) {
            // ... existing modal code ...
        }
    </script>
</body>
</html>
