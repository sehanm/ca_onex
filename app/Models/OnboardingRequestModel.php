<?php

namespace App\Models;

use CodeIgniter\Model;

class OnboardingRequestModel extends Model
{
    protected $table            = 'onboarding_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'candidate_name', 'designation', 'department_id', 'hod_user_id', 
        'status', 'hr_user_id', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'candidate_name' => 'required|min_length[3]|max_length[200]',
        'designation'    => 'required|min_length[2]|max_length[150]',
        'department_id'  => 'required|integer',
        'hod_user_id'    => 'required|integer',
    ];
}
