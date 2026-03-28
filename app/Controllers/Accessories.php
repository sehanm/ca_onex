<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AccessoryModel;
use App\Models\UserDetailModel;
use App\Models\DepartmentModel;
use App\Models\HardwareRequestModel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class Accessories extends BaseController
{
    protected $accessoryModel;
    protected $userDetailModel;
    protected $departmentModel;
    protected $hardwareRequestModel;
    protected $db;

    public function __construct()
    {
        $this->accessoryModel = new AccessoryModel();
        $this->userDetailModel = new UserDetailModel();
        $this->departmentModel = new DepartmentModel();
        $this->hardwareRequestModel = new HardwareRequestModel();
        $this->db = \Config\Database::connect();
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

        $this->logAction('Accessory Added', "Added {$this->request->getPost('category')} model {$this->request->getPost('model')}");
        return redirect()->to('accessories/items')->with('success', 'Accessory added successfully');
    }

    public function assign()
    {
        $id = $this->request->getPost('accessory_id');
        $userId = $this->request->getPost('user_id');

        $item = $this->accessoryModel->find($id);
        if (!$item) {
            return redirect()->back()->with('error', 'Accessory not found');
        }

        // Update Accessory Status
        $this->db->transStart();

        $this->accessoryModel->update($id, [
            'assigned_user_id' => $userId,
            'status' => 'Assigned',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Check for a pending hardware request for this user and category
        $pendingRequest = $this->hardwareRequestModel
            ->where('user_id', $userId)
            ->where('item_type', 'accessory')
            ->where('category', $item['category'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'ASC')
            ->first();

        if ($pendingRequest) {
            $this->hardwareRequestModel->update($pendingRequest['id'], [
                'status' => 'assigned',
                'assigned_item_id' => $id,
                'admin_id' => session()->get('id'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Optional: Create a "system-generated" request for tracking if none exists
            $this->hardwareRequestModel->save([
                'user_id' => $userId,
                'item_type' => 'accessory',
                'category' => $item['category'],
                'requirement_type' => 'fixed',
                'reason' => 'Direct assignment from Accessories module',
                'status' => 'assigned',
                'assigned_item_id' => $id,
                'admin_id' => session()->get('id'),
                'request_date' => date('Y-m-d H:i:s')
            ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Failed to assign accessory');
        }

        $this->logAction('Accessory Assigned', "Assigned accessory {$item['asset_code']} to user ID $userId");
        return redirect()->back()->with('success', 'Accessory assigned and stock updated');
    }

    public function returnToStock()
    {
        $id = $this->request->getPost('id');

        $this->db->transStart();

        // 1. Update Accessory Status
        $this->accessoryModel->update($id, [
            'assigned_user_id' => null,
            'status' => 'Stock',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // 2. Find and update the active hardware request
        $activeRequest = $this->hardwareRequestModel
            ->where('assigned_item_id', $id)
            ->where('item_type', 'accessory')
            ->where('status', 'assigned')
            ->orderBy('updated_at', 'DESC')
            ->first();

        if ($activeRequest) {
            $this->hardwareRequestModel->update($activeRequest['id'], [
                'status' => 'returned',
                'return_date' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Failed to return accessory');
        }

        $this->logAction('Accessory Returned', "Returned accessory ID $id to stock");
        return redirect()->back()->with('success', 'Accessory returned to stock and request closed');
    }

    public function updateStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        $userId = $this->request->getPost('user_id');

        $item = $this->accessoryModel->find($id);
        if (!$item) {
            return redirect()->back()->with('error', 'Accessory not found.');
        }

        $this->db->transStart();

        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Sync assignment with status
        if ($status !== 'Assigned') {
            $data['assigned_user_id'] = null;

            // If it was assigned before and now it's not, mark request as returned if it's back to stock
            if ($item['status'] === 'Assigned' && ($status === 'Stock' || $status === 'Damaged')) {
                $activeRequest = $this->hardwareRequestModel
                    ->where('assigned_item_id', $id)
                    ->where('item_type', 'accessory')
                    ->where('status', 'assigned')
                    ->first();

                if ($activeRequest) {
                    $this->hardwareRequestModel->update($activeRequest['id'], [
                        'status' => ($status === 'Stock' ? 'returned' : 'rejected'),
                        'return_date' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        } elseif (!empty($userId)) {
            $data['assigned_user_id'] = $userId;

            // If newly assigned via status update
            if ($item['status'] !== 'Assigned') {
                $pendingRequest = $this->hardwareRequestModel
                    ->where('user_id', $userId)
                    ->where('item_type', 'accessory')
                    ->where('category', $item['category'])
                    ->where('status', 'pending')
                    ->first();

                if ($pendingRequest) {
                    $this->hardwareRequestModel->update($pendingRequest['id'], [
                        'status' => 'assigned',
                        'assigned_item_id' => $id,
                        'admin_id' => session()->get('id'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    $this->hardwareRequestModel->save([
                        'user_id' => $userId,
                        'item_type' => 'accessory',
                        'category' => $item['category'],
                        'requirement_type' => 'fixed',
                        'reason' => 'Status update to Assigned',
                        'status' => 'assigned',
                        'assigned_item_id' => $id,
                        'admin_id' => session()->get('id'),
                        'request_date' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }

        $this->accessoryModel->update($id, $data);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Failed to synchronize hardware status.');
        }

        $msg = ($status === 'Stock') ? 'Accessory returned to stock.' : 'Operational status synchronized successfully.';
        $this->logAction('Accessory Status Updated', "Updated status of accessory ID $id to $status");
        return redirect()->back()->with('success', $msg);
    }

    public function delete($id)
    {
        if ($this->accessoryModel->delete($id)) {
            $this->logAction('Accessory Deleted', "Removed accessory ID $id");
            return redirect()->to('accessories/items')->with('success', 'Accessory removed from directory');
        }
        return redirect()->to('accessories/items')->with('error', 'Failed to remove accessory');
    }

    public function generateQR($id)
    {
        $item = $this->accessoryModel->find($id);
        if (!$item) {
            return $this->response->setStatusCode(404)->setBody("Accessory not found");
        }

        // Use asset_code for QR data, fallback to id
        $qrData = $item['asset_code'] ?? $id;

        $isRaw = $this->request->getGet('raw') === '1';

        try {
            // 1. Generate the Base QR Code
            $writer = new PngWriter();
            $qrObj = new QrCode(
                data: $qrData,
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

            // 5. Draw Asset Code / Model (Centered)
            $modelText = $item['brand'] . " " . $item['model'];
            $modelBox = imagettfbbox(12, 0, $fontBold, $modelText);
            $modelX = ($width - ($modelBox[2] - $modelBox[0])) / 2;
            imagettftext($canvas, 12, 0, $modelX, 390, $black, $fontBold, $modelText);

            $assetText = "Code: " . ($item['asset_code'] ?? 'N/A');
            $assetBox = imagettfbbox(14, 0, $fontBold, $assetText);
            $assetX = ($width - ($assetBox[2] - $assetBox[0])) / 2;
            imagettftext($canvas, 14, 0, $assetX, 420, $blue, $fontBold, $assetText);

            // 6. Draw Credits (Footer - Centered)
            $footerLine1 = "Designed and Developed by";
            $footerBox1 = imagettfbbox(10, 0, $fontBold, $footerLine1);
            $footerX1 = ($width - ($footerBox1[2] - $footerBox1[0])) / 2;

            $footerLine2 = "CA Sri Lanka ICT Division";
            $footerBox2 = imagettfbbox(11, 0, $fontBold, $footerLine2);
            $footerX2 = ($width - ($footerBox2[2] - $footerBox2[0])) / 2;

            imagettftext($canvas, 10, 0, $footerX1, 470, $gray, $fontBold, $footerLine1);
            imagettftext($canvas, 11, 0, $footerX2, 495, $black, $fontBold, $footerLine2);

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

            $response = $this->response->setHeader('Content-Type', 'image/png');

            if ($this->request->getGet('download') === '1') {
                $filename = "QR_ACC_" . str_replace(['/', '\\', ' '], '_', $item['asset_code'] ?? $id) . ".png";
                $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            }

            return $response->setBody($finalImage);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setBody("QR Generation Error: " . $e->getMessage());
        }
    }
}
