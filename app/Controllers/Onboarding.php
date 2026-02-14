<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OnboardingRequestModel;
use App\Models\DepartmentModel;
use App\Models\UserModel;

use App\Models\OnboardingDetailsModel;
use App\Models\AssetModel;
use App\Models\OnboardingAssignmentModel;

class Onboarding extends BaseController
{
    protected $onboardingModel;
    protected $detailsModel;
    protected $departmentModel;
    protected $userModel;
    protected $assetModel;
    protected $assignmentModel;
    protected $db;

    public function __construct()
    {
        $this->onboardingModel = new OnboardingRequestModel();
        $this->detailsModel = new OnboardingDetailsModel();
        $this->departmentModel = new DepartmentModel();
        $this->userModel = new UserModel();
        $this->assetModel = new AssetModel();
        $this->assignmentModel = new OnboardingAssignmentModel();
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
            $this->logAction('Onboarding Initiated', "Initiated onboarding for {$data['candidate_name']}");

            // Send Email Notifications
            try {
                // 1. Get HOD Details
                $hod = $this->userModel->getUserByIdWithDetails($data['hod_user_id']);
                $hodEmail = $hod['email'] ?? null;

                // 2. Get Initiator (HR Admin) Details
                $initiator = $this->userModel->getUserByIdWithDetails(session()->get('id'));
                $initiatorEmail = $initiator['email'] ?? null;
                $initiatorName = session()->get('full_name');

                // 3. Get HR HOD Email (Manager of HR department)
                $hrDept = $this->departmentModel->where('department_name', 'HR')->first();
                $hrHodEmail = null;
                if ($hrDept && !empty($hrDept['manager_id'])) {
                    $hrHod = $this->userModel->getUserByIdWithDetails($hrDept['manager_id']);
                    $hrHodEmail = $hrHod['email'] ?? null;
                }

                // Prepare Content Data
                $dept = $this->departmentModel->find($data['department_id']);
                $candidateData = [
                    'candidate_name' => $data['candidate_name'],
                    'designation' => $data['designation'],
                    'joining_date' => $data['joining_date'],
                    'department_name' => $dept['department_name'] ?? 'Unknown'
                ];

                // CC List: HR HOD + Initiator
                $ccs = [];
                if ($hrHodEmail && $hrHodEmail !== $hodEmail)
                    $ccs[] = $hrHodEmail;
                if ($initiatorEmail && $initiatorEmail !== $hodEmail && !in_array($initiatorEmail, $ccs))
                    $ccs[] = $initiatorEmail;

                // Only send if toggle is ON
                if ($hodEmail && $this->request->getPost('send_email') == '1') {
                    $this->sendOnboardingInitiateEmail($hodEmail, $ccs, $candidateData, $initiatorName);
                }
            } catch (\Exception $e) {
                // Log and continue
                log_message('error', 'Onboarding Email Error: ' . $e->getMessage());
            }

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
            'hr_sim' => $this->request->getPost('hr_sim') ? 1 : 0,

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

            // Section statuses - auto complete if nothing requested
            'admin_status' => ($this->request->getPost('admin_chair') || $this->request->getPost('admin_table') || $this->request->getPost('admin_phone')) ? 'Pending' : 'Completed',
            'hr_status' => ($this->request->getPost('hr_mobile') || $this->request->getPost('hr_sim')) ? 'Pending' : 'Completed',
            'ict_status' => ($this->request->getPost('ict_desktop_laptop') !== 'None' || $this->request->getPost('ict_printer') ||
                $this->request->getPost('soft_smms') || $this->request->getPost('soft_receipt') ||
                $this->request->getPost('soft_training') || $this->request->getPost('soft_ecole') ||
                $this->request->getPost('soft_pronto') || $this->request->getPost('soft_ims') ||
                $this->request->getPost('soft_sap') || $this->request->getPost('soft_imeet')) ? 'Pending' : 'Completed',
        ];

        // Save details (Upsert)
        $existing = $this->detailsModel->where('request_id', $id)->first();
        if ($existing) {
            $this->detailsModel->update($existing['id'], $detailsData);
        } else {
            $this->detailsModel->insert($detailsData);
        }

