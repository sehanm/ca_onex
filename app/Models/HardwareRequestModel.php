<?php

namespace App\Models;

use CodeIgniter\Model;

class HardwareRequestModel extends Model
{
    protected $table = 'hardware_requests';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id',
        'item_type',
        'category',
        'requirement_type',
        'due_date',
        'reason',
        'status',
        'assigned_item_id',
        'request_date',
        'return_date',
        'admin_id',
        'remarks'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getRequestsWithDetails($userId = null)
    {
        $builder = $this->select('hardware_requests.*, user_details.full_name as requester_name, admin_details.full_name as admin_name')
            ->join('user_details', 'user_details.user_id = hardware_requests.user_id')
            ->join('user_details admin_details', 'admin_details.user_id = hardware_requests.admin_id', 'left');

        if ($userId) {
            $builder->where('hardware_requests.user_id', $userId);
        }

        return $builder->orderBy('hardware_requests.created_at', 'DESC')->findAll();
    }
}
