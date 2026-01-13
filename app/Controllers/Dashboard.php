<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $data = [
            'username' => $session->get('username'),
            'role'     => $session->get('role'),
            'full_name'=> $session->get('full_name'),
            'page_title' => 'Dashboard'
        ];

        return view('dashboard/index', $data);
    }
}
