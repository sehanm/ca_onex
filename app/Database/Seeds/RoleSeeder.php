<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // System Roles
            ['role_name' => 'Super Admin', 'role_type' => 'system'],
            ['role_name' => 'Admin', 'role_type' => 'system'],
            ['role_name' => 'Staff', 'role_type' => 'system'],
            ['role_name' => 'Facilitator', 'role_type' => 'system'],
            // Divisional Roles
            ['role_name' => 'HOD', 'role_type' => 'divisional'],
            ['role_name' => 'Manager', 'role_type' => 'divisional'],
            ['role_name' => 'Other', 'role_type' => 'divisional'],
        ];

        // Using Query Builder
        $this->db->table('roles')->insertBatch($data);
    }
}
