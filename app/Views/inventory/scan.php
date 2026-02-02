<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Asset Scanner & Assignment<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div style="display:flex; align-items:center; gap:15px;">
        <a href="<?= base_url('inventory') ?>" class="btn-icon-lite"><i class="fa-solid fa-arrow-left"></i></a>
        <h2>Asset Scanner</h2>
    </div>
</div>

<div class="scan-container">
    <div class="mode-selector">
        <button id="cameraModeBtn" class="mode-btn active" onclick="switchMode('camera')">
            <i class="fa-solid fa-camera"></i> Camera Scanner
        </button>
        <button id="usbModeBtn" class="mode-btn" onclick="switchMode('usb')">
            <i class="fa-solid fa-barcode"></i> USB/Handheld Scanner
        </button>
    </div>

    <!-- Camera Scanner View -->
    <div id="camera-section" class="scanner-view active">
        <div id="qr-reader"></div>
        <div class="scan-feedback" id="camera-feedback">
            <p><i class="fa-solid fa-expand"></i> Align QR code within the frame</p>
            <button id="switchCameraBtn" onclick="switchCamera()" class="btn-secondary btn-sm" style="margin-top:10px;">
                <i class="fa-solid fa-camera-rotate"></i> Switch Camera
            </button>
        </div>
    </div>

    <!-- USB Scanner View -->
    <div id="usb-section" class="scanner-view">
        <div class="usb-ready-box">
            <div class="usb-icon-anim">
                <i class="fa-solid fa-barcode"></i>
                <div class="scan-line"></div>
            </div>
            <h3>USB Scanner Ready</h3>
            <p>Please scan the asset barcode now.</p>
            <div class="manual-input-box">
                <span>Or enter manually:</span>
                <div class="input-group">
                    <input type="text" id="manualCode" placeholder="Asset Code or Serial">
                    <button onclick="processScan(document.getElementById('manualCode').value)">Go</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Dynamic Found Asset Result -->
    <div id="asset-found-card" class="asset-card" style="display:none;">
        <div class="asset-card-header">
            <div class="asset-type-icon" id="res-type-icon">
                <i class="fa-solid fa-laptop"></i>
            </div>
            <div class="asset-primary-details">
                <h3 id="res-model">Asset Model</h3>
                <span class="asset-badge" id="res-badge">In Store</span>
            </div>
        </div>

        <div class="asset-details-grid">
            <div class="detail-item">
                <label>Asset Code</label>
                <strong id="res-code">ICT-LP-001</strong>
            </div>
            <div class="detail-item">
                <label>Serial Number</label>
                <strong id="res-serial">SN-123456</strong>
            </div>
            <div class="detail-item" id="res-assigned-container">
                <label>Currently Assigned To</label>
                <strong id="res-assigned">Warehouse</strong>
            </div>
        </div>

        <hr class="card-divider">

        <div class="assignment-section">
            <h4><i class="fa-solid fa-user-plus"></i> Assign to User</h4>
            <div class="assign-form">
                <select id="userSelect" class="user-select">
                    <option value="">-- Select User --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['user_id'] ?>"><?= esc($user['full_name']) ?> (ID: <?= $user['user_id'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <button id="assignBtn" class="btn-primary" onclick="confirmAssignment()">
                    Confirm Assignment
                </button>
            </div>
        </div>

        <div style="margin-top:20px; text-align:center;">
            <button class="btn-secondary btn-sm" onclick="resetScanner()">
                <i class="fa-solid fa-rotate-left"></i> Scan Another
            </button>
        </div>
    </div>
</div>

<!-- Hidden input for USB/Handheld Scanner -->
<input type="text" id="usbHiddenInput" style="position: absolute; opacity: 0; pointer-events: none;">

