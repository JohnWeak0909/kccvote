<?php

namespace App\Controllers;

use App\Models\StudentModel;
use App\Models\VoteModel;
use App\Models\CandidateModel;
use App\Models\ElectionModel;

class DashboardController extends BaseController
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
            header('Location: ' . base_url('/login?action=admin'));
            exit;
        }

        $this->studentModel = new StudentModel();
        $this->voteModel = new VoteModel();
        $this->candidateModel = new CandidateModel();
        $this->electionModel = new ElectionModel();
    }

    public function index()
    {
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
        $progress = $studentCount ? min(100, round($voteCount / $studentCount * 100)) : 0;

        $data = [
            'student_count' => $studentCount,
            'vote_count' => $voteCount,
            'candidate_count' => $candidateCount,
            'election_status' => $electionStatus,
            'vote_progress' => $progress,
            'admin_name' => session()->get('admin_name')
        ];

        return view('admin/dashboard', $data);
    }
}
