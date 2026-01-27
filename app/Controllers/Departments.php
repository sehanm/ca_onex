<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DepartmentModel;
use App\Models\UserModel;

class Departments extends BaseController
{
    protected $departmentModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->departmentModel = new DepartmentModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }

    private function checkAccess()
    {
        $role = session()->get('role');
        return ($role === 'Super Admin' || $role === 'HR Admin');
    }

    public function index()
    {
        if (!$this->checkAccess()) return redirect()->to('dashboard')->with('error', 'Access Denied');

        $data = [
            'departments' => $this->departmentModel->select('departments.*, user_details.full_name as manager_name')
                                ->join('users', 'users.id = departments.manager_id', 'left')
                                ->join('user_details', 'user_details.user_id = users.id', 'left')
                                ->findAll(),
            'users' => $this->userModel->select('users.id, user_details.full_name, r.role_name')
                        ->join('user_details', 'user_details.user_id = users.id')
                        ->join('roles r', 'r.id = users.system_role_id', 'left')
                        ->findAll(),
            'page_title' => 'Manage Departments'
        ];

        return view('admin/department_manage', $data);
    }

    public function updateManager()
    {
        if (!$this->checkAccess()) return redirect()->back()->with('error', 'Access Denied');

        $deptId = $this->request->getPost('department_id');
        $managerId = $this->request->getPost('manager_id');
        
        // Validation simple
        if (!$deptId) return redirect()->back()->with('error', 'Invalid Department');

        $updateData = ['manager_id' => !empty($managerId) ? $managerId : null];
        $this->departmentModel->update($deptId, $updateData);

        $dept = $this->departmentModel->find($deptId);
        $this->logAction('Department Updated', "Updated manager for department: {$dept['department_name']}");

        return redirect()->back()->with('success', 'Department manager updated successfully');
    }
}
