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
        $roles = session()->get('roles') ?: [session()->get('role')];
        if (!in_array('Super Admin', (array)$roles)) {
            return false;
        }
        return true;
    }

    public function index()
    {
        if (!$this->checkAdmin())
            return redirect()->to('dashboard')->with('error', 'Access Denied');

        $users = $this->userModel->select('users.*, user_details.full_name, user_details.email, d.department_name')
            ->join('user_details', 'user_details.user_id = users.id')
            ->join('departments d', 'd.id = user_details.department_id', 'left')
            ->findAll();

        foreach ($users as &$user) {
            $user['roles'] = $this->db->table('user_roles')
                ->select('roles.role_name, roles.role_type')
                ->join('roles', 'roles.id = user_roles.role_id')
                ->where('user_roles.user_id', $user['id'])
                ->get()
                ->getResultArray();
            $user['role_names'] = implode(', ', array_column($user['roles'], 'role_name'));
        }

        $data = [
            'users' => $users,
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
            'force_password_change' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->userModel->insert($userData);
        $userId = $this->userModel->getInsertID();

        // Save Roles
        $systemRoles = (array) $this->request->getPost('system_role');
        $divisionalRoles = (array) $this->request->getPost('divisional_role');
        $allRoles = array_merge($systemRoles, $divisionalRoles);
        
        foreach ($allRoles as $roleId) {
            if (!empty($roleId)) {
                $this->db->table('user_roles')->insert([
                    'user_id' => $userId,
                    'role_id' => $roleId
                ]);
            }
        }

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

        $user = $this->userModel->getUserByIdWithDetails($id);

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

        // Update User Table
        $userData = [];

        // Check password change
        $newPass = $this->request->getPost('password');
        if (!empty($newPass)) {
            $userData['password'] = password_hash($newPass, PASSWORD_DEFAULT);
            $userData['force_password_change'] = 1;
            $changes[] = "Password changed";
        }

        if (!empty($userData)) {
            $this->userModel->update($id, $userData);
        }

        // Update Roles
        $systemRoles = (array) $this->request->getPost('system_role');
        $divisionalRoles = (array) $this->request->getPost('divisional_role');
        $newRoles = array_filter(array_merge($systemRoles, $divisionalRoles));
        
        // Remove old roles
        $this->db->table('user_roles')->where('user_id', $id)->delete();
        
        // Add new roles
        foreach ($newRoles as $roleId) {
            $this->db->table('user_roles')->insert([
                'user_id' => $id,
                'role_id' => $roleId
            ]);
        }
        $changes[] = "Roles updated";

        // Update Details
        $userDetails = [
            'full_name' => $this->request->getPost('full_name'),
            'epf_number' => $this->request->getPost('epf_number'),
            'email' => $this->request->getPost('email'),
            'department_id' => $this->request->getPost('department'),
        ];

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
