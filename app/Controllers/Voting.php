<?php

namespace App\Controllers;

use App\Models\ElectionModel;
use App\Models\VoteModel;

class Voting extends BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        if (session()->get('role') !== 'student') {
            $session = session();
            $session->setFlashdata('error', 'Please log in to access the student portal.');
            $redirectUrl = base_url('/login?action=student');
            header('Location: ' . $redirectUrl);
            exit;
        }
    }

    protected function checkLocationAndLogoutIfNeeded()
    {
        $session = session();
        $currentLatitude = $this->request->getPost('latitude') ?? $this->request->getGet('latitude');
        $currentLongitude = $this->request->getPost('longitude') ?? $this->request->getGet('longitude');
        
        $latValue = is_numeric($currentLatitude) ? floatval($currentLatitude) : null;
        $lngValue = is_numeric($currentLongitude) ? floatval($currentLongitude) : null;

        // If location is provided, check if still within range
        if ($latValue !== null && $lngValue !== null) {
            if (! $this->isWithinCampusRange($latValue, $lngValue)) {
                // Student moved outside campus range - clear auth keys only (keep flashdata session)
                $session->remove(['user_id', 'student_id', 'full_name', 'username', 'role', 'isLoggedIn', 'login_latitude', 'login_longitude', 'temp_user_id', 'temp_user_type', 'temp_user_name', 'temp_student_id', 'temp_face_descriptor']);
                return redirect()->to('/login?action=student')->with('error', 'Your location is outside the permitted campus access radius. You have been logged out for security.');
            }
        }

        return null;
    }

    public function index()
    {
        // Check location and auto-logout if needed
        $locationCheck = $this->checkLocationAndLogoutIfNeeded();
        if ($locationCheck) {
            return $locationCheck;
        }

        $session = session();
        $studentId = $session->get('user_id');
        $studentModel = new \App\Models\StudentModel();
        $electionModel = new ElectionModel();

        // Get student's department
        $student = $studentModel->find($studentId);
        $isInactive = $student && (($student['status'] ?? 'Inactive') !== 'Active' || empty($student['is_active']));
        if (!$student || $isInactive) {
            $session->remove(['user_id', 'student_id', 'full_name', 'username', 'role', 'isLoggedIn', 'login_latitude', 'login_longitude']);
            return redirect()->to('/login?action=student')->with('error', 'Your account is inactive and cannot vote. Please contact the administrator.');
        }

        $needsFaceRegistration = empty($student['face_descriptor']) && empty($student['face_registered']);

        $studentDepartment = $student['department'] ?? null;

        // Get only accessible elections for this student
        $allOpenElections = $electionModel->getOpenElections();
        $accessibleElections = [];
        
        foreach ($allOpenElections as $election) {
            if ($electionModel->canStudentAccess($election['id'], $studentDepartment, $studentId)) {
                $accessibleElections[] = $election;
            }
        }

        // Get campus settings for location verification
        $campusSettings = $this->getCampusSettings();

        return view('voting/home', [
            'elections' => $accessibleElections,
            'selectedElection' => null,
            'candidates' => [],
            'hasVoted' => false,
            'receipt' => [],
            'results' => [],
            'full_name' => $session->get('full_name'),
            'campus_latitude' => $campusSettings['campus_latitude'],
            'campus_longitude' => $campusSettings['campus_longitude'],
            'campus_radius' => $campusSettings['campus_access_radius'],
            'needs_face_registration' => $needsFaceRegistration,
            'show_completion_receipt' => false
        ]);
    }

    public function show($electionId)
    {
        // Check location and auto-logout if needed
        $locationCheck = $this->checkLocationAndLogoutIfNeeded();
        if ($locationCheck) {
            return $locationCheck;
        }

        $session = session();
        $studentId = $session->get('user_id');
        $studentModel = new \App\Models\StudentModel();
        $electionModel = new ElectionModel();
        $voteModel = new VoteModel();

        $election = $electionModel->find($electionId);
        if (! $election || $election['status'] !== 'open') {
            return redirect()->to('/voting')->with('error', 'Selected election is not available.');
        }

        // Get student's department
        $student = $studentModel->find($studentId);
        $isInactive = $student && (($student['status'] ?? 'Inactive') !== 'Active' || empty($student['is_active']));
        if (!$student || $isInactive) {
            $session->remove(['user_id', 'student_id', 'full_name', 'username', 'role', 'isLoggedIn', 'login_latitude', 'login_longitude']);
            return redirect()->to('/login?action=student')->with('error', 'Your account is inactive and cannot vote. Please contact the administrator.');
        }

        $needsFaceRegistration = empty($student['face_descriptor']) && empty($student['face_registered']);

        $studentDepartment = $student['department'] ?? null;

        // Check if student has access to this election
        if (! $electionModel->canStudentAccess($electionId, $studentDepartment, $studentId)) {
            return redirect()->to('/voting')->with('error', 'You do not have access to this election.');
        }

        $hasVoted = $voteModel->hasCompletedVote($studentId, $electionId);
        $showCompletionReceipt = $this->request->getGet('completed') === '1';
        $candidates = $electionModel->getCandidatesWithDetails($electionId);
        $receipt = $hasVoted ? $voteModel->getReceipt($studentId, $electionId) : [];
        $results = $voteModel->getResults($electionId);

        // Get accessible elections for the sidebar
        $allOpenElections = $electionModel->getOpenElections();
        $accessibleElections = [];
        
        foreach ($allOpenElections as $e) {
            if ($electionModel->canStudentAccess($e['id'], $studentDepartment, $studentId)) {
                $accessibleElections[] = $e;
            }
        }

        // Get campus settings for location verification
        $campusSettings = $this->getCampusSettings();

        return view('voting/home', [
            'elections' => $accessibleElections,
            'selectedElection' => $election,
            'candidates' => $candidates,
            'hasVoted' => $hasVoted,
            'receipt' => $receipt,
            'results' => $results,
            'full_name' => $session->get('full_name'),
            'campus_latitude' => $campusSettings['campus_latitude'],
            'campus_longitude' => $campusSettings['campus_longitude'],
            'campus_radius' => $campusSettings['campus_access_radius'],
            'needs_face_registration' => $needsFaceRegistration
            , 'show_completion_receipt' => $showCompletionReceipt
        ]);
    }

    public function cast()
    {
        // Check location and auto-logout if needed
        $locationCheck = $this->checkLocationAndLogoutIfNeeded();
        if ($locationCheck) {
            return $locationCheck;
        }

        $db = db_connect();
        $voteModel = new VoteModel();
        $electionModel = new ElectionModel();
        $studentModel = new \App\Models\StudentModel();
        $positionModel = new \App\Models\PositionModel();

        $election_id = $this->request->getPost('election_id');
        $votes = $this->request->getPost('votes') ?? [];
        $studentId = session()->get('user_id');

        $election = $electionModel->find($election_id);
        if (! $election || $election['status'] !== 'open') {
            return redirect()->back()->with('error', 'The selected election is not available.');
        }

        // Get student's department
        $student = $studentModel->find($studentId);
        $isInactive = $student && (($student['status'] ?? 'Inactive') !== 'Active' || empty($student['is_active']));
        if (!$student || $isInactive) {
            return redirect()->back()->with('error', 'Your account is inactive and cannot vote.');
        }

        $studentDepartment = $student['department'] ?? null;

        // Check if student has access to this election
        if (! $electionModel->canStudentAccess($election_id, $studentDepartment, $studentId)) {
            return redirect()->back()->with('error', 'You do not have access to this election.');
        }

        if ($voteModel->hasCompletedVote($studentId, $election_id)) {
            return redirect()->back()->with('error', 'You have already voted in this election.');
        }

        if (empty($votes)) {
            return redirect()->back()->with('error', 'Please select at least one candidate.');
        }

        $validCandidateIds = array_map('intval', array_column($electionModel->getCandidatesWithDetails($election_id), 'id'));

        $db->transBegin();

        // Replace an incomplete earlier attempt with this complete ballot.
        if ($voteModel->hasVoted($studentId, $election_id)) {
            $voteModel->where('student_id', $studentId)
                ->where('election_id', $election_id)
                ->delete();
        }

        try {
            foreach ($votes as $position_id => $candidate_ids) {
            // If it's a single candidate, make it an array for consistent handling
            if (!is_array($candidate_ids)) {
                $candidate_ids = [$candidate_ids];
            }

            // Verify the number of selections matches votes_required
            $position = $positionModel->find($position_id);
            if (!$position) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Position not found.');
            }
            // Fallback to 1 if votes_required isn't set or column doesn't exist
            $votes_required = isset($position['votes_required']) ? (int)$position['votes_required'] : 1;
            
            if (count($candidate_ids) !== $votes_required) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Invalid number of selections for position. Required: ' . $votes_required . ', Selected: ' . count($candidate_ids) . '. Position: ' . htmlspecialchars($position['title']));
            }

                foreach ($candidate_ids as $candidate_id) {
                $candidate_id = filter_var($candidate_id, FILTER_VALIDATE_INT);
                if (! in_array($candidate_id, $validCandidateIds, true)) {
                    $db->transRollback();
                    return redirect()->back()->with('error', 'Invalid candidate selection.');
                }

                $inserted = $voteModel->insert([
                    'student_id'   => $studentId,
                    'candidate_id' => $candidate_id,
                    'position_id'  => $position_id,
                    'election_id'  => $election_id
                ]);

                    if ($inserted === false) {
                        $db->transRollback();
                        log_message('error', 'Vote insert failed: ' . json_encode($voteModel->errors()));
                        return redirect()->back()->with('error', 'Your vote could not be saved. Please try again.');
                    }
                }
            }

            if (! $db->transStatus()) {
                $db->transRollback();
                log_message('error', 'Vote transaction failed for student ' . $studentId . ' and election ' . $election_id);
                return redirect()->back()->with('error', 'Your vote could not be saved. Please try again.');
            }

            $db->transCommit();
        } catch (\Throwable $exception) {
            $db->transRollback();
            log_message('error', 'Vote submission exception: ' . $exception->getMessage());
            return redirect()->back()->with('error', 'Your vote could not be saved. Please try again.');
        }

        return redirect()->to('/voting/' . $election_id . '?completed=1')->with('success', 'Your vote has been cast successfully!');
    }

    public function deactivateAfterVote()
    {
        $studentId = session()->get('user_id');
        $electionId = $this->request->getPost('election_id');
        if (!$studentId || session()->get('role') !== 'student') {
            return $this->response->setJSON(['success' => false]);
        }

        if (!$electionId || !(new VoteModel())->hasCompletedVote($studentId, $electionId)) {
            return $this->response->setJSON(['success' => false]);
        }

        $studentModel = new \App\Models\StudentModel();
        $updated = $studentModel->update($studentId, [
            'status' => 'Inactive',
            'is_active' => 0,
        ]);
        session()->destroy();

        return $this->response->setJSON(['success' => (bool)$updated]);
    }

    public function accountSettings()
    {
        $studentModel = new \App\Models\StudentModel();
        $studentId = session()->get('user_id');
        
        $student = $studentModel->find($studentId);
        
        if (!$student) {
            return redirect()->to('/login')->with('error', 'Student not found.');
        }
        
        return view('student/account-settings', [
            'student' => $student
        ]);
    }

    public function updateSettings()
    {
        $studentModel = new \App\Models\StudentModel();
        $studentId = session()->get('user_id');
        
        // Validation
        if (!$this->validate([
            'section' => 'required|string',
            'year_level' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[4]',
            'school_year' => 'required|string',
            'current_password' => 'permit_empty',
            'new_password' => 'permit_empty',
            'confirm_password' => 'permit_empty'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
        }
        
        $section = $this->request->getPost('section');
        $year_level = $this->request->getPost('year_level');
        $school_year = $this->request->getPost('school_year');
        $current_password = $this->request->getPost('current_password');
        $new_password = $this->request->getPost('new_password');
        $confirm_password = $this->request->getPost('confirm_password');
        
        $updateData = [
            'section' => $section,
            'year_level' => $year_level,
            'school_year' => $school_year
        ];
        
        // Handle password change
        if (!empty($new_password) || !empty($confirm_password)) {
            if (empty($current_password)) {
                return redirect()->back()->withInput()->with('error', 'Current password is required to change password.');
            }
            
            if ($new_password !== $confirm_password) {
                return redirect()->back()->withInput()->with('error', 'New passwords do not match.');
            }
            
            if (strlen($new_password) < 6) {
                return redirect()->back()->withInput()->with('error', 'New password must be at least 6 characters.');
            }
            
            // Verify current password
            $student = $studentModel->find($studentId);
            if (!password_verify($current_password, $student['password'])) {
                return redirect()->back()->withInput()->with('error', 'Current password is incorrect.');
            }
            
            // Don't hash here, StudentModel's beforeUpdate event will do it!
            $updateData['password'] = $new_password;
        }
        
        // Update student
        if ($studentModel->update($studentId, $updateData)) {
            return redirect()->to('/voting')->with('success', 'Account settings updated successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update account settings. Please try again.');
        }
    }

    /**
     * Verify student location and logout if outside campus range
     * Called periodically from frontend JavaScript
     */
    public function verifyLocation()
    {
        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');
        
        $latValue = is_numeric($latitude) ? floatval($latitude) : null;
        $lngValue = is_numeric($longitude) ? floatval($longitude) : null;

        $settings = $this->getCampusSettings();
        
        // If campus settings not configured, always allow
        if ($settings['campus_access_radius'] === '' || $settings['campus_latitude'] === '' || $settings['campus_longitude'] === '') {
            return $this->response->setJSON(['outside_range' => false, 'message' => 'Campus settings not configured']);
        }

        // If no coordinates provided, don't logout
        if ($latValue === null || $lngValue === null) {
            return $this->response->setJSON(['outside_range' => false, 'message' => 'Location not available']);
        }

        // Check if within range using parent class method
        if (! $this->isWithinCampusRange($latValue, $lngValue)) {
            // Student is outside campus range - logout
            $session = session();
            $session->destroy();
            return $this->response->setJSON(['outside_range' => true, 'message' => 'You are outside the campus range and have been logged out']);
        }

        return $this->response->setJSON(['outside_range' => false, 'message' => 'Location verified - within range']);
    }
}
