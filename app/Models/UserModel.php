<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'students';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'student_id', 'full_name', 'id_name', 'course', 
        'school_year', 'year_level', 'username', 
        'password', 'face_descriptor', 'id_photo', 'id_photo_hash'
    ];

    protected $useTimestamps = false;

    /**
     * Get student by username
     */
    public function getStudent($identifier)
    {
        // Try exact match first (student_id or username)
        $found = $this->groupStart()
            ->where('student_id', $identifier)
            ->orWhere('username', $identifier)
            ->groupEnd()
            ->first();

        if ($found) {
            return $found;
        }

        // If not found, try matching by digits-only version of student_id
        $clean = preg_replace('/\D+/', '', (string) $identifier);
        if ($clean !== '') {
            $db = \Config\Database::connect();
            $sql = "SELECT * FROM `students` WHERE REPLACE(REPLACE(student_id, '-', ''), ' ', '') = ? LIMIT 1";
            $row = $db->query($sql, [$clean])->getRowArray();
            if ($row) return $row;
        }

        return null;
    }

    /**
     * Verify student login
     */
    public function verifyStudent($identifier, $password)
    {
        $user = $this->getStudent($identifier);
        if ($user) {
            $studentModel = new StudentModel();
            if ($studentModel->verifyPassword($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }

    /**
     * Get admin by username
     */
    public function getAdmin($username)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('admins');
        return $builder->where('username', $username)->get()->getRowArray();
    }

    /**
     * Verify admin login
     */
    public function verifyAdmin($username, $password)
    {
        $admin = $this->getAdmin($username);
        if ($admin) {
            if (password_verify($password, $admin['password'])) {
                return $admin;
            }

            if (is_string($admin['password'] ?? null) && $admin['password'] === $password) {
                return $admin;
            }
        }
        return false;
    }
}
