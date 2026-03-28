<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserRolesTable extends Migration
{
    public function up()
    {
        // User Roles Table (Pivot)
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
            'role_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['user_id', 'role_id']);
        $this->forge->createTable('user_roles');

        // Migrate existing roles
        $users = $this->db->table('users')->get()->getResultArray();
        foreach ($users as $user) {
            if ($user['system_role_id']) {
                $this->db->table('user_roles')->insert([
                    'user_id' => $user['id'],
                    'role_id' => $user['system_role_id']
                ]);
            }
            if ($user['divisional_role_id']) {
                // Check if already inserted (could be both point to same role, though unlikely given types)
                $existing = $this->db->table('user_roles')
                    ->where(['user_id' => $user['id'], 'role_id' => $user['divisional_role_id']])
                    ->countAllResults();
                if ($existing == 0) {
                    $this->db->table('user_roles')->insert([
                        'user_id' => $user['id'],
                        'role_id' => $user['divisional_role_id']
                    ]);
                }
            }
        }

        // Drop old columns
        $this->forge->dropForeignKey('users', 'users_system_role_id_foreign');
        $this->forge->dropForeignKey('users', 'users_divisional_role_id_foreign');
        $this->forge->dropColumn('users', ['system_role_id', 'divisional_role_id']);
    }

    public function down()
    {
        // Add columns back
        $this->forge->addColumn('users', [
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
        ]);

        $this->forge->addForeignKey('system_role_id', 'roles', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('divisional_role_id', 'roles', 'id', 'SET NULL', 'CASCADE');

        // Restore first system and divisional role back to columns
        $userRoles = $this->db->table('user_roles')
            ->select('user_roles.*, roles.role_type')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->get()
            ->getResultArray();

        foreach ($userRoles as $ur) {
            if ($ur['role_type'] == 'system') {
                $this->db->table('users')->where('id', $ur['user_id'])->update(['system_role_id' => $ur['role_id']]);
            } else {
                $this->db->table('users')->where('id', $ur['user_id'])->update(['divisional_role_id' => $ur['role_id']]);
            }
        }

        $this->forge->dropTable('user_roles');
    }
}