        // Update Request Status
        $this->onboardingModel->update($id, ['status' => 'Processing']);

        $this->logAction('Facility Request Submitted', "HOD submitted facility request for candidate: {$request['candidate_name']}");

        return redirect()->to('onboarding/pending')->with('success', 'Facility Request Submitted Successfully.');
    }
    public function facilitatorTasks()
    {
        $userId = session()->get('id');
        $userRole = session()->get('role');

        $roles = $this->checkFacilitatorAccess();

        if (empty($roles)) {
            return redirect()->to('dashboard')->with('error', 'You do not have facilitator access.');
        }

        $query = $this->onboardingModel->select('onboarding_requests.*, departments.department_name, onboarding_details.*, ud.full_name as hod_name, 
                    onboarding_details.admin_status, onboarding_details.hr_status, onboarding_details.ict_status')
            ->join('departments', 'departments.id = onboarding_requests.department_id')
            ->join('onboarding_details', 'onboarding_details.request_id = onboarding_requests.id')
            ->join('user_details ud', 'ud.user_id = onboarding_requests.hod_user_id', 'left');

        // Restriction logic: Only show requests that have items for the facilitator's roles
        if ($userRole !== 'Super Admin') {
            $query->groupStart();
            $orUsed = false;

            if (in_array('Admin', $roles)) {
                $query->groupStart()
                    ->where('admin_chair', 1)
                    ->orWhere('admin_table', 1)
                    ->orWhere('admin_phone', 1)
                    ->groupEnd();
                $orUsed = true;
            }

            if (in_array('HR', $roles)) {
                if ($orUsed)
                    $query->orGroupStart();
                else
                    $query->groupStart();
                $query->where('hr_mobile', 1)
                    ->orWhere('hr_sim', 1)
                    ->groupEnd();
                $orUsed = true;
            }

            if (in_array('ICT', $roles)) {
                if ($orUsed)
                    $query->orGroupStart();
                else
                    $query->groupStart();
                $query->where('ict_desktop_laptop !=', 'None')
                    ->orWhere('ict_printer', 1)
                    ->orWhere('soft_smms', 1)
                    ->orWhere('soft_receipt', 1)
                    ->orWhere('soft_training', 1)
                    ->orWhere('soft_ecole', 1)
                    ->orWhere('soft_pronto', 1)
                    ->orWhere('soft_ims', 1)
                    ->orWhere('soft_sap', 1)
                    ->orWhere('soft_imeet', 1)
                    ->orWhere('access_copy_user !=', '')
                    ->groupEnd();
            }
            $query->groupEnd();
        }

        $requests = $query->orderBy('onboarding_requests.created_at', 'DESC')->findAll();

        $data = [
            'requests' => $requests,
            'roles' => $roles,
            'page_title' => 'Facilitator Panel'
        ];

        return view('onboarding/facilitator_tasks', $data);
    }

    private function checkFacilitatorAccess()
    {
        $userId = session()->get('id');
        $userRole = session()->get('role');

        $managedDepts = $this->departmentModel->where('manager_id', $userId)->findAll();
        $roles = [];
        foreach ($managedDepts as $dept) {
            if ($dept['department_name'] == 'Administration & Events')
                $roles[] = 'Admin';
            if ($dept['department_name'] == 'HR')
                $roles[] = 'HR';
            if ($dept['department_name'] == 'ICT')
                $roles[] = 'ICT';
        }

        // Super Admin gets all facilitator roles by default
        if ($userRole === 'Super Admin') {
            return ['Admin', 'HR', 'ICT'];
        }

        return $roles;
    }

    public function updateSectionStatus()
    {
        $roles = $this->checkFacilitatorAccess();
        if (empty($roles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access Denied: Facilitator only.']);
        }

        $requestId = $this->request->getPost('request_id');
        $section = $this->request->getPost('section');
        $status = $this->request->getPost('status');

        if (!in_array($section, ['admin', 'hr', 'ict'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid section']);
        }

        // Extra check: Can they update THIS specific section?
        $roleMap = ['admin' => 'Admin', 'hr' => 'HR', 'ict' => 'ICT'];
        if (!in_array($roleMap[$section], $roles)) {
            return $this->response->setJSON(['success' => false, 'message' => "Access Denied: You cannot update $section tasks."]);
        }

        $column = $section . '_status';
        $this->detailsModel->where('request_id', $requestId)->set([$column => $status])->update();

        // Check if all sections are completed to update the main request status
        $details = $this->detailsModel->where('request_id', $requestId)->first();

        // ADMIN Check
        $adminHasItems = ($details['admin_chair'] == 1 || $details['admin_table'] == 1 || $details['admin_phone'] == 1);
        $adminDone = ($details['admin_status'] === 'Completed' || !$adminHasItems);

        // HR Check
        $hrHasItems = ($details['hr_mobile'] == 1 || $details['hr_sim'] == 1);
        $hrDone = ($details['hr_status'] === 'Completed' || !$hrHasItems);

        // ICT Check (Robust)
        $ictHasItems = ($details['ict_desktop_laptop'] !== 'None' ||
            $details['ict_printer'] == 1 ||
            $details['soft_smms'] == 1 ||
            $details['soft_receipt'] == 1 ||
            $details['soft_training'] == 1 ||
            $details['soft_ecole'] == 1 ||
            $details['soft_pronto'] == 1 ||
            $details['soft_ims'] == 1 ||
            $details['soft_sap'] == 1 ||
            $details['soft_imeet'] == 1 ||
            !empty($details['access_copy_user']));

        $ictDone = ($details['ict_status'] === 'Completed' || !$ictHasItems);

        // Calculate Global Status
        $mainStatus = ($adminDone && $hrDone && $ictDone) ? 'Completed' : 'Processing';

        $this->onboardingModel->update($requestId, ['status' => $mainStatus]);

        $request = $this->onboardingModel->find($requestId);
        $this->logAction('Facilitator Task Updated', "Updated $section status to $status for candidate: {$request['candidate_name']}");

        return $this->response->setJSON([
            'success' => true,
            'new_status' => $mainStatus
        ]);
    }

    public function getAssetByCode()
    {
        $code = $this->request->getGet('code');
        if (!$code) {
            return $this->response->setJSON(['success' => false, 'message' => 'No scan data provided']);
        }

        // Try to find by id (primary key), asset code, or serial number
        $asset = $this->assetModel->groupStart()
            ->where('id', $code)
            ->orWhere('asset_code', $code)
            ->orWhere('serial_number', $code)
            ->groupEnd()
            ->first();

        if ($asset) {
            return $this->response->setJSON(['success' => true, 'asset' => $asset]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Asset not found']);
    }

    public function assignAsset()
    {
        $requestId = $this->request->getPost('request_id');
        $assetId = $this->request->getPost('asset_id');

        if (!$requestId || !$assetId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required data']);
        }

        $asset = $this->assetModel->find($assetId);
        if (!$asset) {
            return $this->response->setJSON(['success' => false, 'message' => 'Asset not found']);
        }

        // Update onboarding_details
        $data = [
            'ict_asset_id' => $asset['id'],
            'ict_model' => $asset['model'],
            'ict_serial_number' => $asset['serial_number'],
            'ict_asset_code' => $asset['asset_code']
        ];

        $this->detailsModel->where('request_id', $requestId)->set($data)->update();

        // Track in onboarding_asset_assignments (Upsert logic)
        $existingAssignment = $this->assignmentModel
            ->where('request_id', $requestId)
            ->where('assignment_type', 'Computer')
            ->first();

        if ($existingAssignment) {
            $this->assignmentModel->update($existingAssignment['id'], [
                'asset_id' => $assetId
            ]);
        } else {
            $this->assignmentModel->insert([
                'request_id' => $requestId,
                'asset_id' => $assetId,
                'assignment_type' => 'Computer'
            ]);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Asset updated successfully']);
    }
}
