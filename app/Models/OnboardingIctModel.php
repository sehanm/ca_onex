<?php

namespace App\Models;

use CodeIgniter\Model;

class OnboardingIctModel extends Model
{
    protected $table = 'onboarding_ict_details';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'request_id',
        'desktop_laptop',
        'printer',
        'soft_smms',
        'soft_receipt',
        'soft_training',
        'soft_ecole',
        'soft_pronto',
        'pronto_previous_user',
        'soft_ims',
        'soft_sap',
        'soft_imeet',
        'access_copy_user',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

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
}
