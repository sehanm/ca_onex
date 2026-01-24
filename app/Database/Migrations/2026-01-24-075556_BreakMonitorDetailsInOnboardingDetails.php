<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BreakMonitorDetailsInOnboardingDetails extends Migration
{
    public function up()
    {
        $fields = [
            'ict_monitor_model' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
                'after'      => 'ict_asset_code'
            ],
            'ict_monitor_serial' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
                'after'      => 'ict_monitor_model'
            ],
            'ict_monitor_asset' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'ict_monitor_serial'
            ],
        ];
        $this->forge->addColumn('onboarding_details', $fields);
        
        // Remove the old combined column
        $this->forge->dropColumn('onboarding_details', 'ict_monitor_details');
    }

    public function down()
    {
        $this->forge->addColumn('onboarding_details', [
            'ict_monitor_details' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'ict_asset_code'
            ]
        ]);
        $this->forge->dropColumn('onboarding_details', ['ict_monitor_model', 'ict_monitor_serial', 'ict_monitor_asset']);
    }
}
