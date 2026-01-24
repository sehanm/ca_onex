<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddManagerToDepartments extends Migration
{
    public function up()
    {
        $fields = [
            'manager_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'department_name',
                'comment'    => 'User ID of the HOD/Manager'
            ],
        ];
        $this->forge->addColumn('departments', $fields);
        
        // Add foreign key constraint
        $this->db->query('ALTER TABLE `departments` ADD CONSTRAINT `fk_departments_manager` FOREIGN KEY (`manager_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('departments', 'fk_departments_manager');
        $this->forge->dropColumn('departments', 'manager_id');
    }
}
