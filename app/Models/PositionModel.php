<?php

namespace App\Models;

use CodeIgniter\Model;

class PositionModel extends Model
{
    protected $table      = 'positions';
    protected $primaryKey = 'id';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'election_id',
        'title',
        'sort_order',
        'description',
        'votes_required',
        'status',
    ];

    protected $validationRules = [
        'election_id'   => 'permit_empty|is_natural_no_zero',
        'title'         => 'required|min_length[2]',
        'sort_order'    => 'required|is_natural',
        'description'   => 'permit_empty',
        'votes_required' => 'required|is_natural',
        'status'        => 'permit_empty|in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'title' => ['required' => 'A position title is required.', 'min_length' => 'The position title must be at least 2 characters.'],
        'sort_order' => ['required' => 'Sort order is required.', 'is_natural' => 'Sort order must be a valid number.'],
        'votes_required' => ['required' => 'Votes required is required.', 'is_natural' => 'Votes required must be a valid number.'],
        'status' => ['in_list' => 'Status must be active or inactive.'],
    ];

    public function getByElection(int $electionId, bool $includeInactive = false)
    {
        $builder = $this->where('(election_id = ' . $this->db->escape($electionId) . ' OR election_id IS NULL)');
        if (! $includeInactive) {
            $builder->where('status', 'active');
        }
        return $builder->orderBy('sort_order', 'ASC')
                       ->orderBy('id', 'ASC')
                       ->findAll();
    }

    public function findForElection(int $positionId, int $electionId)
    {
        return $this->where('id', $positionId)
                    ->groupStart()
                        ->where('election_id', $electionId)
                        ->orWhere('election_id IS NULL')
                    ->groupEnd()
                    ->first();
    }

    public function countByElection(int $electionId): int
    {
        return (int) $this->groupStart()
                              ->where('election_id', $electionId)
                              ->orWhere('election_id IS NULL')
                          ->groupEnd()
                          ->countAllResults();
    }

    public function countCandidates(int $positionId, ?int $electionId = null): int
    {
        $builder = $this->db->table('candidates')
                            ->where('position_id', $positionId);
        if ($electionId !== null) {
            $builder->where('election_id', $electionId);
        }
        return (int) $builder->countAllResults();
    }

    public function hasCandidates(int $positionId, ?int $electionId = null): bool
    {
        return $this->countCandidates($positionId, $electionId) > 0;
    }
}
