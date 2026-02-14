<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAccessoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'brand' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'model' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'serial_number' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'asset_code' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'unique' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Stock', 'Assigned', 'Damaged'],
                'default' => 'Stock',
            ],
            'assigned_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('accessories');
    }

    public function down()
    {
        $this->forge->dropTable('accessories');
    }
}
