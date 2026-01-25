<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isAdmin()) redirect('login.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_candidate'])) {
        $name = sanitize($_POST['full_name']);
        $party_id = $_POST['party_id'];
        $position_id = $_POST['position_id'];
        $photo_url = '';

        // Handle photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $upload_dir = '../assets/images/candidates/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('candidate_') . '.' . $file_extension;
            $target_file = $upload_dir . $file_name;

            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($file_extension), $allowed_types)) {
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                    $photo_url = 'assets/images/candidates/' . $file_name;
                }
            }
        }

        $stmt = $pdo->prepare("INSERT INTO candidates (full_name, party_id, position_id, photo_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $party_id, $position_id, $photo_url]);
        $message = "Candidate added successfully.";
    } elseif (isset($_POST['edit_candidate'])) {
        $id = $_POST['candidate_id'];
        $name = sanitize($_POST['full_name']);
        $party_id = $_POST['party_id'];
        $position_id = $_POST['position_id'];
        $photo_url = $_POST['existing_photo'] ?? '';

        // Handle photo upload for edit
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $upload_dir = '../assets/images/candidates/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('candidate_') . '.' . $file_extension;
            $target_file = $upload_dir . $file_name;

            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($file_extension), $allowed_types)) {
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                    $photo_url = 'assets/images/candidates/' . $file_name;
                }
            }
        }

        $stmt = $pdo->prepare("UPDATE candidates SET full_name = ?, party_id = ?, position_id = ?, photo_url = ? WHERE id = ?");
        $stmt->execute([$name, $party_id, $position_id, $photo_url, $id]);
        $message = "Candidate updated successfully.";
    } elseif (isset($_POST['delete_candidate'])) {
        $id = $_POST['candidate_id'];

        // Check if candidate has votes
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE candidate_id = ?");
        $stmt->execute([$id]);
        $vote_count = $stmt->fetchColumn();

        // Get candidate name for logging
        $stmt = $pdo->prepare("SELECT full_name FROM candidates WHERE id = ?");
        $stmt->execute([$id]);
        $candidate_name = $stmt->fetchColumn();

        if ($vote_count > 0) {
            // Allow deletion but log the action and warn about data loss
            $stmt = $pdo->prepare("DELETE FROM votes WHERE candidate_id = ?");
            $stmt->execute([$id]);

            $stmt = $pdo->prepare("DELETE FROM candidates WHERE id = ?");
            $stmt->execute([$id]);

            // Log this action
            logVotingAttempt($pdo, $_SESSION['admin_id'] ?? 0, 'candidate_deleted_with_votes', "Candidate '{$candidate_name}' deleted with {$vote_count} votes removed");

            $message = "⚠️ Candidate '{$candidate_name}' deleted successfully. {$vote_count} associated votes were also removed.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM candidates WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Candidate removed successfully.";
        }
    }
}

