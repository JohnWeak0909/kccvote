<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAccessControlToElections extends Migration
{
    public function up()
    {
        // Add access control fields to elections table
        $this->forge->addColumn('elections', [
            'access_type' => [
                'type'       => 'ENUM',
                'constraint' => ['all', 'specific_departments', 'specific_students'],
                'default'    => 'all',
                'after'      => 'end_time'
            ],
            'allowed_departments' => [
                'type'    => 'LONGTEXT',
                'null'    => true,
                'comment' => 'JSON array of department names',
                'after'   => 'access_type'
            ],
            'allowed_students' => [
                'type'    => 'LONGTEXT',
                'null'    => true,
                'comment' => 'JSON array of student IDs',
                'after'   => 'allowed_departments'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('elections', ['access_type', 'allowed_departments', 'allowed_students']);
    }
}
