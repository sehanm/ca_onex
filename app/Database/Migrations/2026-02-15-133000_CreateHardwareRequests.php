<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHardwareRequests extends Migration
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
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'item_type' => [
                'type' => 'ENUM',
                'constraint' => ['accessory', 'asset'],
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'requirement_type' => [
                'type' => 'ENUM',
                'constraint' => ['fixed', 'temporary'],
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'assigned', 'returned', 'rejected'],
                'default' => 'pending',
            ],
            'assigned_item_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'request_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'return_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'admin_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('hardware_requests');
    }

    public function down()
    {
        $this->forge->dropTable('hardware_requests');
    }
}
