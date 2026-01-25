<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) redirect('login.php');

// Restrict to campus for voting
restrictToCampus($pdo);

$student_id = $_SESSION['user_id'];
$election = $pdo->query("SELECT * FROM elections WHERE status = 'open' ORDER BY id DESC LIMIT 1")->fetch();

// Validate election time window
if ($election && !validateElectionTime($election)) {
    $election = null; // Election not available at this time
}

// Check if it's school hours (additional security)
$school_hours_check = isSchoolHours();
if (!$school_hours_check) {
    logVotingAttempt($pdo, $student_id, 'access_outside_school_hours', 'Attempted access outside school hours');
}

$has_voted = false;
if ($election) {
    $stmt = $pdo->prepare("SELECT id FROM votes WHERE student_id = ? AND election_id = ? LIMIT 1");
    $stmt->execute([$student_id, $election['id']]);
    $has_voted = $stmt->fetch() ? true : false;
}

$positions = [];
if ($election && !$has_voted) {
    $all_positions = $pdo->query("SELECT * FROM positions ORDER BY priority DESC")->fetchAll();
    foreach ($all_positions as $pos) {
        $stmt = $pdo->prepare("SELECT c.*, p.name as party_name FROM candidates c LEFT JOIN parties p ON c.party_id = p.id WHERE c.position_id = ?");
        $stmt->execute([$pos['id']]);
        $candidates = $stmt->fetchAll();
        
        // Include all positions, even if they have no candidates
        $pos['candidates'] = $candidates;
        $positions[] = $pos;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $election && !$has_voted) {
    // Additional security checks before voting
    if (!validateVotingSession($pdo, $student_id)) {
        $error = "Suspicious voting activity detected. Please contact an administrator.";
        logVotingAttempt($pdo, $student_id, 'blocked_suspicious_activity', 'Multiple votes detected');
    } elseif (!$school_hours_check) {
        $error = "Voting is only allowed during school hours.";
        logVotingAttempt($pdo, $student_id, 'blocked_outside_hours', 'Attempted voting outside school hours');
    } else {
        try {
            $pdo->beginTransaction();

            // Log the voting attempt
            logVotingAttempt($pdo, $student_id, 'vote_started', 'Election ID: ' . $election['id']);

            // Validate that votes are provided for all positions with candidates
            $all_positions = $pdo->query("SELECT * FROM positions ORDER BY priority DESC")->fetchAll();
            foreach ($all_positions as $pos) {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM candidates WHERE position_id = ?");
                $stmt->execute([$pos['id']]);
                $candidate_count = $stmt->fetchColumn();
                
                if ($candidate_count > 0 && !isset($_POST['votes'][$pos['id']])) {
                    throw new Exception("Missing vote for position: " . $pos['title']);
                }
            }

            foreach ($_POST['votes'] as $position_id => $candidate_id) {
                $stmt = $pdo->prepare("INSERT INTO votes (student_id, candidate_id, position_id, election_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$student_id, $candidate_id, $position_id, $election['id']]);
            }

            $pdo->commit();
            logVotingAttempt($pdo, $student_id, 'vote_completed', 'Successfully voted in election ID: ' . $election['id']);
            $has_voted = true;
            $_SESSION['just_voted'] = true;
            redirect('certificate.php');
        } catch (Exception $e) {
            $pdo->rollBack();
            logVotingAttempt($pdo, $student_id, 'vote_failed', 'Error: ' . $e->getMessage());
            $error = "An error occurred while casting your vote. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - KCC Online Voting</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <img src="../assets/images/kcclogo.jpg" alt="KCC Online Voting Logo" class="header-logo">
                <h1>KCC Online Voting</h1>
            </div>
            <div class="user-info">
                <span>Welcome, <?php echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'User'; ?></span>
                <a href="logout.php" class="btn-small">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (!$election): ?>
            <div class="card text-center">
                <h2>No Active Election</h2>
                <p>There are currently no ongoing elections. Please check back later.</p>
            </div>
        <?php elseif ($has_voted): ?>
            <div class="voting-certificate">
                <!-- Certificate Border -->
                <div class="certificate-border">
                    <div class="certificate-inner">
                        <!-- Header Section -->
                        <div class="certificate-header">
                            <div class="certificate-logo">
                                <img src="../assets/images/kcclogo.jpg" alt="KCC Logo" class="cert-logo">
                            </div>
                            <div class="certificate-title-section">
                                <h1 class="certificate-main-title">KCC Online Voting System</h1>
                                <div class="certificate-subtitle">Certificate of Participation</div>
                            </div>
                        </div>

                        <!-- Certificate Body -->
                        <div class="certificate-body">
                            <div class="certificate-content">
                                <div class="certification-text">
                                    <p class="cert-text-line">This is to certify that</p>
                                    <h2 class="student-name"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Valued Student'); ?></h2>
                                    <p class="cert-text-line">has successfully participated in the democratic process by</p>
                                    <p class="cert-text-line">casting their vote in the</p>
                                    <h3 class="election-title"><?php echo htmlspecialchars($election['title']); ?></h3>
                                </div>

                                <div class="certificate-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Student ID:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($_SESSION['student_id'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Date:</span>
                                        <span class="detail-value"><?php echo date('F j, Y'); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Time:</span>
                                        <span class="detail-value"><?php echo date('g:i A'); ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Decorative Elements -->
                            <div class="certificate-decorations">
                                <div class="decoration-left">
                                    <div class="ornamental-line"></div>
                                    <div class="ornamental-star">⭐</div>
                                    <div class="ornamental-line"></div>
                                </div>
                                <div class="certificate-seal">
                                    <div class="seal-container">
                                        <div class="seal-circle">
                                            <div class="seal-content">
                                                <div class="seal-icon">✓</div>
                                                <div class="seal-text">VOTED</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="decoration-right">
                                    <div class="ornamental-line"></div>
                                    <div class="ornamental-star">⭐</div>
                                    <div class="ornamental-line"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Section -->
                        <div class="certificate-footer">
                            <div class="footer-message">
                                <p>Your vote has been recorded and counted. Election results will be announced after the voting period closes.</p>
                            </div>
                            <div class="footer-signature">
                                <div class="signature-line"></div>
                                <p class="signature-title">KCC Online Voting System</p>
                                <p class="signature-date"><?php echo date('M j, Y'); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Certificate Corners -->
                    <div class="certificate-corner certificate-corner-tl"></div>
                    <div class="certificate-corner certificate-corner-tr"></div>
                    <div class="certificate-corner certificate-corner-bl"></div>
                    <div class="certificate-corner certificate-corner-br"></div>
                </div>

                <!-- Floating Elements -->
                <div class="certificate-particles">
                    <div class="floating-element elem-1">🎉</div>
                    <div class="floating-element elem-2">⭐</div>
                    <div class="floating-element elem-3">🏆</div>
                    <div class="floating-element elem-4">✨</div>
                    <div class="floating-element elem-5">🎊</div>
                </div>

                <!-- Download Button -->
                <div class="certificate-actions">
                    <button class="btn btn-certificate" onclick="printCertificate()">
                        <span class="btn-icon">📄</span>
                        Print Certificate
                    </button>
                    <button class="btn btn-share" onclick="shareCertificate()">
                        <span class="btn-icon">📤</span>
                        Share Achievement
                    </button>
                </div>
            </div>
        <?php else: ?>
            <?php if (empty($positions)): ?>
                <div class="card text-center">
                    <h2>No Positions Available for Voting</h2>
                    <p>There are no positions set up for this election yet. Please contact an administrator.</p>
                </div>
            <?php else: ?>
            <form action="index.php" method="POST" id="votingForm">
                <h2><?php echo $election['title']; ?></h2>
                <p class="instruction">Please select one candidate for each position that has candidates.</p>
                
                <?php foreach ($positions as $pos): ?>
                    <div class="voting-section card">
                        <h3><?php echo $pos['title']; ?></h3>
                        <div class="candidates-grid">
                            <?php if (!empty($pos['candidates'])): ?>
                                <?php foreach ($pos['candidates'] as $c): ?>
                                    <label class="candidate-card">
                                        <img src="<?php echo '../' . ($c['photo_url'] ?: 'assets/images/candidates/default.jpg'); ?>" alt="<?php echo $c['full_name']; ?>" class="candidate-photo" onerror="this.src='../assets/images/candidates/default.jpg'">
                                        <div class="candidate-info">
                                            <span class="candidate-name"><?php echo $c['full_name']; ?></span>
                                            <span class="candidate-party"><?php echo $c['party_name']; ?></span>
                                        </div>
                                        <input type="radio" name="votes[<?php echo $pos['id']; ?>]" value="<?php echo $c['id']; ?>" required>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="no-candidates-message">
                                    <p>No candidates available for this position.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-large" onclick="return confirm('Are you sure you want to submit your votes? This action cannot be undone.')">Submit My Votes</button>
                </div>
            </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        function printCertificate() {
            window.print();
        }

        function shareCertificate() {
            if (navigator.share) {
                navigator.share({
                    title: 'KCC Voting Certificate',
                    text: 'I have successfully participated in the KCC Online Voting System!',
                    url: window.location.href
                });
            } else {
                // Fallback for browsers that don't support Web Share API
                const text = 'I have successfully participated in the KCC Online Voting System! 🎉';
                navigator.clipboard.writeText(text).then(() => {
                    alert('Certificate message copied to clipboard! You can now share it on social media.');
                });
            }
        }

        // Add entrance animations for certificate elements
        document.addEventListener('DOMContentLoaded', function() {
            const certificate = document.querySelector('.voting-certificate');
            if (certificate) {
                // Animate certificate elements sequentially
                const elements = certificate.querySelectorAll('.student-name, .election-title, .certificate-seal, .footer-signature');
                elements.forEach((element, index) => {
                    element.style.opacity = '0';
                    element.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        element.style.transition = 'all 0.8s ease-out';
                        element.style.opacity = '1';
                        element.style.transform = 'translateY(0)';
                    }, index * 200);
                });
            }
        });

        // Device fingerprinting for security monitoring
        function collectDeviceFingerprint() {
            const fingerprint = {
                screen_resolution: `${screen.width}x${screen.height}`,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                platform: navigator.platform,
                language: navigator.language,
                cookie_enabled: navigator.cookieEnabled,
                do_not_track: navigator.doNotTrack
            };

            // Send fingerprint data (could be used for additional verification)
            fetch('api/device_fingerprint.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(fingerprint)
            }).catch(err => console.log('Fingerprint collection failed:', err));
        }

        // Collect fingerprint on page load
        collectDeviceFingerprint();
    </script>

    <script src="../assets/js/main.js"></script>
</body>
</html>
