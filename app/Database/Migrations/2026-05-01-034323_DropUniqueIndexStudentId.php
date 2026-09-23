<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropUniqueIndexStudentId extends Migration
{
    public function up()
    {
        $this->forge->dropKey('students', 'uidx_students_student_id');
    }

    public function down()
    {
        $this->forge->addKey('students', 'student_id', false, true);
    }
}
