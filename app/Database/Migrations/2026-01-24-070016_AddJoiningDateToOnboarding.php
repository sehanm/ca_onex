<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJoiningDateToOnboarding extends Migration
{
    public function up()
    {
        $fields = [
            'joining_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'designation'
            ],
        ];
        $this->forge->addColumn('onboarding_requests', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('onboarding_requests', 'joining_date');
    }
}
