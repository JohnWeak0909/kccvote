<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImagePathToAnnouncements extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('image_path', 'announcements')) {
            $this->forge->addColumn('announcements', [
                'image_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('image_path', 'announcements')) {
            $this->forge->dropColumn('announcements', 'image_path');
        }
    }
}
