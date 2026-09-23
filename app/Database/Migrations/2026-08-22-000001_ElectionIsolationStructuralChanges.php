<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ElectionIsolationStructuralChanges extends Migration
{
    public function up()
    {
        // ============================================================
        // 1. POSITIONS TABLE: add election_id + status fields
        // ============================================================
        if (! $this->db->fieldExists('election_id', 'positions')) {
            $this->forge->addColumn('positions', [
                'election_id' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
            $this->db->query(
                'ALTER TABLE positions ADD KEY idx_positions_election_id (election_id),
                 ADD CONSTRAINT fk_positions_election FOREIGN KEY (election_id)
                     REFERENCES elections (id) ON DELETE CASCADE ON UPDATE CASCADE'
            );
        }

        if (! $this->db->fieldExists('status', 'positions')) {
            $this->forge->addColumn('positions', [
                'status' => [
                    'type'    => 'ENUM',
                    'constraint' => ['active', 'inactive'],
                    'default' => 'active',
                    'null'    => false,
                    'after'   => 'votes_required',
                ],
            ]);
        }

        if (! $this->db->fieldExists('updated_at', 'positions')) {
            $this->forge->addColumn('positions', [
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'created_at',
                ],
            ]);
        }

        // ============================================================
        // 2. PARTIES TABLE: add acronym, description, logo, status
        // ============================================================
        if (! $this->db->fieldExists('acronym', 'parties')) {
            $this->forge->addColumn('parties', [
                'acronym' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 32,
                    'null'       => true,
                    'after'      => 'name',
                ],
            ]);
        }

        if (! $this->db->fieldExists('description', 'parties')) {
            $this->forge->addColumn('parties', [
                'description' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'acronym',
                ],
            ]);
        }

        if (! $this->db->fieldExists('logo', 'parties')) {
            $this->forge->addColumn('parties', [
                'logo' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'description',
                ],
            ]);
        }

        if (! $this->db->fieldExists('status', 'parties')) {
            $this->forge->addColumn('parties', [
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['active', 'inactive'],
                    'default'    => 'active',
                    'null'       => false,
                    'after'      => 'logo',
                ],
            ]);
        }

        if (! $this->db->fieldExists('updated_at', 'parties')) {
            $this->forge->addColumn('parties', [
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'created_at',
                ],
            ]);
        }

        // ============================================================
        // 3. CANDIDATES TABLE: add student_id + status fields
        // ============================================================
        if (! $this->db->fieldExists('student_id', 'candidates')) {
            $this->forge->addColumn('candidates', [
                'student_id' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
            $this->db->query(
                'ALTER TABLE candidates ADD KEY idx_candidates_student_id (student_id)'
            );
        }

        if (! $this->db->fieldExists('status', 'candidates')) {
            $this->forge->addColumn('candidates', [
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['active', 'inactive'],
                    'default'    => 'active',
                    'null'       => false,
                    'after'      => 'photo',
                ],
            ]);
        }

        if (! $this->db->fieldExists('updated_at', 'candidates')) {
            $this->forge->addColumn('candidates', [
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'created_at',
                ],
            ]);
        }

        // ============================================================
        // 4. ELECTIONS TABLE: add academic_year if missing
        // ============================================================
        if (! $this->db->fieldExists('academic_year', 'elections')) {
            $this->forge->addColumn('elections', [
                'academic_year' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 32,
                    'null'       => true,
                    'after'      => 'title',
                ],
            ]);
        }
    }

    public function down()
    {
        // Rollback positions
        if ($this->db->fieldExists('election_id', 'positions')) {
            $this->db->query('ALTER TABLE positions DROP FOREIGN KEY fk_positions_election');
            $this->db->query('ALTER TABLE positions DROP INDEX idx_positions_election_id');
            $this->forge->dropColumn('positions', 'election_id');
        }
        if ($this->db->fieldExists('status', 'positions')) {
            $this->forge->dropColumn('positions', 'status');
        }
        if ($this->db->fieldExists('updated_at', 'positions')) {
            $this->forge->dropColumn('positions', 'updated_at');
        }

        // Rollback parties
        if ($this->db->fieldExists('acronym', 'parties')) {
            $this->forge->dropColumn('parties', 'acronym');
        }
        if ($this->db->fieldExists('description', 'parties')) {
            $this->forge->dropColumn('parties', 'description');
        }
        if ($this->db->fieldExists('logo', 'parties')) {
            $this->forge->dropColumn('parties', 'logo');
        }
        if ($this->db->fieldExists('status', 'parties')) {
            $this->forge->dropColumn('parties', 'status');
        }
        if ($this->db->fieldExists('updated_at', 'parties')) {
            $this->forge->dropColumn('parties', 'updated_at');
        }

        // Rollback candidates
        if ($this->db->fieldExists('student_id', 'candidates')) {
            $this->db->query('ALTER TABLE candidates DROP INDEX idx_candidates_student_id');
            $this->forge->dropColumn('candidates', 'student_id');
        }
        if ($this->db->fieldExists('status', 'candidates')) {
            $this->forge->dropColumn('candidates', 'status');
        }
        if ($this->db->fieldExists('updated_at', 'candidates')) {
            $this->forge->dropColumn('candidates', 'updated_at');
        }

        // Rollback elections
        if ($this->db->fieldExists('academic_year', 'elections')) {
            $this->forge->dropColumn('elections', 'academic_year');
        }
    }
}
