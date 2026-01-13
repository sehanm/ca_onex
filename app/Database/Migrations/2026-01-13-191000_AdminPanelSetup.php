<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdminPanelSetup extends Migration
{
    public function up()
    {
        // 2. Add department_id to user_details
        // Checking if column exists first would be better, but for now we wrap in try-catch or just separate migrations.
        // Assuming the previous failure created 'departments' but failed on 'addColumn' or foreign key, or vice versa?
        // Actually, previous log showed failure at createTable('departments').
        // If 'departments' exists, we should skip it.
        
        $db = \Config\Database::connect();
        if (!$db->tableExists('departments')) {
             $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'department_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '150',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('departments');
        }

        if (!$db->fieldExists('department_id', 'user_details')) {
             $fields = [
                'department_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'user_id'
                ]
            ];
            $this->forge->addColumn('user_details', $fields);
            // FK might fail if already exists, but usually addColumn is the blocker
             $db->query("ALTER TABLE `user_details` ADD CONSTRAINT `user_details_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL ON UPDATE CASCADE");
        }

        // 3. Audit Logs Table
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
                'null'       => true,
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'details' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '45',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('audit_logs');
    }

    public function down()
    {
        $this->forge->dropTable('audit_logs');
        $this->forge->dropColumn('user_details', 'department_id');
        $this->forge->dropTable('departments');
    }
}
