<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OnboardingRequestModel;
use App\Models\DepartmentModel;
use App\Models\UserModel;

class Onboarding extends BaseController
{
    protected $onboardingModel;
    protected $departmentModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->onboardingModel = new OnboardingRequestModel();
        $this->departmentModel = new DepartmentModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }

    public function create()
    {
        $role = session()->get('role');
        if ($role !== 'Super Admin' && $role !== 'HR Admin') {
            return redirect()->to('dashboard')->with('error', 'Access Denied: HR Admin only.');
        }
        
        $data = [
            'departments' => $this->departmentModel->findAll(),
            'users' => $this->userModel->select('users.id, user_details.full_name, r.role_name')
                        ->join('user_details', 'user_details.user_id = users.id')
                        ->join('roles r', 'r.id = users.system_role_id OR r.id = users.divisional_role_id') // Get role name
                        ->findAll(),
             'page_title' => 'Initiate Onboarding'
        ];

        return view('onboarding/create_request', $data);
    }

    public function store()
    {
        $role = session()->get('role');
        if ($role !== 'Super Admin' && $role !== 'HR Admin') {
            return redirect()->to('dashboard')->with('error', 'Access Denied: HR Admin only.');
        }

        $rules = [
            'candidate_name' => 'required|min_length[3]',
            'designation' => 'required',
            'department_id' => 'required',
            'hod_user_id' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'candidate_name' => $this->request->getPost('candidate_name'),
            'designation' => $this->request->getPost('designation'),
            'department_id' => $this->request->getPost('department_id'),
            'hod_user_id' => $this->request->getPost('hod_user_id'),
            'hr_user_id' => session()->get('id'), // Authenticated user
            'status' => 'Pending_HOD',
        ];

        if ($this->onboardingModel->insert($data)) {
            // Ideally send email to HOD here
            return redirect()->to('dashboard')->with('success', 'Onboarding request initiated successfully. Sent to HOD for approval.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create request.');
        }
    }
}
