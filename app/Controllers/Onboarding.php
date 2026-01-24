<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OnboardingRequestModel;
use App\Models\DepartmentModel;
use App\Models\UserModel;

use App\Models\OnboardingDetailsModel;

class Onboarding extends BaseController
{
    protected $onboardingModel;
    protected $detailsModel;
    protected $departmentModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->onboardingModel = new OnboardingRequestModel();
        $this->detailsModel = new OnboardingDetailsModel();
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
            'departments' => $this->departmentModel->select('departments.*, user_details.full_name as manager_name')
                        ->join('users', 'users.id = departments.manager_id', 'left')
                        ->join('user_details', 'user_details.user_id = users.id', 'left')
                        ->findAll(),
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
            'joining_date' => 'required',
            'department_id' => 'required',
            'hod_user_id' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'candidate_name' => $this->request->getPost('candidate_name'),
            'designation' => $this->request->getPost('designation'),
            'joining_date' => $this->request->getPost('joining_date'),
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

    public function pending()
    {
        $userId = session()->get('id');
        
        $data = [
            'requests' => $this->onboardingModel->select('onboarding_requests.*, departments.department_name, ud.full_name as hr_name')
                        ->join('departments', 'departments.id = onboarding_requests.department_id')
                        ->join('user_details ud', 'ud.user_id = onboarding_requests.hr_user_id', 'left')
                        ->where('hod_user_id', $userId)
                        ->orderBy('onboarding_requests.created_at', 'DESC')
                        ->findAll(),
            'page_title' => 'Onboarding Tasks'
        ];

        return view('onboarding/pending_requests', $data);
    }

    public function fillForm($id)
    {
        $userId = session()->get('id');
        $request = $this->onboardingModel->select('onboarding_requests.*, departments.department_name')
                        ->join('departments', 'departments.id = onboarding_requests.department_id')
                        ->where('onboarding_requests.id', $id)
                        ->first();

        // Check if request exists and assigned to user (or admin)
        if (!$request) {
            return redirect()->to('dashboard')->with('error', 'Request not found.');
        }
        
        // Strict check: Only assigned HOD can fill
        if ($request['hod_user_id'] != $userId && session()->get('role') !== 'Super Admin') {
             return redirect()->to('dashboard')->with('error', 'Access Denied: You are not the assigned HOD.');
        }

        $data = [
            'request' => $request,
            'page_title' => 'Facility Request Form'
        ];

        return view('onboarding/facility_form', $data);
    }

    public function saveForm($id)
    {
         $userId = session()->get('id');
         $request = $this->onboardingModel->find($id);

         if (!$request || ($request['hod_user_id'] != $userId && session()->get('role') !== 'Super Admin')) {
             return redirect()->to('dashboard')->with('error', 'Access Denied.');
         }

         // Validation
         $rules = [
             'onboarding_type' => 'required',
             'designation_type' => 'required',
             'ict_desktop_laptop' => 'required',
         ];
         
         if ($this->request->getPost('designation_type') === 'Replacement') {
             $rules['replacement_employee_name'] = 'required';
         }

          if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // File Upload
        $filePath = null;
        if ($this->request->getPost('designation_type') === 'New') {
            $file = $this->request->getFile('budget_approval_doc');
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(WRITEPATH . 'uploads/approvals', $newName);
                $filePath = $newName;
            } else {
                 return redirect()->back()->withInput()->with('error', 'Budget Approval Document represents a required field for New Designation.');
            }
        }

        $detailsData = [
            'request_id' => $id,
            'onboarding_type' => $this->request->getPost('onboarding_type'),
            'designation_type' => $this->request->getPost('designation_type'),
            'replacement_employee_name' => $this->request->getPost('replacement_employee_name'),
            'budget_approval_doc' => $filePath,
            
            // Admin
            'admin_chair' => $this->request->getPost('admin_chair') ? 1 : 0,
            'admin_table' => $this->request->getPost('admin_table') ? 1 : 0,
            'admin_phone' => $this->request->getPost('admin_phone') ? 1 : 0,
            
            // HR
            'hr_mobile' => $this->request->getPost('hr_mobile') ? 1 : 0,
            'hr_sim'    => $this->request->getPost('hr_sim') ? 1 : 0,
            
            // ICT
            'ict_desktop_laptop' => $this->request->getPost('ict_desktop_laptop'),
            'ict_printer' => $this->request->getPost('ict_printer') ? 1 : 0,
            
            // Software
            'soft_smms' => $this->request->getPost('soft_smms') ? 1 : 0,
            'soft_receipt' => $this->request->getPost('soft_receipt') ? 1 : 0,
            'soft_training' => $this->request->getPost('soft_training') ? 1 : 0,
            'soft_ecole' => $this->request->getPost('soft_ecole') ? 1 : 0,
            'soft_pronto' => $this->request->getPost('soft_pronto') ? 1 : 0,
            'pronto_previous_user' => $this->request->getPost('pronto_previous_user'),
            'soft_ims' => $this->request->getPost('soft_ims') ? 1 : 0,
            'soft_sap' => $this->request->getPost('soft_sap') ? 1 : 0,
            'soft_imeet' => $this->request->getPost('soft_imeet') ? 1 : 0,
            
            'access_copy_user' => $this->request->getPost('access_copy_user'),
        ];

        // Save details
        $this->detailsModel->insert($detailsData);

        // Update Request Status
        $this->onboardingModel->update($id, ['status' => 'Processing']); // Or 'HOD_Submitted'

        return redirect()->to('onboarding/pending')->with('success', 'Facility Request Submitted Successfully.');
    }
    public function facilitatorTasks()
    {
        $userId = session()->get('id');
        
        $managedDepts = $this->departmentModel->where('manager_id', $userId)->findAll();
        $roles = [];
        foreach ($managedDepts as $dept) {
            if ($dept['department_name'] == 'Administration & Events') $roles[] = 'Admin';
            if ($dept['department_name'] == 'HR') $roles[] = 'HR';
            if ($dept['department_name'] == 'ICT') $roles[] = 'ICT';
        }

        if (empty($roles)) {
            return redirect()->to('dashboard')->with('error', 'You do not have facilitator access.');
        }

        $query = $this->onboardingModel->select('onboarding_requests.*, departments.department_name, onboarding_details.*, ud.full_name as hod_name')
                    ->join('departments', 'departments.id = onboarding_requests.department_id')
                    ->join('onboarding_details', 'onboarding_details.request_id = onboarding_requests.id')
                    ->join('user_details ud', 'ud.user_id = onboarding_requests.hod_user_id', 'left');
        
        $requests = $query->orderBy('onboarding_requests.created_at', 'DESC')->findAll();

        $data = [
            'requests' => $requests,
            'roles' => $roles,
            'page_title' => 'Facilitator Panel'
        ];

        return view('onboarding/facilitator_tasks', $data);
    }

    public function updateSectionStatus()
    {
        $requestId = $this->request->getPost('request_id');
        $section = $this->request->getPost('section'); 
        $status = $this->request->getPost('status');

        if (!in_array($section, ['admin', 'hr', 'ict'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid section']);
        }

        $column = $section . '_status';
        $this->detailsModel->where('request_id', $requestId)->set([$column => $status])->update();

        return $this->response->setJSON(['success' => true]);
    }
}
