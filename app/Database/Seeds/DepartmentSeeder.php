<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['department_name' => 'Human Resources', 'created_at' => date('Y-m-d H:i:s')],
            ['department_name' => 'Information Technology', 'created_at' => date('Y-m-d H:i:s')],
            ['department_name' => 'Finance', 'created_at' => date('Y-m-d H:i:s')],
            ['department_name' => 'Operations', 'created_at' => date('Y-m-d H:i:s')],
            ['department_name' => 'Marketing', 'created_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('departments')->insertBatch($data);
    }
}
