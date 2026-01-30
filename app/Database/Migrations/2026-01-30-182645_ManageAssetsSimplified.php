<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ManageAssetsSimplified extends Migration
{
    public function up()
    {
        // Drop previous complex tables
        $tables = [
            'inv_lifecycle_events', 'inv_maintenance', 'inv_assignments', 
            'inv_item_attributes', 'inv_items', 'inv_suppliers', 
            'inv_sub_locations', 'inv_locations', 'inv_statuses', 
            'inv_models', 'inv_manufacturers', 'inv_categories'
        ];
        
        foreach ($tables as $table) {
            $this->forge->dropTable($table, true);
        }

        // Create Simplified Assets Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'model' => ['type' => 'VARCHAR', 'constraint' => 200],
            'serial_number' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'asset_code' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true, 'null' => true],
            'department_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'assigned_user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['In Store', 'Assigned', 'Repair', 'Retired'], 'default' => 'In Store'],
            'qr_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('department_id', 'departments', 'id', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('assigned_user_id', 'users', 'id', 'SET NULL', 'SET NULL');
        
        $this->forge->createTable('assets');
    }

    public function down()
    {
        $this->forge->dropTable('assets', true);
    }
}
