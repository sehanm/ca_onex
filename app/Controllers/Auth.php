<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function index()
    {
        return view('auth/login');
    }

    public function login()
    {
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $session = session();
        $model = new \App\Models\UserModel();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $model->getUserWithDetails($username);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $ses_data = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'full_name' => $user['full_name'],
                    'role' => $user['system_role'],
                    'isLoggedIn' => TRUE,
                    'force_password_change' => $user['force_password_change']
                ];
                $session->set($ses_data);
                $this->logAction('Login', "User {$user['username']} logged in");

                if ($user['force_password_change']) {
                    return redirect()->to('/auth/change-password')->with('info', 'Please change your password to continue.');
                }

                return redirect()->to('/dashboard')->with('success', 'Successfully logged');
            } else {
                return redirect()->back()->with('error', 'Invalid password.');
            }
        } else {
            return redirect()->back()->with('error', 'Username not found.');
        }
    }

    public function changePassword()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }
        return view('auth/change_password');
    }

    public function updatePassword()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $session = session();
        $model = new \App\Models\UserModel();
        $userId = $session->get('id');

        $password = $this->request->getPost('password');
        $confirm_password = $this->request->getPost('confirm_password');

        if ($password !== $confirm_password) {
            return redirect()->back()->with('error', 'Passwords do not match.');
        }

        if (strlen($password) < 6) {
            return redirect()->back()->with('error', 'Password must be at least 6 characters long.');
        }

        $model->update($userId, [
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'force_password_change' => 0
        ]);

        $session->set('force_password_change', 0);
        $this->logAction('Password Changed', "User {$session->get('username')} changed their password");

        return redirect()->to('/dashboard')->with('success', 'Password successfully changed.');
    }

    public function logout()
    {
        $session = session();
        $username = $session->get('username');
        $session->destroy();
        if ($username) {
            $this->logAction('Logout', "User $username logged out");
        }
        return redirect()->to('/');
    }
}
