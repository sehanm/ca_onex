<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Asset Intelligence Scanner<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-premium mb-4">
    <div class="header-main d-flex align-items-center gap-3">
        <a href="<?= base_url('inventory') ?>" class="btn-icon-lite"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h2 class="title-gradient">Asset Intelligence</h2>
            <p class="subtitle">Real-time identification and assignment control.</p>
        </div>
    </div>
</div>

<div class="scan-container-premium">
    <div class="glass-container mb-4">
        <div class="mode-selector-modern">
            <button id="cameraModeBtn" class="mode-btn-modern active" onclick="switchMode('camera')">
                <i class="fa-solid fa-camera"></i> <span>Optical Lens</span>
            </button>
            <button id="usbModeBtn" class="mode-btn-modern" onclick="switchMode('usb')">
                <i class="fa-solid fa-plug"></i> <span>Hardware Hub</span>
            </button>
        </div>

        <!-- Camera Scanner View -->
        <div id="camera-section" class="scanner-view-modern active">
            <div class="scanner-viewport-wrapper">
                <div id="qr-reader"></div>
                <div class="scanner-overlay">
                    <div class="scan-corner top-left"></div>
                    <div class="scan-corner top-right"></div>
                    <div class="scan-corner bottom-left"></div>
                    <div class="scan-corner bottom-right"></div>
                    <div class="scan-beam"></div>
                </div>
            </div>
            <div class="scan-hint mt-3">
                <p><i class="fa-solid fa-circle-info me-2"></i>Position the asset QR code within the frame for
                    auto-detection.</p>
                <button id="switchCameraBtn" onclick="switchCamera()" class="btn-glass-sm mt-2">
                    <i class="fa-solid fa-camera-rotate me-2"></i> Toggle Lens
                </button>
            </div>
        </div>

        <!-- USB Scanner View -->
        <div id="usb-section" class="scanner-view-modern">
            <div class="usb-hub-box text-center py-5">
                <div class="hub-icon-wrapper mb-4">
                    <i class="fa-solid fa-tower-broadcast hub-signal"></i>
                    <i class="fa-solid fa-barcode hub-main"></i>
                </div>
                <h3 class="hub-title">Unified Hardware Hub Ready</h3>
                <p class="hub-subtitle">Awaiting signal from handheld scanner or manual input...</p>

                <div class="manual-intercept mt-4">
                    <div class="modern-input-group">
                        <i class="fa-solid fa-keyboard input-icon"></i>
                        <input type="text" id="manualCode" placeholder="Enter Asset Serial or Code..."
                            autocomplete="off">
                        <button onclick="processScan(document.getElementById('manualCode').value)"
                            class="hub-go-btn">EXECUTE</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asset Result Card -->
        <div id="asset-found-card" class="asset-profile-card mt-4" style="display:none;">
            <div class="profile-main">
                <div class="profile-icon" id="res-type-icon">
                    <i class="fa-solid fa-laptop"></i>
                </div>
                <div class="profile-info">
                    <h3 id="res-model">Asset Title</h3>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="profile-badge" id="res-badge">In Store</span>
                        <code class="profile-sub" id="res-code">CODE-000</code>
                    </div>
                </div>
            </div>

            <div class="profile-details-grid mt-4">
                <div class="p-detail">
                    <label>Identity / Serial</label>
                    <span id="res-serial">SN-0000000</span>
                </div>
                <div class="p-detail" id="res-assigned-container">
                    <label>Current Responsibility</label>
                    <span id="res-assigned">Corporate Warehouse</span>
                </div>
            </div>

            <div class="assignment-console mt-4 px-4 py-4 rounded-4"
                style="background: rgba(99, 102, 241, 0.03); border: 1px solid rgba(99,102,241,0.1);">
                <label class="console-label mb-3 d-block text-uppercase fw-bold"
                    style="font-size: 0.7rem; color: #6366f1; letter-spacing: 1px;">
                    <i class="fa-solid fa-user-shield me-2"></i> Reassign Management
                </label>
                <div class="row g-3">
                    <div class="col-md-9">
                        <select id="userSelect" class="form-select console-select">
                            <option value="">-- Choose Target Personnel --</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['user_id'] ?>"><?= esc($user['full_name']) ?> (ID:
                                    <?= $user['user_id'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button id="assignBtn" class="btn btn-primary w-100 py-2 fw-bold shadow-sm"
                            onclick="confirmAssignment()">
                            ASSIGN
                        </button>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 pt-2">
                <button class="btn-premium-outline btn-sm" onclick="resetScanner()">
                    <i class="fa-solid fa-rotate-left me-2"></i> Initialize New Scan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden input for USB/Handheld Scanner -->
<input type="text" id="usbHiddenInput" style="position: absolute; opacity: 0; pointer-events: none;">

<style>
    .scan-container-premium {
        max-width: 700px;
        margin: 0 auto;
        animation: fadeIn 0.6s ease-out;
    }

    .glass-container {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 24px;
        padding: 25px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
    }

    /* Mode Selector */
    .mode-selector-modern {
        display: flex;
        gap: 12px;
        background: #f1f5f9;
        padding: 6px;
        border-radius: 16px;
        margin-bottom: 25px;
    }

    .mode-btn-modern {
        flex: 1;
        padding: 12px;
        border: none;
        background: transparent;
        border-radius: 12px;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-size: 0.9rem;
    }

    .mode-btn-modern i { font-size: 1.1rem; }
    .mode-btn-modern.active {
        background: white;
        color: #6366f1;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.15);
        transform: translateY(-1px);
    }

    /* Scanner Viewports */
    .scanner-view-modern {
        display: none;
        overflow: hidden;
    }
    .scanner-view-modern.active { display: block; animation: slideIn 0.4s ease-out; }

    .scanner-viewport-wrapper {
        position: relative;
        border-radius: 18px;
        overflow: hidden;
        aspect-ratio: 1/1;
        background: #000;
        border: 4px solid #fff;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    #qr-reader { border: none !important; width: 100% !important; height: 100% !important; }
    #qr-reader video { border-radius: 15px !important; object-fit: cover !important; }

    /* Scanner Overlay */
    .scanner-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        pointer-events: none;
        z-index: 10;
    }

    .scan-corner {
        position: absolute;
        width: 40px; height: 40px;
        border-color: #6366f1;
        border-style: solid;
        filter: drop-shadow(0 0 5px rgba(99, 102, 241, 0.5));
    }
    .top-left { top: 30px; left: 30px; border-width: 4px 0 0 4px; border-top-left-radius: 12px; }
    .top-right { top: 30px; right: 30px; border-width: 4px 4px 0 0; border-top-right-radius: 12px; }
    .bottom-left { bottom: 30px; left: 30px; border-width: 0 0 4px 4px; border-bottom-left-radius: 12px; }
    .bottom-right { bottom: 30px; right: 30px; border-width: 0 4px 4px 0; border-bottom-right-radius: 12px; }

    .scan-beam {
        position: absolute;
        top: 30px; left: 30px; right: 30px;
        height: 3px;
        background: linear-gradient(90deg, transparent, #6366f1, transparent);
        box-shadow: 0 0 15px #6366f1;
        animation: beamMove 2.5s infinite linear;
    }

    @keyframes beamMove {
        0%, 100% { top: 30px; opacity: 0; }
        10%, 90% { opacity: 1; }
        50% { top: calc(100% - 33px); }
    }

    /* USB Hub Box */
    .hub-icon-wrapper {
        position: relative;
        font-size: 5rem;
        display: inline-flex;
        justify-content: center;
        align-items: center;
    }
    .hub-main { color: #6366f1; }
    .hub-signal {
        position: absolute;
        font-size: 8rem;
        color: #6366f1;
        opacity: 0.1;
        animation: pulseHub 2s infinite ease-out;
    }

    @keyframes pulseHub {
        0% { transform: scale(0.8); opacity: 0.2; }
        100% { transform: scale(1.5); opacity: 0; }
    }

    .hub-title { color: #1e293b; font-weight: 800; margin-bottom: 5px; }
    .hub-subtitle { color: #64748b; font-size: 0.95rem; }

    /* Modern Input Group */
    .modern-input-group {
        position: relative;
        max-width: 450px;
        margin: 0 auto;
        display: flex;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 50px;
        padding: 5px 5px 5px 20px;
        transition: all 0.3s;
        box-shadow: 0 4px 10px rgba(0,0,0,0.02);
    }
    .modern-input-group:focus-within {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        transform: translateY(-2px);
    }
    .input-icon { color: #94a3b8; align-self: center; margin-right: 12px; }
    .modern-input-group input {
        border: none;
        outline: none;
        flex: 1;
        font-weight: 600;
        color: #1e293b;
    }
    .hub-go-btn {
        background: #6366f1;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 50px;
        font-weight: 800;
        font-size: 0.8rem;
        letter-spacing: 1px;
        transition: all 0.2s;
    }
    .hub-go-btn:hover { background: #4f46e5; transform: scale(1.05); }

    /* Asset Profile Card */
    .asset-profile-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        border: 1px solid #e2e8f0;
        animation: popUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .profile-main {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .profile-icon {
        width: 64px; height: 64px;
        background: #f8fafc;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: #6366f1;
        border: 1px solid #e2e8f0;
    }
    .profile-info h3 { margin: 0; font-weight: 800; color: #1e293b; }
    .profile-badge {
        padding: 2px 10px;
        background: #ecfdf5;
        color: #059669;
        font-size: 0.7rem;
        font-weight: 800;
        border-radius: 6px;
        text-transform: uppercase;
    }
    .profile-sub { font-size: 0.8rem; color: #64748b; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; }

    .profile-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .p-detail label { display: block; font-size: 0.75rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; margin-bottom: 4px; }
    .p-detail span { font-weight: 700; color: #1e293b; display: block; font-size: 0.95rem; }

    .console-select {
        border-radius: 12px;
        padding: 10px 15px;
        font-weight: 600;
        color: #1e293b;
        border-color: #e2e8f0;
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideIn { from { transform: translateX(10px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes popUp { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
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
        $('.mode-btn-modern').removeClass('active');
        $(`#${mode}ModeBtn`).addClass('active');
        $('.scanner-view-modern').removeClass('active');
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
            // Updated error message styling to match new theme
            $('#camera-section').append('<div class="alert alert-danger mx-3 mt-3">Camera Access Failed. Try Hardware Hub Mode.</div>');
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

        $.get("<?= base_url('inventory/get-asset-by-code') ?>", { code: code }, function (res) {
            if (res.success) {
                displayAsset(res.asset, res.assigned_to);
                showToast("Asset Profile Loaded", "success");
            } else {
                showToast(res.message || "Asset identification failed", "error");
                // If camera mode, restart camera after a delay
                if (activeMode === 'camera') {
                    setTimeout(() => startCamera(), 2000);
                }
            }
        }).fail(function () {
            showToast("Secure Link Error", "error");
        });
    }

    function displayAsset(asset, assignedTo) {
        currentAssetId = asset.id;

        // Hide scanners
        $('.scanner-view-modern').removeClass('active');

        // Fill data
        $('#res-model').text(asset.model);
        $('#res-code').text(asset.asset_code || 'PENDING');
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
        } else {
            $('#res-assigned').text('Corporate Warehouse');
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
            showToast("Select target personnel", "error");
            return;
        }

        const btn = $('#assignBtn');
        btn.prop('disabled', true).text('EXECUTING...');

        $.post("<?= base_url('inventory/assign-user') ?>", {
            asset_id: currentAssetId,
            user_id: userId
        }, function (res) {
            btn.prop('disabled', false).text('ASSIGN');
            if (res.success) {
                showToast("Identity Linked Successfully", "success");

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