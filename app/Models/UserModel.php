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
    protected $allowedFields = ['username', 'password', 'system_role_id', 'divisional_role_id'];

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
        return $this->select('users.*, user_details.full_name, user_details.email, r.role_name as system_role')
            ->join('user_details', 'user_details.user_id = users.id')
            ->join('roles r', 'r.id = users.system_role_id')
            ->where('users.username', $username)
            ->first();
    }

    public function getUserByIdWithDetails($id)
    {
        return $this->select('users.*, user_details.full_name, user_details.email, r.role_name as system_role, d.department_name')
            ->join('user_details', 'user_details.user_id = users.id')
            ->join('roles r', 'r.id = users.system_role_id')
            ->join('departments d', 'd.id = user_details.department_id', 'left')
            ->where('users.id', $id)
            ->first();
    }
}
