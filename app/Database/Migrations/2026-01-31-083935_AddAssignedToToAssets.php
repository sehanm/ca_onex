<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAssignedToToAssets extends Migration
{
    public function up()
    {
        $fields = [
            'assigned_to' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'assigned_user_id',
                'comment' => 'Name of person assigned if not a system user'
            ]
        ];
        $this->forge->addColumn('assets', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('assets', 'assigned_to');
    }
}
