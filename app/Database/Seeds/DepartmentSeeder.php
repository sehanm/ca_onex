<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            'CEO/Secretariat',
            'Legal',
            'Examination',
            'Examination Technical',
            'APFASL',
            'Student Services',
            'Practical Training',
            'Education',
            'Library',
            'Business School',
            'TAX',
            'IT Training',
            'MELC',
            'Internal Audit',
            'Finance',
            'Member Relations',
            'Marketing',
            'Technical',
            'ICT',
            'HR',
            'Administration & Events'
        ];

        $data = [];
        $time = date('Y-m-d H:i:s');

        foreach ($departments as $dept) {
            // Check if department already exists to avoid duplicates if seeder is run multiple times
            $exists = $this->db->table('departments')->where('department_name', $dept)->countAllResults();
            if ($exists == 0) {
                $data[] = [
                    'department_name' => $dept,
                    'created_at' => $time
                ];
            }
        }

        if (!empty($data)) {
            $this->db->table('departments')->insertBatch($data);
        }
    }
}
