<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSectionStatusToOnboardingDetails extends Migration
{
    public function up()
    {
        $fields = [
            'admin_status' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending', 'Processing', 'Completed'],
                'default'    => 'Pending',
                'after'      => 'admin_phone'
            ],
            'hr_status' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending', 'Processing', 'Completed'],
                'default'    => 'Pending',
                'after'      => 'hr_sim'
            ],
            'ict_status' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending', 'Processing', 'Completed'],
                'default'    => 'Pending',
                'after'      => 'access_copy_user'
            ],
        ];
        $this->forge->addColumn('onboarding_details', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('onboarding_details', ['admin_status', 'hr_status', 'ict_status']);
    }
}
