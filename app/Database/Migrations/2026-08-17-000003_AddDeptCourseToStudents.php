<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeptCourseToStudents extends Migration
{
    public function up()
    {
        $fields = [
            'department_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'course_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ];

        $this->forge->addColumn('students', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('students', 'department_id');
        $this->forge->dropColumn('students', 'course_id');
    }
}
