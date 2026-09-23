<?php

namespace App\Models;

use CodeIgniter\Model;

class ElectionModel extends Model
{
    protected $table      = 'elections';
    protected $primaryKey = 'id';

    protected $allowedFields = ['title', 'description', 'academic_year', 'status', 'start_time', 'end_time', 'access_type', 'allowed_departments', 'allowed_students'];

    protected $validationRules = [
        'title' => 'required|min_length[3]',
        'description' => 'permit_empty|max_length[2000]',
        'academic_year' => 'permit_empty|max_length[32]',
        'status' => 'required|in_list[pending,open,closed,draft,active,completed,inactive]',
        'start_time' => 'permit_empty|valid_date[Y-m-d H:i:s]',
        'end_time' => 'permit_empty|valid_date[Y-m-d H:i:s]',
    ];

    protected $validationMessages = [
        'title' => ['required' => 'Title is required.', 'min_length' => 'Title must be at least 3 characters.'],
        'description' => ['max_length' => 'Description must be 2000 characters or less.'],
        'academic_year' => ['max_length' => 'Academic year must be 32 characters or less.'],
        'status' => ['required' => 'Status is required.', 'in_list' => 'Status must be draft, active, completed, inactive, pending, open, or closed.'],
        'start_time' => ['valid_date' => 'Start time must be a valid datetime.'],
        'end_time' => ['valid_date' => 'End time must be a valid datetime.'],
    ];

    public function getActiveElection()
    {
        return $this->where('status', 'open')
                    ->orderBy('id', 'DESC')
                    ->first();
    }

    public function getOpenElections()
    {
        return $this->where('status', 'open')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getCandidatesWithDetails($electionId = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('candidates c');
        $builder->select('c.*, p.name as party_name, pos.title as position_title, e.title as election_title, pos.sort_order, pos.votes_required');
        $builder->join('parties p', 'p.id = c.party_id', 'left');
        $builder->join('positions pos', 'pos.id = c.position_id', 'left');
        $builder->join('elections e', 'e.id = c.election_id', 'left');

        if ($electionId !== null) {
            $builder->where('c.election_id', $electionId);
        }

        $builder->orderBy('pos.sort_order', 'ASC');
        return $builder->get()->getResultArray();
    }

    public function canStudentAccess($electionId, $studentDepartment = null, $studentId = null)
    {
        $election = $this->find($electionId);
        
        if (!$election) {
            return false;
        }

        // If access type is 'all', everyone can access
        if ($election['access_type'] === 'all') {
            return true;
        }

        // If access type is 'specific_departments', check department
        if ($election['access_type'] === 'specific_departments') {
            if ($studentDepartment === null) {
                return false;
            }
            $allowed = json_decode($election['allowed_departments'], true) ?? [];
            return in_array($studentDepartment, $allowed, true);
        }

        // If access type is 'specific_students', check student ID
        if ($election['access_type'] === 'specific_students') {
            if ($studentId === null) {
                return false;
            }
            $allowed = json_decode($election['allowed_students'], true) ?? [];
            return in_array($studentId, $allowed, true);
        }

        return false;
    }

    public function getAccessibleElections($studentDepartment = null, $studentId = null)
    {
        $elections = $this->where('status', 'open')->findAll();
        
        $accessible = [];
        foreach ($elections as $election) {
            if ($this->canStudentAccess($election['id'], $studentDepartment, $studentId)) {
                $accessible[] = $election;
            }
        }
        
        return $accessible;
    }
}
