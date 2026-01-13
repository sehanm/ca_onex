<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InitialSetup extends Migration
{
    public function up()
    {
        // Roles Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'role_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'role_type' => [
                'type'       => 'ENUM',
                'constraint' => ['system', 'divisional'],
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('roles');

        // Users Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'system_role_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'divisional_role_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addForeignKey('system_role_id', 'roles', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('divisional_role_id', 'roles', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('users');

        // User Details Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'full_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
            ],
            'epf_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_details');
    }

    public function down()
    {
        $this->forge->dropTable('user_details');
        $this->forge->dropTable('users');
        $this->forge->dropTable('roles');
    }
}
