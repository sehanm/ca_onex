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

        $userId = $session->get('id');

        $assetModel = new \App\Models\AssetModel();
        $assignedAssets = $assetModel->where('assigned_user_id', $userId)->findAll();

        $accessoryModel = new \App\Models\AccessoryModel();
        $assignedAccessories = $accessoryModel->where('assigned_user_id', $userId)->findAll();

        $hrModel = new \App\Models\HardwareRequestModel();
        $recentRequests = $hrModel->getRequestsWithDetails($userId);

        $role = $session->get('role');
        $isAdmin = in_array($role, ['Super Admin', 'HR Admin', 'ICT Admin']);

        $data = [
            'username' => $session->get('username'),
            'role' => $role,
            'full_name' => $session->get('full_name'),
            'page_title' => 'Dashboard',
            'assignedAssets' => $assignedAssets,
            'assignedAccessories' => $assignedAccessories,
            'recentRequests' => $recentRequests,
            'totalEmployees' => 0,
            'totalAssets' => 0,
            'totalAccessories' => 0,
            'pendingRequests' => 0
        ];

        if ($isAdmin) {
            $userModel = new \App\Models\UserModel();
            $data['totalEmployees'] = $userModel->countAllResults();
            $data['totalAssets'] = $assetModel->countAllResults();
            $data['totalAccessories'] = $accessoryModel->countAllResults();
            
            // Note: CodeIgniter Builder retains the previous query. But countAllResults() resets it. 
            // However, getRequestsWithDetails earlier did not use countAllResults. But we instantiated $hrModel.
            $data['pendingRequests'] = clone $hrModel;
            $data['pendingRequests'] = $hrModel->whereIn('status', ['pending', 'Pending'])->countAllResults();
        }

        return view('dashboard/index', $data);
    }
}
