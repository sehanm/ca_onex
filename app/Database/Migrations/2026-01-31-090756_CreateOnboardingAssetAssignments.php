<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOnboardingAssetAssignments extends Migration
{
    public function up()
    {
        // 1. Create Assignments Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'request_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'asset_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'assignment_type' => ['type' => 'ENUM', 'constraint' => ['Main', 'Monitor'], 'default' => 'Main'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('request_id', 'onboarding_requests', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('asset_id', 'assets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('onboarding_asset_assignments');

        // 2. Migrate Data
        $db = \Config\Database::connect();
        $builder = $db->table('onboarding_ict_details');
        $rows = $builder->get()->getResultArray();

        foreach ($rows as $row) {
            // Main Asset
            if (!empty($row['ict_asset_id'])) {
                $db->table('onboarding_asset_assignments')->insert([
                    'request_id' => $row['request_id'],
                    'asset_id' => $row['ict_asset_id'],
                    'assignment_type' => 'Main',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            // Monitor
            if (!empty($row['ict_monitor_id'])) {
                $db->table('onboarding_asset_assignments')->insert([
                    'request_id' => $row['request_id'],
                    'asset_id' => $row['ict_monitor_id'],
                    'assignment_type' => 'Monitor',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        // 3. Drop Columns from Old Table
        $this->forge->dropForeignKey('onboarding_ict_details', 'onboarding_ict_details_ict_asset_id_foreign');
        $this->forge->dropForeignKey('onboarding_ict_details', 'onboarding_ict_details_ict_monitor_id_foreign');
        $this->forge->dropColumn('onboarding_ict_details', ['ict_asset_id', 'ict_monitor_id']);
    }

    public function down()
    {
        // Add Columns Back
        $fields = [
            'ict_asset_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'ict_monitor_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
        ];
        $this->forge->addColumn('onboarding_ict_details', $fields);
        $this->forge->addForeignKey('ict_asset_id', 'assets', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('ict_monitor_id', 'assets', 'id', 'SET NULL', 'CASCADE');

        // Migrate Data Back (Optional, but good practice)
        $db = \Config\Database::connect();
        $assignments = $db->table('onboarding_asset_assignments')->get()->getResultArray();
        foreach ($assignments as $a) {
            if ($a['assignment_type'] == 'Main') {
                $db->table('onboarding_ict_details')->where('request_id', $a['request_id'])->update(['ict_asset_id' => $a['asset_id']]);
            } elseif ($a['assignment_type'] == 'Monitor') {
                $db->table('onboarding_ict_details')->where('request_id', $a['request_id'])->update(['ict_monitor_id' => $a['asset_id']]);
            }
        }

        // Drop Table
        $this->forge->dropTable('onboarding_asset_assignments');
    }
}
