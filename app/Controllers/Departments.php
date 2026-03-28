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
        $roles = (array)(session()->get('roles') ?: [session()->get('role')]);
        return (in_array('Super Admin', $roles) || in_array('HR Admin', $roles));
    }

    public function index()
    {
        if (!$this->checkAccess()) return redirect()->to('dashboard')->with('error', 'Access Denied');

        $data = [
            'departments' => $this->departmentModel->select('departments.*, user_details.full_name as manager_name')
                                ->join('users', 'users.id = departments.manager_id', 'left')
                                ->join('user_details', 'user_details.user_id = users.id', 'left')
                                ->findAll(),
            'users' => $this->userModel->select('users.id, user_details.full_name, GROUP_CONCAT(r.role_name SEPARATOR ", ") as role_name')
                        ->join('user_details', 'user_details.user_id = users.id')
                        ->join('user_roles ur', 'ur.user_id = users.id', 'left')
                        ->join('roles r', 'r.id = ur.role_id', 'left')
                        ->groupBy('users.id')
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
