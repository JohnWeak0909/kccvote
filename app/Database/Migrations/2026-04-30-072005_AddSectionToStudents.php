<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSectionToStudents extends Migration
{
    public function up()
    {
        $this->forge->addColumn('students', [
            'section' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'year_level',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('students', 'section');
    }
}
