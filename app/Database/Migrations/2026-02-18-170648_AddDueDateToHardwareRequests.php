<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDueDateToHardwareRequests extends Migration
{
    public function up()
    {
        $this->forge->addColumn('hardware_requests', [
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'requirement_type'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('hardware_requests', 'due_date');
    }
}
