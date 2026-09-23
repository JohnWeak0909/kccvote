<?php

namespace App\Controllers;

use App\Models\StudentModel;
use App\Models\VoteModel;
use App\Models\CandidateModel;
use App\Models\ElectionModel;

class ApiController extends BaseController
{
    protected $studentModel;
    protected $voteModel;
    protected $candidateModel;
    protected $electionModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        if (session()->get('role') !== 'admin') {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $this->studentModel = new StudentModel();
        $this->voteModel = new VoteModel();
        $this->candidateModel = new CandidateModel();
        $this->electionModel = new ElectionModel();
    }

    public function getStats()
    {
        if ($this->request->getMethod() !== 'get') {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
        }

        $studentCount = $this->studentModel->countAllResults();
        $voteCount = $this->voteModel->countAllResults();
        $candidateCount = $this->candidateModel->countAllResults();

        $election = $this->electionModel->where('status', 'open')
            ->orderBy('start_time', 'DESC')
            ->first();

        if (! $election) {
            $election = $this->electionModel->orderBy('id', 'DESC')->first();
        }

        $electionStatus = $election ? strtoupper($election['status']) : 'NONE';
        $voteProgress = $studentCount ? min(100, round($voteCount / $studentCount * 100)) : 0;

        $activities = $this->buildRecentActivity();
        $systemStatus = $this->buildSystemStatus($electionStatus);

        return $this->response->setJSON([
            'students' => $studentCount,
            'votes' => $voteCount,
            'candidates' => $candidateCount,
            'election_status' => $electionStatus,
            'vote_progress' => $voteProgress,
            'activities' => $activities,
            'system_status' => $systemStatus,
        ]);
    }

    private function buildRecentActivity()
    {
        $db = \Config\Database::connect();

        $voteEvents = $db->table('votes v')
            ->select("v.created_at as created_at, CONCAT(s.full_name, ' voted for ', c.full_name) as description")
            ->join('students s', 's.id = v.student_id')
            ->join('candidates c', 'c.id = v.candidate_id')
            ->orderBy('v.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $studentEvents = $db->table('students')
            ->select("created_at as created_at, CONCAT(full_name, ' joined the system') as description")
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $electionEvents = $db->table('elections')
            ->select("created_at as created_at, CONCAT('Election ', title, ' is ', UPPER(status)) as description")
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $activities = array_merge($voteEvents, $studentEvents, $electionEvents);

        usort($activities, function ($a, $b) {
            return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        });

        return array_slice($activities, 0, 5);
    }

    private function buildSystemStatus(string $electionStatus)
    {
        $status = [
            'database' => ['label' => 'Database', 'value' => 'OK', 'detail' => 'Connected'],
            'voting_system' => ['label' => 'Voting System', 'value' => $electionStatus === 'OPEN' ? 'OPEN' : 'CLOSED', 'detail' => $electionStatus === 'OPEN' ? 'Election open' : 'No active election'],
            'face_recognition' => ['label' => 'Face Recognition', 'value' => 'FACE API.JS', 'detail' => 'Ready in browser'],
            'backup' => ['label' => 'Backup', 'value' => 'UNKNOWN', 'detail' => $this->getBackupTimestamp()],
        ];

        try {
            $db = \Config\Database::connect();
            $db->simpleQuery('SELECT 1');
        } catch (\Throwable $e) {
            $status['database'] = ['label' => 'Database', 'value' => 'DOWN', 'detail' => 'Connection failed'];
        }

        return $status;
    }

    private function getBackupTimestamp()
    {
        $backupPath = WRITEPATH . 'backup';

        if (! is_dir($backupPath)) {
            return 'No backup directory';
        }

        $files = glob($backupPath . DIRECTORY_SEPARATOR . '*');
        if (empty($files)) {
            return 'No backup files';
        }

        $latest = array_reduce($files, function ($carry, $item) {
            if (! is_file($item)) {
                return $carry;
            }
            return $carry === null || filemtime($item) > filemtime($carry) ? $item : $carry;
        });

        return $latest ? date('M d, Y H:i:s', filemtime($latest)) : 'Unavailable';
    }
}
