<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\AssetModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;
use App\Models\UserDetailModel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class Inventory extends BaseController
{
    protected $assetModel;
    protected $userModel;
    protected $departmentModel;
    protected $userDetailModel;

    public function __construct()
    {
        $this->assetModel = new AssetModel();
        $this->userModel = new UserModel();
        $this->departmentModel = new DepartmentModel();
        $this->userDetailModel = new UserDetailModel();
    }

    public function index()
    {
        $items = $this->assetModel->select('assets.*, departments.department_name, user_details.full_name as assigned_to')
            ->join('departments', 'departments.id = assets.department_id', 'left')
            ->join('user_details', 'user_details.user_id = assets.assigned_user_id', 'left')
            ->findAll();

        $data = [
            'items' => $items
        ];
        return view('inventory/items', $data);
    }

    public function items()
    {
        return $this->index();
    }

    public function view($id)
    {
        $item = $this->assetModel->find($id);
        if (!$item) {
            return redirect()->to('inventory')->with('error', 'Asset not found');
        }

        $data = [
            'item' => $item,
            'departments' => $this->departmentModel->findAll(),
            'users' => $this->userDetailModel->findAll()
        ];
        return view('inventory/view_item', $data);
    }

    public function store()
    {
        $rules = [
            'model' => 'required',
            'serial_number' => 'required|is_unique[assets.serial_number]',
            'asset_code' => 'required|is_unique[assets.asset_code]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->assetModel->save([
            'type' => $this->request->getPost('type'),
            'model' => $this->request->getPost('model'),
            'serial_number' => $this->request->getPost('serial_number'),
            'asset_code' => $this->request->getPost('asset_code'),
            'status' => 'In Store',
            'purchased_date' => date('Y-m-d')
        ]);

        return redirect()->to('inventory')->with('success', 'Asset created successfully');
    }

    public function updateAsset()
    {
        $id = $this->request->getPost('id');
        $data = [
            'asset_code' => $this->request->getPost('asset_code'),
            'status' => $this->request->getPost('status'),
            'department_id' => $this->request->getPost('department_id') ?: null,
            'assigned_user_id' => $this->request->getPost('assigned_user_id') ?: null,
            'notes' => $this->request->getPost('notes'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->assetModel->update($id, $data)) {
            return redirect()->back()->with('success', 'Asset updated successfully');
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

        $isRaw = $this->request->getGet('raw') === '1';

        try {
            // 1. Generate the Base QR Code
            $writer = new PngWriter();
            $qrObj = new QrCode(
                data: $url,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::Low,
                size: 250,
                margin: 0,
                roundBlockSizeMode: RoundBlockSizeMode::Margin
            );
            $qrResult = $writer->write($qrObj);
            if ($isRaw) {
                // Return just the raw QR code for on-screen display
                return $this->response
                    ->setHeader('Content-Type', $qrResult->getMimeType())
                    ->setBody($qrResult->getString());
            }

            // 2. Setup Canvas (400x520 for a professional label look)
            $qrImage = imagecreatefromstring($qrResult->getString());
            $width = 400;
            $height = 520;
            $canvas = imagecreatetruecolor($width, $height);

            // Colors
            $white = imagecolorallocate($canvas, 255, 255, 255);
            $black = imagecolorallocate($canvas, 0, 0, 0);
            $gray = imagecolorallocate($canvas, 80, 80, 80);
            $blue = imagecolorallocate($canvas, 30, 58, 138); // Corporate Blue

            imagefill($canvas, 0, 0, $white);

            // Font Path
            $fontBold = ROOTPATH . 'vendor/endroid/qr-code/assets/open_sans.ttf';

            // 3. Draw Header (Centered)
            $headerText = "CA OnEx System";
            $headerBox = imagettfbbox(22, 0, $fontBold, $headerText);
            $headerX = ($width - ($headerBox[2] - $headerBox[0])) / 2;
            imagettftext($canvas, 22, 0, $headerX, 60, $blue, $fontBold, $headerText);

            // 4. Draw QR Code (Centered)
            imagecopy($canvas, $qrImage, 75, 90, 0, 0, 250, 250);

            // 5. Draw Serial Number (Centered)
            $serialText = "S/N: " . ($item['serial_number'] ?? 'N/A');
            $serialBox = imagettfbbox(14, 0, $fontBold, $serialText);
            $serialX = ($width - ($serialBox[2] - $serialBox[0])) / 2;
            imagettftext($canvas, 14, 0, $serialX, 390, $black, $fontBold, $serialText);

            // 6. Draw Credits (Footer - Centered)
            $footerLine1 = "Designed and Developed by";
            $footerBox1 = imagettfbbox(10, 0, $fontBold, $footerLine1);
            $footerX1 = ($width - ($footerBox1[2] - $footerBox1[0])) / 2;

            $footerLine2 = "CA Sri Lanka ICTT Division";
            $footerBox2 = imagettfbbox(11, 0, $fontBold, $footerLine2);
            $footerX2 = ($width - ($footerBox2[2] - $footerBox2[0])) / 2;

            imagettftext($canvas, 10, 0, $footerX1, 450, $gray, $fontBold, $footerLine1);
            imagettftext($canvas, 11, 0, $footerX2, 475, $black, $fontBold, $footerLine2);

            // 7. Add Border
            imagesetthickness($canvas, 2);
            imagerectangle($canvas, 5, 5, $width - 5, $height - 5, $black);

            // 8. Output the final image
            ob_start();
            imagepng($canvas);
            $finalImage = ob_get_clean();

            // Cleanup
            imagedestroy($canvas);
            imagedestroy($qrImage);

            return $this->response
                ->setHeader('Content-Type', 'image/png')
                ->setBody($finalImage);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setBody("QR Generation Error: " . $e->getMessage());
        }
    }

    public function getDetails($id)
    {
        $item = $this->assetModel->find($id);
        if ($item) {
            return $this->response->setJSON(['success' => true, 'data' => $item]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Asset not found']);
    }
}
