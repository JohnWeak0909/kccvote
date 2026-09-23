<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table      = 'attendance';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'student_id',
        'date',
        'status',
        'marked_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $validationRules = [
        'student_id' => 'required|is_natural_no_zero',
        'date'       => 'required|valid_date[Y-m-d]',
        'status'     => 'required|in_list[present,absent,late]',
    ];

    protected $validationMessages = [
        'student_id' => [
            'required' => 'Student ID is required.',
            'is_natural_no_zero' => 'Invalid student ID.',
        ],
        'date' => [
            'required' => 'Date is required.',
            'valid_date' => 'Invalid date format.',
        ],
        'status' => [
            'required' => 'Status is required.',
            'in_list' => 'Status must be present, absent, or late.',
        ],
    ];

    public function getAttendanceWithStudent($date = null, $studentId = null)
    {
        $builder = $this->db->table('attendance a');
        $builder->select('a.*, s.full_name, s.student_id as student_number, s.department, s.course, s.school_year, s.year_level, s.section');
        $builder->join('students s', 's.id = a.student_id', 'left');
        if ($date) {
            $builder->where('a.date', $date);
        }
        if ($studentId) {
            $builder->where('a.student_id', $studentId);
        }
        $builder->orderBy('s.department ASC');
        $builder->orderBy('s.course ASC');
        $builder->orderBy('s.school_year ASC');
        $builder->orderBy('s.year_level ASC');
        $builder->orderBy('s.section ASC');
        $builder->orderBy('s.full_name ASC');
        return $builder->get()->getResultArray();
    }
}