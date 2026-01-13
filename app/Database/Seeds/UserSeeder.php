<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Helper function to get Role ID
        $getRoleId = function ($name) {
            return $this->db->table('roles')->where('role_name', $name)->get()->getRow()->id;
        };

        // Fetch Role IDs
        $superAdminRoleId = $getRoleId('Super Admin');
        $staffRoleId = $getRoleId('Staff');
        $otherDivRoleId = $getRoleId('Other'); // Default divisional role
        $hodRoleId = $getRoleId('HOD');

        $users = [
            [
                'username' => 'superadmin',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'system_role_id' => $superAdminRoleId,
                'divisional_role_id' => $otherDivRoleId,
                'details' => [
                    'full_name' => 'Super Administrator',
                    'epf_number' => '001',
                    'email' => 'superadmin@caonex.com'
                ]
            ],
            [
                'username' => 'staffuser',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'system_role_id' => $staffRoleId,
                'divisional_role_id' => $otherDivRoleId,
                'details' => [
                    'full_name' => 'John Doe',
                    'epf_number' => '101',
                    'email' => 'johndoe@caonex.com'
                ]
            ],
            [
                 'username' => 'hoduser',
                 'password' => password_hash('hod123', PASSWORD_DEFAULT),
                 'system_role_id' => $staffRoleId, // HODs are essentially staff with extra privileges in their division, or could be 'Admin' depending on logic. Sticking to Staff system role for now but HOD divisional.
                 'divisional_role_id' => $hodRoleId,
                 'details' => [
                     'full_name' => 'Jane Smith',
                     'epf_number' => '102',
                     'email' => 'janesmith@caonex.com'
                 ]
            ]
        ];

        foreach ($users as $user) {
            // Insert User
            $userData = [
                'username' => $user['username'],
                'password' => $user['password'],
                'system_role_id' => $user['system_role_id'],
                'divisional_role_id' => $user['divisional_role_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            $this->db->table('users')->insert($userData);
            $newUserId = $this->db->insertID();

            // Insert User Details
            $detailsData = $user['details'];
            $detailsData['user_id'] = $newUserId;
            
            $this->db->table('user_details')->insert($detailsData);
        }
    }
}
