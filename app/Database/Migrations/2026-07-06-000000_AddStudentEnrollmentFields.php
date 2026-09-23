<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStudentEnrollmentFields extends Migration
{
    public function up()
    {
        $fields = [
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'section'
            ],
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'full_name'
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'first_name'
            ],
            'middle_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'last_name'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Active', 'Inactive'],
                'default' => 'Inactive',
                'after' => 'email'
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'status'
            ],
            'face_registered' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'is_active'
            ],
            'must_change_password' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'after' => 'face_registered'
            ],
            'face_enrolled_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'must_change_password'
            ],
            'failed_login_attempts' => [
                'type' => 'INT',
                'constraint' => 5,
                'default' => 0,
                'after' => 'face_enrolled_at'
            ],
            'locked_until' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'failed_login_attempts'
            ],
            'last_login_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'locked_until'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'last_login_at'
            ],
        ];

        $this->forge->addColumn('students', $fields);
        $this->db->query('ALTER TABLE students ADD INDEX idx_students_status (status)');
        $this->db->query('ALTER TABLE students ADD INDEX idx_students_department_course (department, course)');
    }

    public function down()
    {
        $this->forge->dropColumn('students', ['email', 'first_name', 'last_name', 'middle_name', 'status', 'is_active', 'face_registered', 'must_change_password', 'face_enrolled_at', 'failed_login_attempts', 'locked_until', 'last_login_at']);
    }
}
