<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropUniqueIndexUsername extends Migration
{
    public function up()
    {
        $this->forge->dropKey('students', 'uidx_students_username');
    }

    public function down()
    {
        $this->forge->addKey('students', 'username', false, true);
    }
}