$candidates = $pdo->query("SELECT c.*, p.name as party_name, pos.title as position_title,
                           (SELECT COUNT(*) FROM votes WHERE candidate_id = c.id) as vote_count
                           FROM candidates c
                           LEFT JOIN parties p ON c.party_id = p.id
                           LEFT JOIN positions pos ON c.position_id = pos.id
                           ORDER BY pos.priority DESC, c.full_name ASC")->fetchAll();

$parties = $pdo->query("SELECT * FROM parties")->fetchAll();
$positions = $pdo->query("SELECT * FROM positions ORDER BY priority DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Candidates - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/main.js" defer></script>
    <script>
        function openEditModal(type, id, name, partyId, positionId, photoUrl) {
            if (type === 'candidate') {
                document.getElementById('editCandidateId').value = id;
                document.getElementById('editCandidateName').value = name;
                document.getElementById('editCandidateParty').value = partyId;
                document.getElementById('editCandidatePosition').value = positionId;
                document.getElementById('editCandidatePhoto').value = photoUrl;

                const photoContainer = document.getElementById('currentPhotoContainer');
                if (photoUrl) {
                    photoContainer.innerHTML = '<p><strong>Current Photo:</strong></p><img src="../' + photoUrl + '" alt="Current photo" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 2px solid #e5e7eb;">';
                } else {
                    photoContainer.innerHTML = '<p><strong>Current Photo:</strong> No photo uploaded</p>';
                }

                document.getElementById('editCandidateModal').style.display = 'block';
            }
        }

        function closeEditModal(type) {
            if (type === 'candidate') {
                document.getElementById('editCandidateModal').style.display = 'none';
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
                <li><a href="candidates.php" class="active">Candidates</a></li>
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
            <h1>Manage Candidates</h1>
            <?php if($message): ?> <div class="alert alert-success"><?php echo $message; ?></div> <?php endif; ?>
            
            <section class="card">
                <h3>Add New Candidate</h3>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label>Party</label>
                        <select name="party_id" required>
                            <?php foreach($parties as $party): ?>
                            <option value="<?php echo $party['id']; ?>"><?php echo $party['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Position</label>
                        <select name="position_id" required>
                            <?php foreach($positions as $pos): ?>
                            <option value="<?php echo $pos['id']; ?>"><?php echo $pos['title']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Photo (Optional)</label>
                        <input type="file" name="photo" accept="image/*">
                        <small class="form-text">Accepted formats: JPG, PNG, GIF. Max size: 5MB</small>
                    </div>
                    <button type="submit" name="add_candidate" class="btn">Add Candidate</button>
                </form>
            </section>

            <section class="card">
                <h3>Candidate List</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Party</th>
                            <th>Votes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($candidates as $c): ?>
                        <tr>
                            <td>
                                <?php if($c['photo_url']): ?>
                                    <img src="../<?php echo $c['photo_url']; ?>" alt="<?php echo $c['full_name']; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                <?php else: ?>
                                    <div style="width: 50px; height: 50px; background: #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #6b7280;">👤</div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $c['full_name']; ?></td>
                            <td><?php echo $c['position_title']; ?></td>
                            <td><?php echo $c['party_name']; ?></td>
                            <td><?php echo $c['vote_count']; ?></td>
                            <td class="action-buttons">
                                <button type="button" class="btn btn-edit" onclick="openEditModal('candidate', <?php echo $c['id']; ?>, '<?php echo addslashes($c['full_name']); ?>', <?php echo $c['party_id']; ?>, <?php echo $c['position_id']; ?>, '<?php echo addslashes($c['photo_url'] ?? ''); ?>')">Edit</button>
                                <form method="POST" onsubmit="return confirmDeleteCandidate(<?php echo $c['vote_count']; ?>, '<?php echo addslashes($c['full_name']); ?>');" style="display: inline;">
                                    <input type="hidden" name="candidate_id" value="<?php echo $c['id']; ?>">
                                    <button type="submit" name="delete_candidate" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <!-- Edit Candidate Modal -->
            <div id="editCandidateModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeEditModal('candidate')">&times;</span>
                    <h3>Edit Candidate</h3>
                    <form method="POST" enctype="multipart/form-data" id="editCandidateForm">
                        <input type="hidden" name="candidate_id" id="editCandidateId">
                        <input type="hidden" name="existing_photo" id="editCandidatePhoto">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" id="editCandidateName" required>
                        </div>
                        <div class="form-group">
                            <label>Party</label>
                            <select name="party_id" id="editCandidateParty" required>
                                <?php foreach($parties as $party): ?>
                                <option value="<?php echo $party['id']; ?>"><?php echo $party['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Position</label>
                            <select name="position_id" id="editCandidatePosition" required>
                                <?php foreach($positions as $pos): ?>
                                <option value="<?php echo $pos['id']; ?>"><?php echo $pos['title']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Photo (Optional)</label>
                            <input type="file" name="photo" accept="image/*">
                            <small class="form-text">Leave empty to keep current photo. Accepted formats: JPG, PNG, GIF. Max size: 5MB</small>
                            <div id="currentPhotoContainer" style="margin-top: 10px;"></div>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn" onclick="closeEditModal('candidate')">Cancel</button>
                            <button type="submit" name="edit_candidate" class="btn btn-primary">Update Candidate</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function confirmDeleteCandidate(voteCount, candidateName) {
            if (voteCount > 0) {
                return confirm('⚠️ WARNING: ' + candidateName + ' has received ' + voteCount + ' vote(s)!\n\nDeleting this candidate will permanently remove their votes from the election results.\n\nThis action CANNOT be undone!\n\nAre you absolutely sure you want to proceed?');
            } else {
                return confirm('Are you sure you want to delete ' + candidateName + '?\n\nThis action cannot be undone.');
            }
        }

        function openEditModal(type, id, name, partyId, positionId, photoUrl) {
            // ... existing modal code ...
        }

        function closeEditModal(type) {
            // ... existing modal code ...
        }
    </script>
</body>
</html>
