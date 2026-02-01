<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SplitOnboardingDetails extends Migration
{
    public function up()
    {
        // 1. Admin Details Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'request_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'chair' => ['type' => 'BOOLEAN', 'default' => false],
            'table' => ['type' => 'BOOLEAN', 'default' => false],
            'phone' => ['type' => 'BOOLEAN', 'default' => false],
            'status' => ['type' => 'ENUM', 'constraint' => ['Pending', 'Processing', 'Completed'], 'default' => 'Pending'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('request_id', 'onboarding_requests', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('onboarding_admin_details');

        // 2. HR Details Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'request_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'mobile_phone' => ['type' => 'BOOLEAN', 'default' => false],
            'sim_card' => ['type' => 'BOOLEAN', 'default' => false],
            'status' => ['type' => 'ENUM', 'constraint' => ['Pending', 'Processing', 'Completed'], 'default' => 'Pending'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('request_id', 'onboarding_requests', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('onboarding_hr_details');

        // 3. ICT Details Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'request_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],

            // Requirements
            'desktop_laptop' => ['type' => 'ENUM', 'constraint' => ['None', 'Desktop', 'Laptop'], 'default' => 'None'],
            'printer' => ['type' => 'BOOLEAN', 'default' => false],

            // Assignments
            'ict_asset_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'ict_monitor_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],

            // Software
            'soft_smms' => ['type' => 'BOOLEAN', 'default' => false],
            'soft_receipt' => ['type' => 'BOOLEAN', 'default' => false],
            'soft_training' => ['type' => 'BOOLEAN', 'default' => false],
            'soft_ecole' => ['type' => 'BOOLEAN', 'default' => false],
            'soft_pronto' => ['type' => 'BOOLEAN', 'default' => false],
            'pronto_previous_user' => ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true],
            'soft_ims' => ['type' => 'BOOLEAN', 'default' => false],
            'soft_sap' => ['type' => 'BOOLEAN', 'default' => false],
            'soft_imeet' => ['type' => 'BOOLEAN', 'default' => false],
            'access_copy_user' => ['type' => 'VARCHAR', 'constraint' => '150', 'null' => true],

            'status' => ['type' => 'ENUM', 'constraint' => ['Pending', 'Processing', 'Completed'], 'default' => 'Pending'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('request_id', 'onboarding_requests', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('ict_asset_id', 'assets', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('ict_monitor_id', 'assets', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('onboarding_ict_details');

        // MIGRATE DATA
        $oldData = $this->db->table('onboarding_details')->get()->getResultArray();
        foreach ($oldData as $row) {
            // Admin
            $this->db->table('onboarding_admin_details')->insert([
                'request_id' => $row['request_id'],
                'chair' => $row['admin_chair'],
                'table' => $row['admin_table'],
                'phone' => $row['admin_phone'],
                'status' => $row['admin_status'] ?? 'Pending',
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
            ]);

            // HR
            $this->db->table('onboarding_hr_details')->insert([
                'request_id' => $row['request_id'],
                'mobile_phone' => $row['hr_mobile'],
                'sim_card' => $row['hr_sim'],
                'status' => $row['hr_status'] ?? 'Pending',
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
            ]);

            // ICT
            $this->db->table('onboarding_ict_details')->insert([
                'request_id' => $row['request_id'],
                'desktop_laptop' => $row['ict_desktop_laptop'],
                'printer' => $row['ict_printer'],
                'ict_asset_id' => $row['ict_asset_id'] ?? null,
                'ict_monitor_id' => $row['ict_monitor_id'] ?? null,
                'soft_smms' => $row['soft_smms'],
                'soft_receipt' => $row['soft_receipt'],
                'soft_training' => $row['soft_training'],
                'soft_ecole' => $row['soft_ecole'],
                'soft_pronto' => $row['soft_pronto'],
                'pronto_previous_user' => $row['pronto_previous_user'] ?? null,
                'soft_ims' => $row['soft_ims'],
                'soft_sap' => $row['soft_sap'],
                'soft_imeet' => $row['soft_imeet'],
                'access_copy_user' => $row['access_copy_user'] ?? null,
                'status' => $row['ict_status'] ?? 'Pending',
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('onboarding_admin_details');
        $this->forge->dropTable('onboarding_hr_details');
        $this->forge->dropTable('onboarding_ict_details');
    }
}
