<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\DepartmentModel;
use App\Models\AuditLogModel;

class Admin extends BaseController
{
    protected $userModel;
    protected $departmentModel;
    protected $auditModel;
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->departmentModel = new DepartmentModel();
        $this->auditModel = new AuditLogModel();
        $this->db = \Config\Database::connect();
    }

    private function checkAdmin()
    {
        if (session()->get('role') !== 'Super Admin') {
            return false;
        }
        return true;
    }

    public function index()
    {
        if (!$this->checkAdmin())
            return redirect()->to('dashboard')->with('error', 'Access Denied');

        $data = [
            'users' => $this->userModel->select('users.*, user_details.full_name, user_details.email, r.role_name as system_role, d.department_name')
                ->join('user_details', 'user_details.user_id = users.id')
                ->join('roles r', 'r.id = users.system_role_id')
                ->join('departments d', 'd.id = user_details.department_id', 'left')
                ->findAll(),
            'page_title' => 'User Management'
        ];

        return view('admin/user_manage', $data);
    }

    public function create()
    {
        if (!$this->checkAdmin())
            return redirect()->to('dashboard');

        $data = [
            'roles' => $this->db->table('roles')->where('role_type', 'system')->get()->getResultArray(),
            'div_roles' => $this->db->table('roles')->where('role_type', 'divisional')->get()->getResultArray(),
            'departments' => $this->departmentModel->findAll(),
            'page_title' => 'Create User'
        ];
        return view('admin/user_create', $data);
    }

    public function store()
    {
        if (!$this->checkAdmin())
            return redirect()->to('dashboard');

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userData = [
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'system_role_id' => $this->request->getPost('system_role'),
            'divisional_role_id' => $this->request->getPost('divisional_role'),
            'force_password_change' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->userModel->insert($userData);
        $userId = $this->userModel->getInsertID();

        $userDetails = [
            'user_id' => $userId,
            'full_name' => $this->request->getPost('full_name'),
            'epf_number' => $this->request->getPost('epf_number'),
            'email' => $this->request->getPost('email'),
            'department_id' => $this->request->getPost('department'),
        ];
        $this->db->table('user_details')->insert($userDetails);

        $this->logAction('User Created', "Created user $username");

        // Send email if toggled
        $emailSent = false;
        if ($this->request->getPost('send_email') == '1') {
            $emailSent = $this->sendUserCreationEmail($userDetails['email'], $userDetails['full_name'], $username, $password);
        }

        $msg = 'User created successfully';
        if ($this->request->getPost('send_email') == '1') {
            $msg .= $emailSent ? ' and confirmation email sent.' : ' but failed to send confirmation email.';
        }

        return redirect()->to('admin/users')->with('success', $msg);
    }

    public function edit($id)
    {
        if (!$this->checkAdmin())
            return redirect()->to('dashboard');

        $user = $this->userModel->select('users.*, user_details.full_name, user_details.epf_number, user_details.email, user_details.department_id')
            ->join('user_details', 'user_details.user_id = users.id')
            ->where('users.id', $id)
            ->first();

        $data = [
            'user' => $user,
            'roles' => $this->db->table('roles')->where('role_type', 'system')->get()->getResultArray(),
            'div_roles' => $this->db->table('roles')->where('role_type', 'divisional')->get()->getResultArray(),
            'departments' => $this->departmentModel->findAll(),
            'page_title' => 'Edit User'
        ];
        return view('admin/user_edit', $data);
    }

    public function update($id)
    {
        if (!$this->checkAdmin())
            return redirect()->to('dashboard');

        $currentUser = $this->userModel->find($id);
        $changes = [];

        // Check for role change
        if ($currentUser['system_role_id'] != $this->request->getPost('system_role')) {
            $changes[] = "System Role changed";
        }

        // Update User Table
        $userData = [
            'system_role_id' => $this->request->getPost('system_role'),
            'divisional_role_id' => $this->request->getPost('divisional_role'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Check password change
        $newPass = $this->request->getPost('password');
        if (!empty($newPass)) {
            $userData['password'] = password_hash($newPass, PASSWORD_DEFAULT);
            $userData['force_password_change'] = 1;
            $changes[] = "Password changed";
        }

        $this->userModel->update($id, $userData);

        // Update Details
        $userDetails = [
            'full_name' => $this->request->getPost('full_name'),
            'epf_number' => $this->request->getPost('epf_number'),
            'email' => $this->request->getPost('email'),
            'department_id' => $this->request->getPost('department'),
        ];

        // Detailed check could be done here, simplification for now
        $this->db->table('user_details')->where('user_id', $id)->update($userDetails);

        if (!empty($changes)) {
            $this->logAction('User Updated', "Updated user {$currentUser['username']}: " . implode(', ', $changes));
        } else {
            $this->logAction('User Updated', "Updated details for user {$currentUser['username']}");
        }

        return redirect()->to('admin/users')->with('success', 'User updated successfully');
    }

    public function auditLogs()
    {
        if (!$this->checkAdmin())
            return redirect()->to('dashboard');

        $data = [
            'logs' => $this->auditModel->getLogs(),
            'page_title' => 'Audit Logs'
        ];

        return view('admin/audit_logs', $data);
    }

    public function delete($id)
    {
        if (!$this->checkAdmin())
            return redirect()->to('dashboard');

        $user = $this->userModel->find($id);
        if ($user) {
            $this->userModel->delete($id);
            $this->logAction('User Deleted', "Deleted user: {$user['username']}");

            return redirect()->to('admin/users')->with('success', 'User deleted successfully');
        }

        return redirect()->to('admin/users')->with('error', 'User not found');
    }

}