<style>
    .scan-container {
        max-width: 600px;
        margin: 0 auto;
        padding-bottom: 50px;
    }

    .mode-selector {
        display: flex;
        gap: 10px;
        margin-bottom: 25px;
        background: #f1f5f9;
        padding: 5px;
        border-radius: 12px;
    }

    .mode-btn {
        flex: 1;
        padding: 12px;
        border: none;
        background: transparent;
        border-radius: 8px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .mode-btn.active {
        background: white;
        color: #6366f1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .scanner-view {
        display: none;
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }

    .scanner-view.active {
        display: block;
    }

    #qr-reader {
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        background: #000;
    }

    .scan-feedback {
        text-align: center;
        margin-top: 15px;
        color: #64748b;
        font-size: 0.9rem;
    }

    /* USB Styles */
    .usb-ready-box {
        text-align: center;
        padding: 40px 20px;
    }

    .usb-icon-anim {
        font-size: 4rem;
        color: #6366f1;
        margin-bottom: 20px;
        position: relative;
        display: inline-block;
    }

    .scan-line {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: #ef4444;
        box-shadow: 0 0 10px #ef4444;
        animation: scan-move 2s infinite;
    }

    @keyframes scan-move {
        0% {
            top: 0;
        }

        50% {
            top: 100%;
        }

        100% {
            top: 0;
        }
    }

    .manual-input-box {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
    }

    .manual-input-box span {
        display: block;
        font-size: 0.8rem;
        color: #94a3b8;
        margin-bottom: 10px;
    }

    .input-group {
        display: flex;
        gap: 5px;
        max-width: 300px;
        margin: 0 auto;
    }

    .input-group input {
        flex: 1;
        padding: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        outline: none;
    }

    .input-group button {
        padding: 0 20px;
        background: #6366f1;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }

    /* Asset Card Styles */
    .asset-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 15px 50px rgba(99, 102, 241, 0.1);
        border: 2px solid #6366f1;
        animation: slideUp 0.4s ease-out;
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .asset-card-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 25px;
    }

    .asset-type-icon {
        width: 60px;
        height: 60px;
        background: #f1f5f9;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: #6366f1;
    }

    .asset-primary-details h3 {
        margin: 0;
        font-size: 1.4rem;
        color: #1e293b;
    }

    .asset-badge {
        display: inline-block;
        padding: 4px 12px;
        background: #e0e7ff;
        color: #4338ca;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        margin-top: 5px;
        text-transform: uppercase;
    }

    .asset-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .detail-item label {
        display: block;
        font-size: 0.8rem;
        color: #94a3b8;
        margin-bottom: 4px;
    }

    .detail-item strong {
        color: #1e293b;
        font-size: 1rem;
    }

    .card-divider {
        border: none;
        border-top: 1px dashed #e2e8f0;
        margin: 25px 0;
    }

    .assignment-section h4 {
        margin: 0 0 15px 0;
        color: #475569;
        font-size: 1.1rem;
    }

    .assign-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .user-select {
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        outline: none;
        cursor: pointer;
    }

    .user-select:focus {
        border-color: #6366f1;
    }

    #assignBtn {
        width: 100%;
        padding: 15px;
        font-size: 1rem;
    }
