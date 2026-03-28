<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['username', 'password', 'force_password_change'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public function getUserWithDetails($username)
    {
        $user = $this->select('users.*, user_details.full_name, user_details.email')
            ->join('user_details', 'user_details.user_id = users.id')
            ->where('users.username', $username)
            ->first();

        if ($user) {
            $user['roles'] = $this->db->table('user_roles')
                ->select('roles.*')
                ->join('roles', 'roles.id = user_roles.role_id')
                ->where('user_roles.user_id', $user['id'])
                ->get()
                ->getResultArray();
                
            // For backward compatibility and session
            $systemRoles = array_values(array_filter($user['roles'], fn($r) => $r['role_type'] == 'system'));
            $user['system_role'] = !empty($systemRoles) ? $systemRoles[0]['role_name'] : 'User';
        }

        return $user;
    }

    public function getUserByIdWithDetails($id)
    {
        $user = $this->select('users.*, user_details.full_name, user_details.epf_number, user_details.email, d.department_name, user_details.department_id')
            ->join('user_details', 'user_details.user_id = users.id')
            ->join('departments d', 'd.id = user_details.department_id', 'left')
            ->where('users.id', $id)
            ->first();

        if ($user) {
            $user['roles'] = $this->db->table('user_roles')
                ->select('roles.*')
                ->join('roles', 'roles.id = user_roles.role_id')
                ->where('user_roles.user_id', $user['id'])
                ->get()
                ->getResultArray();
            
            $user['role_ids'] = array_column($user['roles'], 'id');
        }

        return $user;
    }
}
