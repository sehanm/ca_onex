<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\HardwareRequestModel;
use App\Models\AccessoryModel;
use App\Models\AssetModel;
use App\Models\UserDetailModel;

class HardwareRequests extends BaseController
{
    protected $requestModel;
    protected $accessoryModel;
    protected $assetModel;
    protected $userDetailModel;

    public function __construct()
    {
        $this->requestModel = new HardwareRequestModel();
        $this->accessoryModel = new AccessoryModel();
        $this->assetModel = new AssetModel();
        $this->userDetailModel = new UserDetailModel();
    }

    public function index()
    {
        $userId = session()->get('id');
        $data = [
            'requests' => $this->requestModel->getRequestsWithDetails($userId),
            'page_title' => 'My Hardware Requests'
        ];

        return view('hardware_requests/index', $data);
    }

    public function create()
    {
        $data = [
            'page_title' => 'Request Accessory or Asset'
        ];
        return view('hardware_requests/create', $data);
    }

    public function store()
    {
        $rules = [
            'item_type' => 'required',
            'category' => 'required',
            'requirement_type' => 'required',
            'reason' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->requestModel->save([
            'user_id' => session()->get('id'),
            'item_type' => $this->request->getPost('item_type'),
            'category' => $this->request->getPost('category'),
            'requirement_type' => $this->request->getPost('requirement_type'),
            'reason' => $this->request->getPost('reason'),
            'status' => 'pending',
            'request_date' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('hardware-requests')->with('success', 'Hardware request submitted successfully');
    }

    public function overview()
    {
        $allRequests = $this->requestModel->getRequestsWithDetails();

        $stats = [
            'total' => count($allRequests),
            'pending' => count(array_filter($allRequests, fn($r) => $r['status'] == 'pending')),
            'assigned' => count(array_filter($allRequests, fn($r) => $r['status'] == 'assigned')),
            'returned' => count(array_filter($allRequests, fn($r) => $r['status'] == 'returned')),
            'rejected' => count(array_filter($allRequests, fn($r) => $r['status'] == 'rejected')),
            'asset' => count(array_filter($allRequests, fn($r) => $r['item_type'] == 'asset')),
            'accessory' => count(array_filter($allRequests, fn($r) => $r['item_type'] == 'accessory')),
        ];

        $data = [
            'title' => 'Hardware Overview',
            'stats' => $stats,
            'recent_requests' => array_slice($allRequests, 0, 5)
        ];

        return view('hardware_requests/overview', $data);
    }

    public function admin_index()
    {
        // Check for ICT / Admin role if needed
        $data = [
            'requests' => $this->requestModel->getRequestsWithDetails(),
            'page_title' => 'Hardware Request Management'
        ];

        return view('hardware_requests/admin_index', $data);
    }

    public function process_scan()
    {
        $qrData = $this->request->getPost('qr_data');
        $requestId = $this->request->getPost('request_id');
        $action = $this->request->getPost('action'); // 'assign' or 'return'

        // 1. Identify the item from QR data (asset_code)
        $item = $this->accessoryModel->where('asset_code', $qrData)->first();
        $type = 'accessory';

        if (!$item) {
            $item = $this->assetModel->where('asset_code', $qrData)->first();
            $type = 'asset';
        }

        if (!$item) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid QR Code or Item Not Found']);
        }

        if ($action === 'assign') {
            return $this->handle_assign($item, $type, $requestId);
        } elseif ($action === 'return') {
            return $this->handle_return($item, $type);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid Action']);
    }

    public function item_details()
    {
        $code = $this->request->getGet('code');
        if (!$code)
            return $this->response->setJSON(['status' => 'error', 'message' => 'No code provided']);

        // Try Accessory
        $item = $this->accessoryModel->select('accessories.*, user_details.full_name as assigned_to_name')
            ->join('user_details', 'user_details.user_id = accessories.assigned_user_id', 'left')
            ->where('asset_code', $code)
            ->first();
        $type = 'accessory';

        if (!$item) {
            // Try Asset
            $item = $this->assetModel->select('assets.*, user_details.full_name as assigned_to_name')
                ->join('user_details', 'user_details.user_id = assets.assigned_user_id', 'left')
                ->where('asset_code', $code)
                ->first();
            $type = 'asset';
        }

        if ($item) {
            return $this->response->setJSON([
                'status' => 'success',
                'item' => $item,
                'itemType' => $type
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Item not found']);
    }

    public function pending_json()
    {
        $type = $this->request->getGet('type'); // 'accessory' or 'asset'

        $builder = $this->requestModel->select('hardware_requests.*, user_details.full_name as requester_name')
            ->join('user_details', 'user_details.user_id = hardware_requests.user_id')
            ->where('hardware_requests.status', 'pending');

        if ($type) {
            $builder->where('item_type', $type);
        }

        $requests = $builder->orderBy('created_at', 'DESC')->findAll();

        // Add formatted date
        foreach ($requests as &$req) {
            $req['time_ago'] = date('M d, Y', strtotime($req['created_at']));
        }

        return $this->response->setJSON($requests);
    }

    public function scanner_data()
    {
        // 1. Pending Requests (To be assigned)
        $pending = $this->requestModel->select('hardware_requests.*, user_details.full_name as requester_name')
            ->join('user_details', 'user_details.user_id = hardware_requests.user_id')
            ->where('hardware_requests.status', 'pending')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // 2. Active Assignments (To be returned)
        $assigned = $this->requestModel->select('hardware_requests.*, user_details.full_name as requester_name')
            ->join('user_details', 'user_details.user_id = hardware_requests.user_id')
            ->where('hardware_requests.status', 'assigned')
            ->orderBy('updated_at', 'DESC')
            ->findAll();

        // Link items to assigned requests
        foreach ($assigned as &$req) {
            if ($req['item_type'] === 'asset') {
                $req['item'] = $this->assetModel->find($req['assigned_item_id']);
            } else {
                $req['item'] = $this->accessoryModel->find($req['assigned_item_id']);
            }
            $req['time_ago'] = date('M d, Y', strtotime($req['updated_at']));
        }

        foreach ($pending as &$req) {
            $req['time_ago'] = date('M d, Y', strtotime($req['created_at']));
        }

        return $this->response->setJSON([
            'pending' => $pending,
            'assigned' => $assigned
        ]);
    }

    private function handle_assign($item, $type, $requestId)
    {
        $request = $this->requestModel->find($requestId);
        if (!$request)
            return $this->response->setJSON(['status' => 'error', 'message' => 'Request not found']);

        // Check if item is available
        if ($item['status'] !== 'Stock' && $item['status'] !== 'Available') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Item is already ' . $item['status']]);
        }

        // Update Request
        $this->requestModel->update($requestId, [
            'status' => 'assigned',
            'assigned_item_id' => $item['id'],
            'admin_id' => session()->get('id'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Update Item Status
        if ($type === 'accessory') {
            $this->accessoryModel->update($item['id'], [
                'status' => 'Assigned',
                'assigned_user_id' => $request['user_id']
            ]);
        } else {
            $this->assetModel->update($item['id'], [
                'status' => 'Assigned',
                'assigned_user_id' => $request['user_id']
            ]);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Item assigned successfully']);
    }

    private function handle_return($item, $type)
    {
        // 1. Find the active request/assignment for this item
        $request = $this->requestModel->where('assigned_item_id', $item['id'])
            ->where('item_type', $type)
            ->where('status', 'assigned')
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($request) {
            $this->requestModel->update($request['id'], [
                'status' => 'returned',
                'return_date' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        // 2. Clear assignment in primary tables
        if ($type === 'accessory') {
            $this->accessoryModel->update($item['id'], [
                'status' => 'Stock',
                'assigned_user_id' => null
            ]);
        } else {
            $this->assetModel->update($item['id'], [
                'status' => 'Stock',
                'assigned_user_id' => null
            ]);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Item returned and added to stock']);
    }

    public function reject()
    {
        $requestId = $this->request->getPost('request_id');
        $this->requestModel->update($requestId, [
            'status' => 'rejected',
            'admin_id' => session()->get('id'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return redirect()->back()->with('success', 'Request rejected.');
    }

    public function scanner()
    {
        return view('hardware_requests/scanner');
    }
}
