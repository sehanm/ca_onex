<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AccessoryModel;
use App\Models\UserDetailModel;
use App\Models\DepartmentModel;

class Accessories extends BaseController
{
    protected $accessoryModel;
    protected $userDetailModel;
    protected $departmentModel;

    public function __construct()
    {
        $this->accessoryModel = new AccessoryModel();
        $this->userDetailModel = new UserDetailModel();
        $this->departmentModel = new DepartmentModel();
    }

    public function index()
    {
        $data = [
            'total' => $this->accessoryModel->countAllResults(),
            'by_status' => $this->accessoryModel->select('status, COUNT(*) as count')->groupBy('status')->findAll(),
            'by_category' => $this->accessoryModel->select('category, COUNT(*) as count')->groupBy('category')->findAll(),
            'page_title' => 'Accessories Overview'
        ];

        return view('accessories/dashboard', $data);
    }

    public function items()
    {
        $items = $this->accessoryModel->select('accessories.*, user_details.full_name as assigned_to')
            ->join('user_details', 'user_details.user_id = accessories.assigned_user_id', 'left')
            ->findAll();

        $data = [
            'items' => $items,
            'users' => $this->userDetailModel->orderBy('full_name', 'ASC')->findAll(),
            'page_title' => 'Accessories Inventory'
        ];

        return view('accessories/items', $data);
    }

    public function store()
    {
        $rules = [
            'category' => 'required',
            'model' => 'required',
            'asset_code' => 'required|is_unique[accessories.asset_code]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->accessoryModel->save([
            'category' => $this->request->getPost('category'),
            'brand' => $this->request->getPost('brand'),
            'model' => $this->request->getPost('model'),
            'serial_number' => $this->request->getPost('serial_number'),
            'asset_code' => $this->request->getPost('asset_code'),
            'status' => 'Stock',
            'notes' => $this->request->getPost('notes')
        ]);

        return redirect()->to('accessories/items')->with('success', 'Accessory added successfully');
    }

    public function assign()
    {
        $id = $this->request->getPost('accessory_id');
        $userId = $this->request->getPost('user_id');

        if (
            $this->accessoryModel->update($id, [
                'assigned_user_id' => $userId,
                'status' => 'Assigned',
                'updated_at' => date('Y-m-d H:i:s')
            ])
        ) {
            return redirect()->back()->with('success', 'Accessory assigned successfully');
        }

        return redirect()->back()->with('error', 'Failed to assign accessory');
    }

    public function returnToStock()
    {
        $id = $this->request->getPost('id');

        if (
            $this->accessoryModel->update($id, [
                'assigned_user_id' => null,
                'status' => 'Stock',
                'updated_at' => date('Y-m-d H:i:s')
            ])
        ) {
            return redirect()->back()->with('success', 'Accessory returned to stock');
        }

        return redirect()->back()->with('error', 'Failed to return accessory');
    }

    public function updateStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        $userId = $this->request->getPost('user_id');

        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Sync assignment with status
        if ($status !== 'Assigned') {
            $data['assigned_user_id'] = null;
        } elseif (!empty($userId)) {
            $data['assigned_user_id'] = $userId;
        }

        if ($this->accessoryModel->update($id, $data)) {
            $msg = ($status === 'Stock') ? 'Accessory returned to stock.' : 'Operational status synchronized successfully.';
            return redirect()->back()->with('success', $msg);
        }

        return redirect()->back()->with('error', 'Failed to synchronize hardware status.');
    }

    public function delete($id)
    {
        if ($this->accessoryModel->delete($id)) {
            return redirect()->to('accessories/items')->with('success', 'Accessory removed from directory');
        }
        return redirect()->to('accessories/items')->with('error', 'Failed to remove accessory');
    }
}
