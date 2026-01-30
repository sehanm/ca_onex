<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventorySystem extends Migration
{
    public function up()
    {
        // 1. Categories (Laptops, Monitors, Mice, etc.)
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('inv_categories');

        // 2. Manufacturers (Dell, HP, Logitech)
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('inv_manufacturers');

        // 3. Models
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'manufacturer_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'category_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'model_name' => ['type' => 'VARCHAR', 'constraint' => 200],
            'image_url' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('inv_models');

        // 4. Statuses (In Store, Assigned, Repair, Retired)
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'status_name' => ['type' => 'VARCHAR', 'constraint' => 50],
            'color_code' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => '#ccc'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('inv_statuses');

        // 5. Locations (Head Office, Branch A)
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'location_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'address' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('inv_locations');

        // 6. Sub-Locations (Room 304, IT Store)
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'location_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'sub_location_name' => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('inv_sub_locations');

        // 7. Suppliers
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'supplier_name' => ['type' => 'VARCHAR', 'constraint' => 200],
            'contact_person' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('inv_suppliers');

        // 8. Items (The Master Asset Table)
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'uuid' => ['type' => 'VARCHAR', 'constraint' => 36, 'unique' => true],
            'model_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'status_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'location_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'sub_location_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'supplier_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'serial_number' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'asset_code' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true, 'null' => true],
            'purchase_date' => ['type' => 'DATE', 'null' => true],
            'purchase_price' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'warranty_expiry' => ['type' => 'DATE', 'null' => true],
            'qr_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('inv_items');

        // 9. Item Attributes (EAV Pattern for flexible specs like RAM, CPU, Storage)
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'item_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'attribute_key' => ['type' => 'VARCHAR', 'constraint' => 50],
            'attribute_value' => ['type' => 'VARCHAR', 'constraint' => 255],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('inv_item_attributes');

        // 10. Assignments (Who has what)
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'item_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'assigned_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'assigned_at' => ['type' => 'DATETIME'],
            'returned_at' => ['type' => 'DATETIME', 'null' => true],
            'condition_on_assign' => ['type' => 'TEXT', 'null' => true],
            'condition_on_return' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('inv_assignments');

        // 11. Maintenance Logs
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'item_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'maintenance_type' => ['type' => 'VARCHAR', 'constraint' => 50], // Repair, Service, Upgrade
            'description' => ['type' => 'TEXT'],
            'cost' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'start_date' => ['type' => 'DATE'],
            'end_date' => ['type' => 'DATE', 'null' => true],
            'performed_by' => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('inv_maintenance');

        // 12. Lifecycle Events (The Big Data / Audit Stream)
        // Tracks EVERY movement, status change, or scan
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'item_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'event_type' => ['type' => 'VARCHAR', 'constraint' => 50], // MOV, ASG, STS, RPR, SCN
            'description' => ['type' => 'TEXT'],
            'previous_val' => ['type' => 'TEXT', 'null' => true],
            'current_val' => ['type' => 'TEXT', 'null' => true],
            'performed_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'timestamp' => ['type' => 'DATETIME'],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('inv_lifecycle_events');
    }

    public function down()
    {
        $this->forge->dropTable('inv_lifecycle_events', true);
        $this->forge->dropTable('inv_maintenance', true);
        $this->forge->dropTable('inv_assignments', true);
        $this->forge->dropTable('inv_item_attributes', true);
        $this->forge->dropTable('inv_items', true);
        $this->forge->dropTable('inv_suppliers', true);
        $this->forge->dropTable('inv_sub_locations', true);
        $this->forge->dropTable('inv_locations', true);
        $this->forge->dropTable('inv_statuses', true);
        $this->forge->dropTable('inv_models', true);
        $this->forge->dropTable('inv_manufacturers', true);
        $this->forge->dropTable('inv_categories', true);
    }
}
