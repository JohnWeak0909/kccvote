<?php

namespace App\Models;

use CodeIgniter\Model;

class CandidateModel extends Model
{
    protected $table      = 'candidates';
    protected $primaryKey = 'id';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'student_id',
        'full_name',
        'party_id',
        'position_id',
        'election_id',
        'photo',
        'status',
    ];

    protected $validationRules = [
        'student_id'  => 'permit_empty|max_length[50]',
        'full_name'   => 'required|min_length[2]',
        'party_id'    => 'permit_empty|is_natural_no_zero',
        'position_id' => 'required|is_natural_no_zero',
        'election_id' => 'required|is_natural_no_zero',
        'photo'       => 'permit_empty|max_length[255]',
        'status'      => 'permit_empty|in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'full_name' => ['required' => 'Candidate full name is required.', 'min_length' => 'Candidate full name must be at least 2 characters.'],
        'position_id' => ['required' => 'Position is required.', 'is_natural_no_zero' => 'Please select a valid position.'],
        'election_id' => ['required' => 'Election is required.', 'is_natural_no_zero' => 'Please select a valid election.'],
        'party_id' => ['is_natural_no_zero' => 'Please select a valid party.'],
        'status' => ['in_list' => 'Status must be active or inactive.'],
    ];

    public function getByElection(int $electionId, bool $includeInactive = false)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('candidates c');
        $builder->select('c.*, s.full_name as student_full_name, s.student_id as s_student_id, s.department as student_department,
                          p.name as party_name, p.acronym as party_acronym,
                          pos.title as position_title, pos.sort_order, pos.votes_required');
        $builder->join('students s', 's.student_id = c.student_id OR s.id = CAST(c.student_id AS UNSIGNED)', 'left');
        $builder->join('parties p', 'p.id = c.party_id', 'left');
        $builder->join('positions pos', 'pos.id = c.position_id', 'left');
        $builder->where('c.election_id', $electionId);
        if (! $includeInactive) {
            $builder->where('c.status', 'active');
        }
        $builder->orderBy('pos.sort_order', 'ASC');
        $builder->orderBy('c.full_name', 'ASC');
        return $builder->get()->getResultArray();
    }

    public function findForElection(int $candidateId, int $electionId)
    {
        return $this->where('id', $candidateId)
                    ->where('election_id', $electionId)
                    ->first();
    }

    public function countByElection(int $electionId): int
    {
        return (int) $this->where('election_id', $electionId)
                           ->countAllResults();
    }

    public function isDuplicateStudent(int $electionId, $studentId, ?int $excludeCandidateId = null): bool
    {
        if (empty($studentId)) {
            return false;
        }
        $builder = $this->where('election_id', $electionId)
                        ->groupStart()
                            ->where('student_id', $studentId)
                        ->groupEnd();
        if ($excludeCandidateId !== null) {
            $builder->where('id !=', $excludeCandidateId);
        }
        return (int) $builder->countAllResults() > 0;
    }

    public function isDuplicateName(int $electionId, string $fullName, ?int $excludeCandidateId = null): bool
    {
        if (trim($fullName) === '') {
            return false;
        }
        $builder = $this->where('election_id', $electionId)
                        ->where('LOWER(full_name)', strtolower(trim($fullName)));
        if ($excludeCandidateId !== null) {
            $builder->where('id !=', $excludeCandidateId);
        }
        return (int) $builder->countAllResults() > 0;
    }
}
