<?php

namespace App\Controllers\Admin;

use App\Models\AnnouncementModel;
use App\Models\CandidateModel;
use App\Models\ElectionModel;
use App\Models\PartyModel;
use App\Models\PositionModel;
use App\Models\SettingModel;
use App\Models\VoteModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Admin extends \App\Controllers\BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        if (session()->get('role') !== 'admin') {
            header('Location: ' . base_url('/login?action=admin'));
            exit;
        }
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $electionModel = new ElectionModel();

        $data = [
            'student_count'   => $db->table('students')->countAll(),
            'candidate_count' => $db->table('candidates')->countAll(),
            'vote_count'      => $db->table('votes')->countAll(),
            'election'        => $electionModel->orderBy('id', 'DESC')->first(),
            'admin_name'      => session()->get('admin_name')
        ];

        return view('admin/dashboard', $data);
    }

    public function candidates()
    {
        $electionModel = new ElectionModel();
        $data['candidates'] = $electionModel->getCandidatesWithDetails();
        return view('admin/candidates', $data);
    }

    public function createCandidate()
    {
        $data = [
            'mode' => 'create',
            'positions' => (new PositionModel())->findAll(),
            'parties' => (new PartyModel())->findAll(),
            'elections' => (new ElectionModel())->findAll(),
        ];

        return view('admin/candidate_form', $data);
    }

    public function storeCandidate()
    {
        $model = new CandidateModel();
        $data = $this->request->getPost(['full_name', 'student_id', 'party_id', 'position_id', 'election_id', 'status']);
        $redirectTo = $this->request->getPost('redirect_to');

        if (empty($data['full_name']) && ! empty($data['student_id'])) {
            $studentModel = new \App\Models\StudentModel();
            $student = $studentModel->where('student_id', $data['student_id'])->orWhere('id', $data['student_id'])->first();
            if ($student) {
                $data['full_name'] = $student['full_name'] ?? $student['first_name'] . ' ' . ($student['last_name'] ?? '');
            }
        }

        if (! empty($data['election_id']) && ! empty($data['position_id'])) {
            $position = (new PositionModel())->find($data['position_id']);
            if ($position && (int) $position['election_id'] !== (int) $data['election_id']) {
                return redirect()->back()->withInput()->with('error', 'Selected position does not belong to the current election.');
            }
        }

        if (! empty($data['election_id']) && ! empty($data['party_id'])) {
            $party = (new PartyModel())->find($data['party_id']);
            if ($party && (int) $party['election_id'] !== (int) $data['election_id']) {
                return redirect()->back()->withInput()->with('error', 'Selected party does not belong to the current election.');
            }
        }

        if (! empty($data['election_id']) && ! empty($data['student_id'])) {
            if ($model->isDuplicateStudent((int) $data['election_id'], $data['student_id'])) {
                return redirect()->back()->withInput()->with('error', 'This student is already registered as a candidate for this election.');
            }
        }

        $data['party_id'] = empty($data['party_id']) ? null : $data['party_id'];
        $data['election_id'] = empty($data['election_id']) ? null : (int) $data['election_id'];
        $data['position_id'] = empty($data['position_id']) ? null : (int) $data['position_id'];
        $data['status'] = $data['status'] ?? 'active';

        $file = $this->request->getFile('photo');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/candidates';
            if (! is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $data['photo'] = $newName;
        }

        if (! $model->save($data)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unable to create candidate.']);
            }
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'candidate_id' => $model->getInsertID()]);
        }

        if ($redirectTo) {
            return redirect()->to($redirectTo)->with('success', 'Candidate saved successfully.');
        }
        return redirect()->to('/admin/candidates')->with('success', 'Candidate saved successfully.');
    }

    public function editCandidate(int $id)
    {
        $model = new CandidateModel();
        $candidate = $model->find($id);
        if (! $candidate) {
            throw new PageNotFoundException('Candidate not found');
        }

        $data = [
            'mode' => 'edit',
            'candidate' => $candidate,
            'positions' => (new PositionModel())->findAll(),
            'parties' => (new PartyModel())->findAll(),
            'elections' => (new ElectionModel())->findAll(),
        ];

        return view('admin/candidate_form', $data);
    }

    public function updateCandidate(int $id)
    {
        $model = new CandidateModel();
        $data = $this->request->getPost(['full_name', 'student_id', 'party_id', 'position_id', 'election_id', 'status']);
        $data['id'] = $id;
        $redirectTo = $this->request->getPost('redirect_to');

        if (! empty($data['election_id']) && ! empty($data['position_id'])) {
            $position = (new PositionModel())->find($data['position_id']);
            if ($position && (int) $position['election_id'] !== (int) $data['election_id']) {
                return redirect()->back()->withInput()->with('error', 'Selected position does not belong to the current election.');
            }
        }

        if (! empty($data['election_id']) && ! empty($data['party_id'])) {
            $party = (new PartyModel())->find($data['party_id']);
            if ($party && (int) $party['election_id'] !== (int) $data['election_id']) {
                return redirect()->back()->withInput()->with('error', 'Selected party does not belong to the current election.');
            }
        }

        $data['party_id'] = empty($data['party_id']) ? null : $data['party_id'];
        $data['election_id'] = empty($data['election_id']) ? null : (int) $data['election_id'];
        $data['position_id'] = empty($data['position_id']) ? null : (int) $data['position_id'];
        $data['status'] = $data['status'] ?? 'active';

        $file = $this->request->getFile('photo');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/candidates';
            if (! is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $data['photo'] = $newName;
        }

        if (! $model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        if ($redirectTo) {
            return redirect()->to($redirectTo)->with('success', 'Candidate updated successfully.');
        }
        return redirect()->to('/admin/candidates')->with('success', 'Candidate updated successfully.');
    }

    public function deleteCandidate(int $id)
    {
        $model = new CandidateModel();
        $redirectTo = $this->request->getPost('redirect_to');
        if (! $model->delete($id)) {
            return redirect()->back()->with('error', 'Failed to delete candidate.');
        }

        if ($redirectTo) {
            return redirect()->to($redirectTo)->with('success', 'Candidate deleted successfully.');
        }
        return redirect()->to('/admin/candidates')->with('success', 'Candidate deleted successfully.');
    }

    public function elections()
    {
        $electionModel = new ElectionModel();
        $data['elections'] = $electionModel->findAll();
        $data['positions'] = (new PositionModel())->findAll();
        $data['parties'] = (new PartyModel())->getPartiesWithElections();
        return view('admin/elections', $data);
    }

    public function createElection()
    {
        $studentModel = new \App\Models\StudentModel();
        return view('admin/election_form', [
            'mode' => 'create', 
            'election' => [],
            'departments' => $studentModel->getDepartments()
        ]);
    }

    public function editElection(int $id)
    {
        $electionModel = new ElectionModel();
        $positionModel = new PositionModel();
        $partyModel = new PartyModel();
        $candidateModel = new CandidateModel();
        $studentModel = new \App\Models\StudentModel();

        $election = $electionModel->find($id);
        if (! $election) {
            return redirect()->to('/admin/elections')->with('error', 'Election not found.');
        }

        $positions = $positionModel->where('election_id', $id)->orderBy('sort_order', 'ASC')->findAll();
        $parties = $partyModel->where('election_id', $id)->orderBy('name', 'ASC')->findAll();
        $candidates = $candidateModel->where('election_id', $id)->orderBy('full_name', 'ASC')->findAll();

        $students = $studentModel->orderBy('full_name', 'ASC')->findAll();

        return view('admin/election_form', [
            'mode' => 'edit',
            'election' => $election,
            'positions' => $positions,
            'parties' => $parties,
            'candidates' => $candidates,
            'students' => $students,
            'departments' => $studentModel->getDepartments(),
            'stats' => [
                'positions' => count($positions),
                'parties' => count($parties),
                'candidates' => count($candidates),
            ],
        ]);
    }

    public function storeElection()
    {
        $model = new ElectionModel();
        $data = $this->request->getPost(['title', 'description', 'academic_year', 'status', 'start_time', 'end_time', 'access_type']);

        foreach (['start_time', 'end_time'] as $field) {
            if (! empty($data[$field])) {
                $data[$field] = str_replace('T', ' ', $data[$field]) . ':00';
            }
        }

        $data['status'] = $data['status'] ?? 'pending';

        if ($data['access_type'] === 'specific_departments') {
            $departments = $this->request->getPost('allowed_departments');
            $data['allowed_departments'] = json_encode($departments ?? []);
            $data['allowed_students'] = null;
        } elseif ($data['access_type'] === 'specific_students') {
            $students = $this->request->getPost('allowed_students');
            $data['allowed_students'] = json_encode($students ?? []);
            $data['allowed_departments'] = null;
        } else {
            $data['allowed_departments'] = null;
            $data['allowed_students'] = null;
        }

        if (! $model->save($data)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unable to create election.']);
            }
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        $newElectionId = $model->getInsertID();
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'election_id' => $newElectionId]);
        }
        return redirect()->to('/admin/elections')->with('success', 'Election created successfully.');
    }

    public function updateElection(int $id)
    {
        $model = new ElectionModel();
        $data = $this->request->getPost(['title', 'description', 'academic_year', 'status', 'start_time', 'end_time', 'access_type']);
        $data['id'] = $id;

        $statusMap = ['draft' => 'pending', 'active' => 'open', 'completed' => 'closed', 'inactive' => 'pending'];
        if (isset($data['status']) && array_key_exists(strtolower($data['status']), $statusMap)) {
            $data['status'] = $statusMap[strtolower($data['status'])];
        }

        foreach (['start_time', 'end_time'] as $field) {
            if (! empty($data[$field])) {
                $data[$field] = str_replace('T', ' ', $data[$field]) . ':00';
            }
        }

        if ($data['access_type'] === 'specific_departments') {
            $departments = $this->request->getPost('allowed_departments');
            $data['allowed_departments'] = json_encode($departments ?? []);
            $data['allowed_students'] = null;
        } elseif ($data['access_type'] === 'specific_students') {
            $students = $this->request->getPost('allowed_students');
            $data['allowed_students'] = json_encode($students ?? []);
            $data['allowed_departments'] = null;
        } else {
            $data['allowed_departments'] = null;
            $data['allowed_students'] = null;
        }

        if (! $model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/admin/elections/edit/' . $id . '#election-information')->with('success', 'Election updated successfully.');
    }

    public function updateElectionStatus(int $id)
    {
        $model = new ElectionModel();
        $election = $model->find($id);

        if (! $election) {
            throw new PageNotFoundException('Election not found');
        }

        $status = $this->request->getPost('status');
        if (! in_array($status, ['pending', 'open', 'closed'], true)) {
            return redirect()->back()->with('error', 'Invalid election status.');
        }

        if (! $model->save(['id' => $id, 'status' => $status])) {
            return redirect()->back()->with('error', 'Failed to update election status.');
        }

        return redirect()->to('/admin/elections')->with('success', 'Election status updated successfully.');
    }

    public function deleteElection(int $id)
    {
        $model = new ElectionModel();
        if (! $model->delete($id)) {
            return redirect()->back()->with('error', 'Failed to delete election.');
        }

        return redirect()->to('/admin/elections')->with('success', 'Election deleted successfully.');
    }

    public function announcements()
    {
        $model = new AnnouncementModel();
        $data['announcements'] = $model->orderBy('created_at', 'DESC')->findAll();

        return view('admin/announcements', $data);
    }

    public function editAnnouncement(int $id)
    {
        $model = new AnnouncementModel();
        $announcement = $model->find($id);

        if (! $announcement) {
            throw new PageNotFoundException('Announcement not found');
        }

        $data['announcement'] = $announcement;
        $data['announcements'] = $model->orderBy('created_at', 'DESC')->findAll();

        return view('admin/announcements', $data);
    }

    public function storeAnnouncement()
    {
        $model = new AnnouncementModel();
        $data = $this->request->getPost(['title', 'message', 'is_active']);
        $data['is_active'] = $data['is_active'] ?? 0;
        $db = \Config\Database::connect();

        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/announcements';
            if (! is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $imagePath = 'uploads/announcements/' . $newName;

            if ($db->fieldExists('image_path', 'announcements')) {
                $data['image_path'] = $imagePath;
            }
        }

        if (! $model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/admin/announcements')->with('success', 'Announcement posted successfully.');
    }

    public function updateAnnouncement(int $id)
    {
        $model = new AnnouncementModel();
        $data = $this->request->getPost(['title', 'message', 'is_active']);
        $data['id'] = $id;
        $data['is_active'] = $data['is_active'] ?? 0;
        $db = \Config\Database::connect();

        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/announcements';
            if (! is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $imagePath = 'uploads/announcements/' . $newName;

            if ($db->fieldExists('image_path', 'announcements')) {
                $data['image_path'] = $imagePath;
            }
        }

        if (! $model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/admin/announcements')->with('success', 'Announcement updated successfully.');
    }

    public function deleteAnnouncement(int $id)
    {
        $model = new AnnouncementModel();
        if (! $model->delete($id)) {
            return redirect()->back()->with('error', 'Failed to delete announcement.');
        }

        return redirect()->to('/admin/announcements')->with('success', 'Announcement deleted successfully.');
    }

    public function positions()
    {
        $positionModel = new PositionModel();
        $data['positions'] = $positionModel->findAll();
        return view('admin/positions', $data);
    }

    public function createPosition()
    {
        return view('admin/position_form', ['mode' => 'create']);
    }

    public function storePosition()
    {
        $model = new PositionModel();
        $data = $this->request->getPost(['title', 'sort_order', 'description', 'votes_required', 'election_id', 'status']);
        $redirectTo = $this->request->getPost('redirect_to');

        if (empty($data['election_id'])) {
            $data['election_id'] = $this->request->getGet('election_id') ?: null;
        }
        if (empty($data['sort_order'])) {
            $currentMax = $model->where('election_id', $data['election_id'])->selectMax('sort_order')->first();
            $data['sort_order'] = (int) ($currentMax['sort_order'] ?? 0) + 1;
        }
        if (! isset($data['status']) || $data['status'] === '') {
            $data['status'] = 'active';
        }

        if (! $model->save($data)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unable to create position.', 'errors' => $model->errors()]);
            }
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'position_id' => $model->getInsertID()]);
        }

        if ($redirectTo) {
            return redirect()->to($redirectTo)->with('success', 'Position added successfully.');
        }
        return redirect()->to('/admin/positions')->with('success', 'Position added successfully.');
    }

    public function editPosition(int $id)
    {
        $model = new PositionModel();
        $position = $model->find($id);
        if (! $position) {
            throw new PageNotFoundException('Position not found');
        }

        return view('admin/position_form', ['mode' => 'edit', 'position' => $position]);
    }

    public function updatePosition(int $id)
    {
        $model = new PositionModel();
        $data = $this->request->getPost(['title', 'sort_order', 'description', 'votes_required', 'status', 'election_id']);
        $data['id'] = $id;
        $redirectTo = $this->request->getPost('redirect_to');

        if (! empty($data['election_id'])) {
            $position = $model->find($id);
            if ($position && (int) $position['election_id'] !== (int) $data['election_id']) {
                return redirect()->back()->withInput()->with('error', 'You can only edit positions in the current election.');
            }
        }

        if (! $model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        if ($redirectTo) {
            return redirect()->to($redirectTo)->with('success', 'Position updated successfully.');
        }
        return redirect()->to('/admin/positions')->with('success', 'Position updated successfully.');
    }

    public function deletePosition(int $id)
    {
        $model = new PositionModel();
        $position = $model->find($id);
        $redirectTo = $this->request->getPost('redirect_to');

        if (! $position) {
            return redirect()->back()->with('error', 'Position not found.');
        }

        if ((new CandidateModel())->where('position_id', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'This position has candidates assigned to it. Please remove or reassign the candidates before deleting this position.');
        }

        if (! $model->delete($id)) {
            return redirect()->back()->with('error', 'Failed to delete position.');
        }

        if ($redirectTo) {
            return redirect()->to($redirectTo)->with('success', 'Position deleted successfully.');
        }
        return redirect()->to('/admin/positions')->with('success', 'Position deleted successfully.');
    }

    public function parties()
    {
        $partyModel = new PartyModel();
        $data['parties'] = $partyModel->getPartiesWithElections();
        return view('admin/parties', $data);
    }

    public function createParty()
    {
        $electionModel = new \App\Models\ElectionModel();
        $elections = $electionModel->findAll();
        return view('admin/party_form', ['mode' => 'create', 'elections' => $elections, 'party' => []]);
    }

    public function storeParty()
    {
        $model = new PartyModel();
        $data = $this->request->getPost(['name', 'acronym', 'description', 'election_id', 'status', 'logo']);
        $redirectTo = $this->request->getPost('redirect_to');

        if (empty($data['election_id'])) {
            $data['election_id'] = $this->request->getGet('election_id') ?: null;
        }
        if (! isset($data['status']) || $data['status'] === '') {
            $data['status'] = 'active';
        }

        $file = $this->request->getFile('logo');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/parties';
            if (! is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $data['logo'] = $newName;
        }

        if (! $model->save($data)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unable to create party.']);
            }
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'party_id' => $model->getInsertID()]);
        }

        if ($redirectTo) {
            return redirect()->to($redirectTo)->with('success', 'Party saved successfully.');
        }
        return redirect()->to('/admin/parties')->with('success', 'Party saved successfully.');
    }

    public function editParty(int $id)
    {
        $model = new PartyModel();
        $party = $model->find($id);
        if (! $party) {
            throw new PageNotFoundException('Party not found');
        }

        $electionModel = new \App\Models\ElectionModel();
        $elections = $electionModel->findAll();

        return view('admin/party_form', ['mode' => 'edit', 'party' => $party, 'elections' => $elections]);
    }

    public function updateParty(int $id)
    {
        $model = new PartyModel();
        $data = $this->request->getPost(['name', 'acronym', 'description', 'election_id', 'status', 'logo']);
        $data['id'] = $id;
        $redirectTo = $this->request->getPost('redirect_to');

        if (! empty($data['election_id'])) {
            $party = $model->find($id);
            if ($party && (int) $party['election_id'] !== (int) $data['election_id']) {
                return redirect()->back()->withInput()->with('error', 'You can only edit parties in the current election.');
            }
        }

        $file = $this->request->getFile('logo');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/parties';
            if (! is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $data['logo'] = $newName;
        }

        if (! $model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        if ($redirectTo) {
            return redirect()->to($redirectTo)->with('success', 'Party updated successfully.');
        }
        return redirect()->to('/admin/parties')->with('success', 'Party updated successfully.');
    }

    public function deleteParty(int $id)
    {
        $model = new PartyModel();
        $party = $model->find($id);
        $redirectTo = $this->request->getPost('redirect_to');

        if (! $party) {
            return redirect()->back()->with('error', 'Party not found.');
        }

        if ((new CandidateModel())->where('party_id', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'This party has candidates assigned to it. Please remove or reassign the candidates before deleting the party.');
        }

        if (! $model->delete($id)) {
            return redirect()->back()->with('error', 'Failed to delete party.');
        }

        if ($redirectTo) {
            return redirect()->to($redirectTo)->with('success', 'Party deleted successfully.');
        }
        return redirect()->to('/admin/parties')->with('success', 'Party deleted successfully.');
    }

    public function admins()
    {
        $db = \Config\Database::connect();
        $data['admins'] = $db->table('admins')
            ->select('id, username, full_name, created_at')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/admins', $data);
    }

    public function createAdmin()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        $data = $this->request->getJSON();
        
        // Validate required fields
        if (empty($data->username) || empty($data->full_name) || empty($data->password)) {
            return $this->response->setJSON(['success' => false, 'message' => 'All fields are required']);
        }

        // Validate passwords match
        if ($data->password !== $data->password_confirm) {
            return $this->response->setJSON(['success' => false, 'message' => 'Passwords do not match']);
        }

        // Check if username already exists
        $db = \Config\Database::connect();
        $existingAdmin = $db->table('admins')
            ->where('username', $data->username)
            ->countAllResults();

        if ($existingAdmin > 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Username already exists']);
        }

        // Hash password
        $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);

        // Insert new admin
        try {
            $result = $db->table('admins')->insert([
                'username' => $data->username,
                'full_name' => $data->full_name,
                'password' => $hashedPassword,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if ($result) {
                return $this->response->setJSON(['success' => true, 'message' => 'Admin created successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to create admin']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public function security()
    {
        $settingModel = new SettingModel();

        $data = [
            'campus_latitude' => $settingModel->getValue('campus_latitude', ''),
            'campus_longitude' => $settingModel->getValue('campus_longitude', ''),
            'campus_access_radius' => $settingModel->getValue('campus_access_radius', ''),
        ];

        return view('admin/security', $data);
    }

    public function updateSecurity()
    {
        $settingModel = new SettingModel();

        $latitude = $this->request->getPost('campus_latitude');
        $longitude = $this->request->getPost('campus_longitude');
        $radius = $this->request->getPost('campus_access_radius');

        if ($radius !== '' && ! is_numeric($radius)) {
            return redirect()->back()->withInput()->with('error', 'Please enter a valid numeric radius in meters.');
        }

        if ($latitude !== '' && ! is_numeric($latitude)) {
            return redirect()->back()->withInput()->with('error', 'Please enter a valid campus latitude.');
        }

        if ($longitude !== '' && ! is_numeric($longitude)) {
            return redirect()->back()->withInput()->with('error', 'Please enter a valid campus longitude.');
        }

        if ($radius !== '' && ($latitude === '' || $longitude === '')) {
            return redirect()->back()->withInput()->with('error', 'Campus latitude and longitude are required when an access radius is set.');
        }

        $settingModel->setValue('campus_latitude', $latitude);
        $settingModel->setValue('campus_longitude', $longitude);
        $settingModel->setValue('campus_access_radius', $radius);

        return redirect()->to('/admin/security')->with('success', 'Campus access settings saved successfully.');
    }

    public function results()
    {
        $electionModel = new ElectionModel();
        $voteModel = new VoteModel();

        $selectedElectionId = $this->request->getGet('election_id') ?? null;
        $elections = $electionModel->findAll();
        
        if ($selectedElectionId) {
            $election = $electionModel->find($selectedElectionId);
        } else {
            $election = $electionModel->where('status', 'closed')->orderBy('id', 'DESC')->first();
            if (empty($election)) {
                $election = $electionModel->orderBy('id', 'DESC')->first();
            }
        }
        
        $data['elections'] = $elections;
        $data['election'] = $election;
        $data['selectedElectionId'] = $election ? $election['id'] : null;
        $data['results'] = $election ? $voteModel->getResults($election['id']) : [];

        return view('admin/results', $data);
    }
}