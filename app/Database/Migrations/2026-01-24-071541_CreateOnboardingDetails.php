<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOnboardingDetails extends Migration
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
            'request_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            // Request Types
            'onboarding_type' => [
                'type'       => 'ENUM',
                'constraint' => ['New Recruit', 'Internal Transfer'],
            ],
            'designation_type' => [
                'type'       => 'ENUM',
                'constraint' => ['New', 'Replacement'],
            ],
            'replacement_employee_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
                'null'       => true,
            ],
            'budget_approval_doc' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            // Administration & Events
            'admin_chair' => ['type' => 'BOOLEAN', 'default' => false],
            'admin_table' => ['type' => 'BOOLEAN', 'default' => false],
            'admin_phone' => ['type' => 'BOOLEAN', 'default' => false],
            // HR
            'hr_mobile' => ['type' => 'BOOLEAN', 'default' => false],
            'hr_sim'    => ['type' => 'BOOLEAN', 'default' => false],
            // ICT Hardware
            'ict_desktop_laptop' => [
                 'type'       => 'ENUM',
                 'constraint' => ['None', 'Desktop', 'Laptop'],
                 'default'    => 'None'
            ],
            'ict_printer' => ['type' => 'BOOLEAN', 'default' => false],
            // ICT Software
            'soft_smms'     => ['type' => 'BOOLEAN', 'default' => false],
            'soft_receipt'  => ['type' => 'BOOLEAN', 'default' => false],
            'soft_training' => ['type' => 'BOOLEAN', 'default' => false],
            'soft_ecole'    => ['type' => 'BOOLEAN', 'default' => false],
            'soft_pronto'   => ['type' => 'BOOLEAN', 'default' => false],
            'pronto_previous_user' => ['type' => 'VARCHAR', 'constraint' => '200', 'null' => true],
            'soft_ims'      => ['type' => 'BOOLEAN', 'default' => false],
            'soft_sap'      => ['type' => 'BOOLEAN', 'default' => false],
            'soft_imeet'    => ['type' => 'BOOLEAN', 'default' => false],
            // ICT Other
            'access_copy_user' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
                'null'       => true,
                'comment'    => 'Existing user name to copy permissions from'
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
        $this->forge->addForeignKey('request_id', 'onboarding_requests', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('onboarding_details');
    }

    public function down()
    {
        $this->forge->dropTable('onboarding_details');
    }
}
