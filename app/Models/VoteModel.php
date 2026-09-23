<?php

namespace App\Models;

use CodeIgniter\Model;

class VoteModel extends Model
{
    protected $table      = 'votes';
    protected $primaryKey = 'id';

    protected $allowedFields = ['student_id', 'candidate_id', 'position_id', 'election_id'];

    public function hasVoted($student_id, $election_id)
    {
        return $this->where('student_id', $student_id)
                    ->where('election_id', $election_id)
                    ->countAllResults() > 0;
    }

    public function hasCompletedVote($student_id, $election_id)
    {
        $db = \Config\Database::connect();
        $positions = $db->table('positions p')
            ->select('p.id, p.votes_required')
            ->join('candidates c', 'c.position_id = p.id AND c.election_id = ' . $db->escape($election_id), 'inner')
            ->groupBy(['p.id', 'p.votes_required'])
            ->get()
            ->getResultArray();

        if (empty($positions)) {
            return false;
        }

        $votes = $this->select('position_id, COUNT(*) as vote_count')
            ->where('student_id', $student_id)
            ->where('election_id', $election_id)
            ->groupBy('position_id')
            ->findAll();
        $votesByPosition = array_column($votes, 'vote_count', 'position_id');

        foreach ($positions as $position) {
            if ((int)($votesByPosition[$position['id']] ?? 0) !== (int)$position['votes_required']) {
                return false;
            }
        }

        return true;
    }

    public function getResults($election_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('candidates c');
        $builder->select('c.full_name, pos.title as position, COALESCE(COUNT(v.id), 0) as vote_count, pos.sort_order');
        $builder->join('positions pos', 'pos.id = c.position_id');
        $builder->join('votes v', 'v.candidate_id = c.id AND v.election_id = ' . $db->escape($election_id), 'left');
        $builder->where('c.election_id', $election_id);
        $builder->groupBy('c.id');
        $builder->orderBy('pos.sort_order', 'ASC');
        $builder->orderBy('vote_count', 'DESC');
        return $builder->get()->getResultArray();
    }

    public function getReceipt($student_id, $election_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('votes v');
        $builder->select('pos.title as position, c.full_name as candidate, COALESCE(p.name, "Independent") as party, v.created_at');
        $builder->join('candidates c', 'c.id = v.candidate_id');
        $builder->join('positions pos', 'pos.id = v.position_id');
        $builder->join('parties p', 'p.id = c.party_id', 'left');
        $builder->where('v.student_id', $student_id);
        $builder->where('v.election_id', $election_id);
        $builder->orderBy('pos.sort_order', 'ASC');
        return $builder->get()->getResultArray();
    }

    public function getVotedStudentsWithDetails($election_id, $date = null)
    {
        $db = \Config\Database::connect();
        $date = $date ?? date('Y-m-d');
        
        $sql = "SELECT DISTINCT 
                    v.student_id, 
                    s.full_name, 
                    s.student_id as student_number, 
                    s.department, 
                    s.course, 
                    s.year_level, 
                    s.section, 
                    s.school_year, 
                    a.date, 
                    a.status, 
                    v.created_at as vote_time,
                    e.title as election_title,
                    e.id as election_id
                FROM votes v
                INNER JOIN students s ON s.id = v.student_id
                LEFT JOIN attendance a ON a.student_id = v.student_id AND a.date = ?
                INNER JOIN elections e ON e.id = v.election_id
                WHERE v.election_id = ?
                ORDER BY s.department ASC, s.course ASC, s.school_year ASC, s.year_level ASC, s.section ASC, s.full_name ASC";
        
        $query = $db->query($sql, [$date, $election_id]);
        return $query->getResultArray();
    }
}
