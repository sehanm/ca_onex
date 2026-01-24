<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OnboardingSetup extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'candidate_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
            ],
            'designation' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'department_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'hod_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'The Manager/HOD assigned to fill requirements',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending_HOD', 'HOD_Submitted', 'Processing', 'Completed'],
                'default'    => 'Pending_HOD',
            ],
            'hr_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'HR user who initiated the request',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('department_id', 'departments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('hod_user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('hr_user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('onboarding_requests');
    }

    public function down()
    {
        $this->forge->dropTable('onboarding_requests');
    }
}