</style>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCode;
    let cameraMode = "environment";
    let activeMode = 'camera';
    let currentAssetId = null;
    let scanTimer;

    $(document).ready(function () {
        startCamera();

        // Initial focus on search bar and search monitoring
        autoFocusSearch();

        // Keep focus on the search bar
        $(document).on('mousedown', function (e) {
            if (!$(e.target).is('button, select, option, a, input')) {
                setTimeout(autoFocusSearch, 100);
            }
        });

        // Auto-process manual input (for scanners)
        $('#manualCode').on('input', function () {
            clearTimeout(scanTimer);
            const val = $(this).val().trim();
            if (val.length >= 3) {
                scanTimer = setTimeout(() => {
                    if ($('#manualCode').is(':focus') && $('#manualCode').val().trim() === val) {
                        processScan(val);
                        $('#manualCode').val('');
                    }
                }, 500);
            }
        });

        // Enter key support
        $('#manualCode').on('keypress', function (e) {
            if (e.which === 13) {
                clearTimeout(scanTimer);
                const val = $(this).val().trim();
                if (val) {
                    processScan(val);
                    $(this).val('');
                }
            }
        });

        // Background listener (hidden input fail-safe)
        $(document).on('keydown', function (e) {
            if (activeMode === 'usb' && !$('#asset-found-card').is(':visible')) {
                const hiddenInput = $('#usbHiddenInput');
                if (document.activeElement.id !== 'manualCode') {
                    hiddenInput.focus();
                }

                if (e.key === 'Enter') {
                    const code = hiddenInput.val().trim();
                    if (code) {
                        processScan(code);
                        hiddenInput.val('');
                    }
                }
            }
        });
    });

    function autoFocusSearch() {
        if (!$('#asset-found-card').is(':visible')) {
            $('#manualCode').focus();
        }
    }

    function switchMode(mode) {
        activeMode = mode;
        $('.mode-btn').removeClass('active');
        $(`#${mode}ModeBtn`).addClass('active');
        $('.scanner-view').removeClass('active');
        $(`#${mode}-section`).addClass('active');

        $('#asset-found-card').hide();

        if (mode === 'camera') {
            startCamera();
        } else {
            if (html5QrCode) {
                html5QrCode.stop().catch(e => console.error(e));
            }
        }
        setTimeout(autoFocusSearch, 200);
    }

    async function startCamera() {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("qr-reader");
        }

        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        try {
            await html5QrCode.start({ facingMode: cameraMode }, config, (decodedText) => {
                processScan(decodedText);
            });
        } catch (err) {
            console.error(err);
            $('#camera-feedback').html('<p style="color:#ef4444;"><i class="fa-solid fa-circle-exclamation"></i> Camera Access Failed. Try USB Mode.</p>');
        }
    }

    async function switchCamera() {
        if (html5QrCode && html5QrCode.isScanning) {
            await html5QrCode.stop();
            cameraMode = (cameraMode === "environment") ? "user" : "environment";
            startCamera();
        }
    }

    function processScan(scannedText) {
        if (!scannedText) return;

        // Handle URL if scanned via QR
        let code = scannedText.trim();
        if (code.includes('inventory/view/')) {
            const parts = code.split('/');
            code = parts[parts.length - 1];
        }

        console.log("Processing Scan:", code);

        // Stop camera if scanning
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().catch(e => console.error(e));
        }

        showLoading();

        $.get("<?= base_url('inventory/get-asset-by-code') ?>", { code: code }, function (res) {
            hideLoading();
            if (res.success) {
                displayAsset(res.asset, res.assigned_to);
                showToast("Asset Found!", "success");
            } else {
                showToast(res.message || "Asset not found", "error");
                // If camera mode, restart camera after a delay
                if (activeMode === 'camera') {
                    setTimeout(() => startCamera(), 2000);
                }
            }
        }).fail(function () {
            hideLoading();
            showToast("Network Error", "error");
        });
    }

    function displayAsset(asset, assignedTo) {
        currentAssetId = asset.id;

        // Hide scanners
        $('.scanner-view').removeClass('active');

        // Fill data
        $('#res-model').text(asset.model);
        $('#res-code').text(asset.asset_code || 'N/A');
        $('#res-serial').text(asset.serial_number);
        $('#res-badge').text(asset.status);

        // Icon mapping
        const typeIcons = {
            'Laptop': 'fa-laptop',
            'Desktop': 'fa-computer',
            'Monitor': 'fa-tv',
            'Printer': 'fa-print',
            'Accessory': 'fa-mouse'
        };
        const iconClass = typeIcons[asset.type] || 'fa-box-open';
        $('#res-type-icon i').attr('class', 'fa-solid ' + iconClass);

        // Assigned info
        if (assignedTo) {
            $('#res-assigned').text(assignedTo);
            $('#res-assigned-container').show();
        } else {
            $('#res-assigned').text('Warehouse / Not Assigned');
            $('#res-assigned-container').show();
        }

        // Pre-select user if already assigned
        if (asset.assigned_user_id) {
            $('#userSelect').val(asset.assigned_user_id);
        } else {
            $('#userSelect').val('');
        }

        $('#asset-found-card').fadeIn();
    }

    function confirmAssignment() {
        const userId = $('#userSelect').val();
        if (!userId) {
            showToast("Please select a user", "warning");
            return;
        }

        const btn = $('#assignBtn');
        btn.prop('disabled', true).text('Processing...');

        $.post("<?= base_url('inventory/assign-user') ?>", {
            asset_id: currentAssetId,
            user_id: userId
        }, function (res) {
            btn.prop('disabled', false).text('Confirm Assignment');
            if (res.success) {
                showToast(res.message, "success");

                // Update the UI
                const userName = $('#userSelect option:selected').text().split(' (ID:')[0];
                $('#res-assigned').text(userName);
                $('#res-badge').text('Assigned');
            } else {
                showToast(res.message, "error");
            }
        });
    }

    function resetScanner() {
        $('#asset-found-card').hide();
        switchMode(activeMode);
    }

    function showLoading() {
        // Implementation for loading if needed, but showToast usually enough
    }

    function hideLoading() {
    }
</script>
<?= $this->endSection() ?>