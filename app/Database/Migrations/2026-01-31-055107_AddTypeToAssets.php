<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTypeToAssets extends Migration
{
    public function up()
    {
        $fields = [
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'id'
            ],
        ];
        $this->forge->addColumn('assets', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('assets', 'type');
    }
}
