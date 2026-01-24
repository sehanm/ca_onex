<?php

namespace App\Models;

use CodeIgniter\Model;

class OnboardingDetailsModel extends Model
{
    protected $table            = 'onboarding_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'request_id',
        'onboarding_type',
        'designation_type',
        'replacement_employee_name',
        'budget_approval_doc',
        // Admin
        'admin_chair', 'admin_table', 'admin_phone', 'admin_status',
        // HR
        'hr_mobile', 'hr_sim', 'hr_status',
        // ICT
        'ict_desktop_laptop', 'ict_printer',
        // Software
        'soft_smms', 'soft_receipt', 'soft_training', 'soft_ecole', 
        'soft_pronto', 'pronto_previous_user',
        'soft_ims', 'soft_sap', 'soft_imeet',
        'access_copy_user', 'ict_status',
        'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation could be added here to strictly enforce types if needed
}
