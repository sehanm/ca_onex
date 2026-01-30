<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class LinkAssetsToOnboarding extends Migration
{
    public function up()
    {
        $fields = [
            'ict_asset_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'ict_printer'],
            'ict_monitor_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'ict_asset_code'],
        ];
        $this->forge->addColumn('onboarding_details', $fields);
        
        // Add Foreign Keys
        $this->db->query("ALTER TABLE onboarding_details ADD CONSTRAINT fk_onboarding_main_asset FOREIGN KEY (ict_asset_id) REFERENCES assets(id) ON DELETE SET NULL ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE onboarding_details ADD CONSTRAINT fk_onboarding_monitor_asset FOREIGN KEY (ict_monitor_id) REFERENCES assets(id) ON DELETE SET NULL ON UPDATE CASCADE");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE onboarding_details DROP FOREIGN KEY fk_onboarding_main_asset");
        $this->db->query("ALTER TABLE onboarding_details DROP FOREIGN KEY fk_onboarding_monitor_asset");
        $this->forge->dropColumn('onboarding_details', 'ict_asset_id');
        $this->forge->dropColumn('onboarding_details', 'ict_monitor_id');
    }
}
