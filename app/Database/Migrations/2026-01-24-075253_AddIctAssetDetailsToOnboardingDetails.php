<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIctAssetDetailsToOnboardingDetails extends Migration
{
    public function up()
    {
        $fields = [
            'ict_model' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
                'after'      => 'ict_printer'
            ],
            'ict_serial_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
                'after'      => 'ict_model'
            ],
            'ict_asset_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'ict_serial_number'
            ],
            'ict_monitor_details' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'ict_asset_code'
            ],
        ];
        $this->forge->addColumn('onboarding_details', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('onboarding_details', ['ict_model', 'ict_serial_number', 'ict_asset_code', 'ict_monitor_details']);
    }
}
