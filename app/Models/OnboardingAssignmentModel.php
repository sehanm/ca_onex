<?php

namespace App\Models;

use CodeIgniter\Model;

class OnboardingAssignmentModel extends Model
{
    protected $table = 'onboarding_asset_assignments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'request_id',
        'asset_id',
        'assignment_type'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
