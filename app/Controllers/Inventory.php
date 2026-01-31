<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\AssetModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;
use App\Models\UserDetailModel;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class Inventory extends BaseController
{
    protected $assetModel;
    protected $userModel;
    protected $deptModel;

    public function __construct()
    {
        $this->assetModel = new AssetModel();
        $this->userModel = new UserModel();
        $this->deptModel = new DepartmentModel();
    }

    public function index()
    {
        $data = [
            'total_assets' => $this->assetModel->countAll(),
            'by_status'    => $this->assetModel->select('status, COUNT(*) as count')
                                ->groupBy('status')
                                ->findAll(),
            'page_title'   => 'Inventory Overview'
        ];
        return view('inventory/dashboard', $data);
    }

    public function store()
    {
        $rules = [
            'model'         => 'required|min_length[3]',
            'serial_number' => 'required|is_unique[assets.serial_number]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Invalid data or duplicate serial number.')->withInput();
        }

        $data = [
            'model'         => $this->request->getPost('model'),
            'serial_number' => $this->request->getPost('serial_number'),
            'asset_code'    => $this->request->getPost('asset_code'),
            'status'        => 'In Store'
        ];

        $this->assetModel->insert($data);
        return redirect()->to('inventory/items')->with('success', 'Asset registered successfully.');
    }

    public function items()
    {
        $data = [
            'items' => $this->assetModel->select('assets.*, departments.department_name, user_details.full_name as assigned_to')
                            ->join('departments', 'departments.id = assets.department_id', 'left')
                            ->join('user_details', 'user_details.user_id = assets.assigned_user_id', 'left')
                            ->orderBy('assets.created_at', 'DESC')
                            ->findAll(),
            'page_title' => 'Central Inventory'
        ];
        return view('inventory/items', $data);
    }

    public function view($id)
    {
        $item = $this->assetModel->select('assets.*, departments.department_name, user_details.full_name as assigned_to')
                        ->join('departments', 'departments.id = assets.department_id', 'left')
                        ->join('user_details', 'user_details.user_id = assets.assigned_user_id', 'left')
                        ->find($id);

        if (!$item) return redirect()->to('inventory/items')->with('error', 'Item not found');

        $data = [
            'item'        => $item,
            'departments' => $this->deptModel->findAll(),
            'users'       => (new UserDetailModel())->select('user_id, full_name')->findAll(),
            'page_title'  => 'Manage Asset: ' . $item['serial_number']
        ];

        return view('inventory/view_item', $data);
    }

    public function scan()
    {
        return view('inventory/scan', ['page_title' => 'Scan Asset QR']);
    }

    public function updateAsset()
    {
        $id = $this->request->getPost('id');
        if (!$id) return redirect()->back()->with('error', 'Missing asset ID');

        $deptId = $this->request->getPost('department_id');
        $userId = $this->request->getPost('assigned_user_id');

        // Sanitize IDs for database (empty string to NULL)
        $newData = [
            'status'           => $this->request->getPost('status'),
            'department_id'    => ($deptId !== "") ? $deptId : null,
            'assigned_user_id' => ($userId !== "") ? $userId : null,
            'asset_code'       => $this->request->getPost('asset_code'),
            'notes'            => $this->request->getPost('notes'),
        ];

        if ($this->assetModel->update($id, $newData)) {
            return redirect()->back()->with('success', 'Asset record updated successfully');
        }
        
        return redirect()->back()->with('error', 'Failed to update asset repository');
    }

    public function generateQR($id)
    {
        $item = $this->assetModel->find($id);
        if (!$item) {
            return $this->response->setStatusCode(404)->setBody("Asset not found");
        }

        $url = base_url('inventory/view/' . $id);
        
        try {
            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($url)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(ErrorCorrectionLevel::Low)
                ->size(200)
                ->margin(10)
                ->build();

            return $this->response
                ->setHeader('Content-Type', 'image/png')
                ->setBody($result->getString());
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setBody("QR Generation Error: " . $e->getMessage());
        }
    }
}
