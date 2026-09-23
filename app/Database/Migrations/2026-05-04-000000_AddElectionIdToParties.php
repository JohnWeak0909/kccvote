<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddElectionIdToParties extends Migration
{
    public function up()
    {
        $this->forge->addColumn('parties', [
            'election_id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'null'           => true,
                'after'          => 'name'
            ],
        ]);

        // Add foreign key constraint
        $this->db->query(
            'ALTER TABLE parties ADD KEY idx_parties_election_id (election_id), 
             ADD CONSTRAINT fk_parties_election FOREIGN KEY (election_id) REFERENCES elections (id) ON DELETE SET NULL ON UPDATE CASCADE'
        );
    }

    public function down()
    {
        // Drop foreign key constraint
        $this->db->query('ALTER TABLE parties DROP FOREIGN KEY fk_parties_election');
        
        $this->forge->dropColumn('parties', 'election_id');
    }
}
