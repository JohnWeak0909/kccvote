<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDepartmentToStudents extends Migration
{
    public function up()
    {
        $this->forge->addColumn('students', [
            'department' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'after' => 'id_name',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('students', 'department');
    }
}
