<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Facilitator Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h2>Facilitator Task Queue</h2>
</div>

<div class="card facilitator-card">
    <div class="table-responsive">
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th class="hide-mobile">My Tasks Status</th>
                    <th>Overall Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $req): ?>
                    <tr data-request-id="<?= $req['request_id'] ?>">
                        <td>
                            <div class="cand-info">
                                <span class="cand-name"><?= esc($req['candidate_name']) ?></span>
                                <span class="cand-meta"><?= esc($req['department_name']) ?> |
                                    <?= date('M d, Y', strtotime($req['joining_date'])) ?></span>
                            </div>
                        </td>
                        <td class="hide-mobile">
                            <div class="task-badges">
                                <?php if (in_array('Admin', $roles) && ($req['admin_chair'] || $req['admin_table'] || $req['admin_phone'])): ?>
                                    <div class="task-group">
                                        <span class="badge badge-admin">Admin</span>
                                        <select onchange="updateSectionStatus(<?= $req['request_id'] ?>, 'admin', this.value)"
                                            class="fac-select">
                                            <option value="Pending" <?= $req['admin_status'] == 'Pending' ? 'selected' : '' ?>>
                                                Pending</option>
                                            <option value="Processing" <?= $req['admin_status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                                            <option value="Completed" <?= $req['admin_status'] == 'Completed' ? 'selected' : '' ?>>
                                                Completed</option>
                                        </select>
                                    </div>
                                <?php endif; ?>

                                <?php if (in_array('HR', $roles) && ($req['hr_mobile'] || $req['hr_sim'])): ?>
                                    <div class="task-group">
                                        <span class="badge badge-hr">HR</span>
                                        <select onchange="updateSectionStatus(<?= $req['request_id'] ?>, 'hr', this.value)"
                                            class="fac-select">
                                            <option value="Pending" <?= $req['hr_status'] == 'Pending' ? 'selected' : '' ?>>Pending
                                            </option>
                                            <option value="Processing" <?= $req['hr_status'] == 'Processing' ? 'selected' : '' ?>>
                                                Processing</option>
                                            <option value="Completed" <?= $req['hr_status'] == 'Completed' ? 'selected' : '' ?>>
                                                Completed</option>
                                        </select>
                                    </div>
                                <?php endif; ?>

                                <?php if (
                                    in_array('ICT', $roles) && ($req['ict_desktop_laptop'] !== 'None' || $req['ict_printer'] ||
                                        $req['soft_smms'] || $req['soft_receipt'] || $req['soft_training'] ||
                                        $req['soft_ecole'] || $req['soft_pronto'] || $req['soft_ims'] ||
                                        $req['soft_sap'] || $req['soft_imeet'] || !empty($req['access_copy_user']))
                                ): ?>
                                    <div class="task-group">
                                        <span class="badge badge-ict">ICT</span>
                                        <select onchange="updateSectionStatus(<?= $req['request_id'] ?>, 'ict', this.value)"
                                            class="fac-select">
                                            <option value="Pending" <?= $req['ict_status'] == 'Pending' ? 'selected' : '' ?>>
                                                Pending</option>
                                            <option value="Processing" <?= $req['ict_status'] == 'Processing' ? 'selected' : '' ?>>
                                                Processing</option>
                                            <option value="Completed" <?= $req['ict_status'] == 'Completed' ? 'selected' : '' ?>>
                                                Completed</option>
                                        </select>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php
                            $statusClass = '';
                            if ($req['status'] === 'Completed')
                                $statusClass = 'badge-success';
                            else if ($req['status'] === 'Processing')
                                $statusClass = 'badge-processing-pulse';
                            ?>
                            <span class="badge badge-role <?= $statusClass ?>"
                                id="status-badge-<?= $req['request_id'] ?>"><?= esc($req['status']) ?></span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <button class="btn-icon" onclick="viewDetails(<?= htmlspecialchars(json_encode($req)) ?>)"
                                    title="View Required Facilities">
                                    <i class="fa-solid fa-rectangle-list"></i>
                                </button>
                                <?php if (in_array('ICT', $roles) && $req['ict_desktop_laptop'] !== 'None'): ?>
                                    <button class="btn-icon" onclick="openScanner(<?= $req['request_id'] ?>)"
                                        title="Assign Asset (QR Scan)"
                                        style="background: rgba(124, 58, 237, 0.1); color: #7c3aed;">
                                        <i class="fa-solid fa-qrcode"></i>
                                    </button>

                                    <?php if (!empty($req['ict_asset_id'])): ?>
                                        <button class="btn-icon"
                                            onclick="viewAssignedAsset(<?= htmlspecialchars(json_encode($req)) ?>)"
                                            title="View Assigned Asset"
                                            style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                            <i class="fa-solid fa-laptop"></i>
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<!-- Modal for Facility Details -->
<div id="facilityModal" class="modal"
    style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; padding: 20px; background-color: rgba(0,0,0,0.4); overflow-y: auto;">
    <div class="modal-content"
        style="background-color: #fefefe; margin: 20px auto; padding: 30px; border-radius: 12px; border: 1px solid #888; width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
        <h3 id="modalTitle" style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px;">Required
            Facilities</h3>

        <div id="modalBody" class="modal-body-content">
            <!-- Dynamic Content -->
        </div>

        <div style="text-align: right; margin-top: 25px;">
            <button onclick="closeModal()" class="btn-secondary">Close</button>
        </div>
    </div>
</div>

<!-- Modal for QR Scanner -->
<div id="qrModal" class="modal"
    style="display:none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; padding: 20px; background-color: rgba(0,0,0,0.6); overflow-y: auto;">
    <div class="modal-content"
        style="background-color: #fefefe; margin: 5% auto; padding: 25px; border-radius: 16px; width: 95%; max-width: 500px; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
        <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin:0;">Scan Asset QR</h3>
            <button onclick="closeScanner()"
                style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#64748b;">&times;</button>
        </div>

        <div id="qr-reader"
            style="width: 100%; border-radius: 12px; overflow: hidden; background: #fafafa; border: 1px solid #e2e8f0;">
        </div>

        <div id="asset-scanned-details"
            style="display:none; margin-top: 20px; background: #f8fafc; padding: 15px; border-radius: 10px; border: 1px solid #7c3aed;">
            <h4 style="margin-top:0; color: #1e293b;"><i class="fa-solid fa-laptop"></i> Asset Details</h4>
            <div id="scanned-info-content" style="font-size: 0.95rem; line-height: 1.6;"></div>
            <div style="margin-top: 20px; text-align: right;">
                <button id="confirmAssignBtn" class="btn-primary"
                    style="width:100%; background: #7c3aed; border-color: #7c3aed;">Confirm & Assign Asset</button>
            </div>
        </div>

        <div id="scan-feedback" style="margin-top: 15px; text-align: center; color: #64748b; font-size: 0.9rem;">
            Place the asset QR code in front of the camera
        </div>
    </div>
</div>

<!-- Modal for Assigned Asset Details -->
<div id="assetDetailModal" class="modal"
    style="display:none; position: fixed; z-index: 1002; left: 0; top: 0; width: 100%; height: 100%; padding: 20px; background-color: rgba(0,0,0,0.5); overflow-y: auto;">
    <div class="modal-content"
        style="background-color: #fefefe; margin: 10% auto; padding: 25px; border-radius: 16px; width: 95%; max-width: 450px; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
        <div
            style="display:flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
            <h3 style="margin:0; color: #1e293b;">Assigned Asset</h3>
            <button onclick="closeAssetModal()"
                style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#64748b;">&times;</button>
        </div>

        <div id="asset-details-content"
            style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
            <!-- Content will be injected by JS -->
        </div>

        <div style="text-align: right; margin-top: 25px;">
            <button onclick="closeAssetModal()" class="btn-secondary">Close</button>
        </div>
    </div>
</div>

<style>
    .cand-info {
        display: flex;
        flex-direction: column;
    }

    .cand-name {
        font-weight: 600;
        color: #1e293b;
    }

    .cand-meta {
        font-size: 0.8rem;
        color: #64748b;
    }

    .task-badges {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .task-group {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        padding: 5px 10px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .badge-admin {
        background: #0891b2;
        color: #fff;
    }

    .badge-hr {
        background: #db2777;
        color: #fff;
    }

    .badge-ict {
        background: #7c3aed;
        color: #fff;
    }

    .fac-select {
        padding: 4px 8px;
        border-radius: 4px;
        border: 1px solid #cbd5e1;
        font-size: 0.85rem;
        cursor: pointer;
    }

    .fac-select:focus {
        border-color: var(--primary-color);
        outline: none;
    }

    .modal-body-content h5 {
        color: #334155;
        margin: 20px 0 10px 0;
        border-left: 3px solid var(--primary-color);
        padding-left: 10px;
    }

    .fac-list {
        list-style: none;
        padding: 0;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .fac-list li {
        background: #f1f5f9;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .fac-list li i {
        color: #10b981;
    }

    @media (max-width: 600px) {
        .fac-list {
            grid-template-columns: 1fr;
        }

        .modal-content {
            padding: 20px !important;
        }

        .monitor-input-grid {
            display: block !important;
        }
    }

    /* Pulse animation for Processing status */
    .badge-processing-pulse {
        background-color: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
        animation: pulse-blue 2s infinite;
    }

    @keyframes pulse-blue {
        0% {
            box-shadow: 0 0 0 0 rgba(3, 105, 161, 0.4);
        }

        70% {
            box-shadow: 0 0 0 6px rgba(3, 105, 161, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(3, 105, 161, 0);
        }
    }
</style>

<script>
    function updateSectionStatus(requestId, section, status) {
        $.post("<?= base_url('onboarding/update-section-status') ?>", {
            request_id: requestId,
            section: section,
            status: status
        }, function (res) {
            if (res.success) {
                showToast("Status updated successfully", "success");
                if (res.new_status) {
                    const badge = $(`#status-badge-${requestId}`);
                    badge.text(res.new_status);

                    // Update classes
                    badge.removeClass('badge-processing-pulse badge-success');
                    if (res.new_status === 'Completed') badge.addClass('badge-success');
                    else if (res.new_status === 'Processing') badge.addClass('badge-processing-pulse');
                }
            } else {
                showToast("Failed to update status", "error");
            }
        });
    }

    function viewDetails(req) {
        const roles = <?= json_encode($roles) ?>;
        let html = `
            <div class="modal-info-header">
                <p><strong>Candidate:</strong> ${req.candidate_name}</p>
                <p><strong>Joining Date:</strong> ${req.joining_date}</p>
            </div>
            <hr style="margin: 15px 0; border: none; border-top: 1px dashed #ddd;">
        `;

        let itemsFound = false;

        // Admin Section
        if (roles.includes('Admin')) {
            let adminHtml = '';
            if (req.admin_chair == 1) adminHtml += '<li><i class="fa-solid fa-chair"></i> Chair</li>';
            if (req.admin_table == 1) adminHtml += '<li><i class="fa-solid fa-table"></i> Table</li>';
            if (req.admin_phone == 1) adminHtml += '<li><i class="fa-solid fa-phone"></i> Land Phone</li>';

            if (adminHtml !== '') {
                html += '<h5>Administration & Events</h5><ul class="fac-list">' + adminHtml + '</ul>';
                itemsFound = true;
            }
        }

        // HR Section
        if (roles.includes('HR')) {
            let hrHtml = '';
            if (req.hr_mobile == 1) hrHtml += '<li><i class="fa-solid fa-mobile-screen"></i> Mobile Phone</li>';
            if (req.hr_sim == 1) hrHtml += '<li><i class="fa-solid fa-sim-card"></i> SIM Card</li>';

            if (hrHtml !== '') {
                html += '<h5>HR Facilities</h5><ul class="fac-list">' + hrHtml + '</ul>';
                itemsFound = true;
            }
        }

        // ICT Section
        if (roles.includes('ICT')) {
            let ictHtml = '';
            // Hardware
            if (req.ict_desktop_laptop !== 'None') ictHtml += `<li><i class="fa-solid fa-laptop"></i> ${req.ict_desktop_laptop}</li>`;
            if (req.ict_printer == 1) ictHtml += '<li><i class="fa-solid fa-print"></i> Printer Access</li>';

            // Software
            if (req.soft_smms == 1) ictHtml += '<li><i class="fa-solid fa-code-branch"></i> SMMS</li>';
            if (req.soft_receipt == 1) ictHtml += '<li><i class="fa-solid fa-file-invoice"></i> Receipt Module</li>';
            if (req.soft_training == 1) ictHtml += '<li><i class="fa-solid fa-graduation-cap"></i> Training Module</li>';
            if (req.soft_ecole == 1) ictHtml += '<li><i class="fa-solid fa-school"></i> Ecole</li>';
            if (req.soft_pronto == 1) ictHtml += `<li><i class="fa-solid fa-database"></i> Pronto (Prev User: ${req.get_pronto_previous_user || 'N/A'})</li>`;
            if (req.soft_ims == 1) ictHtml += '<li><i class="fa-solid fa-warehouse"></i> IMS</li>';
            if (req.soft_sap == 1) ictHtml += '<li><i class="fa-solid fa-briefcase"></i> SAP Business One</li>';
            if (req.soft_imeet == 1) ictHtml += '<li><i class="fa-solid fa-handshake"></i> Imeet-Venue Booking</li>';

            if (ictHtml !== '') {
                html += '<h5>ICT Infrastructure & Software</h5><ul class="fac-list">' + ictHtml + '</ul>';
                itemsFound = true;
            }

            if (req.access_copy_user) {
                html += `<p style="margin-top:15px; background: #fffbeb; padding: 10px; border-radius: 8px; border: 1px solid #fde68a; font-size: 0.85rem;">
                            <strong>Note:</strong> Mirror permissions from <u>${req.access_copy_user}</u>
                         </p>`;
                itemsFound = true;
            }
        }

        if (!itemsFound) {
            html += '<p style="text-align:center; color:#94a3b8; padding:20px;">No facilities requested for your department.</p>';
        }

        document.getElementById('modalBody').innerHTML = html;
        document.getElementById('facilityModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('facilityModal').style.display = 'none';
    }

    window.onclick = function (event) {
        if (event.target == document.getElementById('facilityModal')) {
            closeModal();
        }
        if (event.target == document.getElementById('qrModal')) {
            closeScanner();
        }
        if (event.target == document.getElementById('assetDetailModal')) {
            closeAssetModal();
        }
    }

    function viewAssignedAsset(req) {
        let html = `
            <div style="display:flex; align-items:center; gap:15px; margin-bottom:15px;">
                <div style="width:50px; height:50px; background:#f0fdf4; color:#10b981; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.5rem;">
                    <i class="fa-solid fa-laptop"></i>
                </div>
                <div>
                    <h4 style="margin:0; color:#1e293b;">${req.ict_model || 'Unknown Model'}</h4>
                    <p style="margin:0; font-size:0.85rem; color:#64748b;">Assigned Asset</p>
                </div>
            </div>
            
            <div style="display:grid; gap:12px;">
                <div style="display:flex; justify-content:space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom:8px;">
                    <span style="color:#64748b;">Serial Number:</span>
                    <strong style="color:#1e293b;">${req.ict_serial_number || 'N/A'}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom:8px;">
                    <span style="color:#64748b;">Asset Code:</span>
                    <strong style="color:#1e293b;">${req.ict_asset_code || 'N/A'}</strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:#64748b;">ID:</span>
                    <strong style="color:#1e293b;">#${req.ict_asset_id}</strong>
                </div>
            </div>
            
            <div style="margin-top:20px; padding:12px; background:#f1f5f9; border-radius:8px; font-size:0.85rem; color:#475569;">
                <i class="fa-solid fa-user" style="margin-right:8px;"></i> Assigned to <strong>${req.candidate_name}</strong>
            </div>
        `;

        document.getElementById('asset-details-content').innerHTML = html;
        document.getElementById('assetDetailModal').style.display = 'block';
    }

    function closeAssetModal() {
        document.getElementById('assetDetailModal').style.display = 'none';
    }

    // QR Scanner Logic
    let html5QrCode;
    let currentRequestId;

    async function openScanner(requestId) {
        currentRequestId = requestId;
        document.getElementById('qrModal').style.display = 'block';
        document.getElementById('asset-scanned-details').style.display = 'none';
        document.getElementById('scan-feedback').innerText = "Initializing camera...";

        if (html5QrCode) {
            await html5QrCode.clear();
        }

        html5QrCode = new Html5Qrcode("qr-reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        try {
            await html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess);
            document.getElementById('scan-feedback').innerText = "Scanning... Place QR code in frame";
        } catch (err) {
            console.error(err);
            document.getElementById('scan-feedback').innerText = "Error: Camera access denied or not found.";
            showToast("Camera access denied or not found", "error");
        }
    }

    async function onScanSuccess(decodedText, decodedResult) {
        console.log(`Scan result: ${decodedText}`);
        document.getElementById('scan-feedback').innerText = "Searching for asset...";

        // Stop scanning once we have a result
        try {
            await html5QrCode.stop();
        } catch (e) { console.warn(e); }

        // Fetch asset from backend
        let code = decodedText;
        if (decodedText.includes('view/')) {
            const parts = decodedText.split('/');
            code = parts[parts.length - 1]; // Get the ID or SKU part
        }

        $.get("<?= base_url('onboarding/get-asset-by-code') ?>", { code: code }, function (res) {
            if (res.success) {
                const asset = res.asset;
                document.getElementById('scanned-info-content').innerHTML = `
                    <p><strong>Model:</strong> ${asset.model}</p>
                    <p><strong>Serial:</strong> ${asset.serial_number}</p>
                    <p><strong>Asset Code:</strong> ${asset.asset_code}</p>
                    <p><strong>Status:</strong> <span class="badge ${asset.status === 'Available' ? 'badge-success' : 'badge-warning'}">${asset.status}</span></p>
                `;
                document.getElementById('asset-scanned-details').style.display = 'block';
                document.getElementById('confirmAssignBtn').onclick = () => confirmAssign(asset.id);
                document.getElementById('scan-feedback').innerText = "Asset found!";
                showToast("Asset information fetched", "success");
            } else {
                showToast(res.message || "Asset not found in system", "error");
                document.getElementById('scan-feedback').innerText = "Asset not found. Please try another QR.";
                // Restart scanning after a short delay
                setTimeout(() => {
                    openScanner(currentRequestId);
                }, 2000);
            }
        });
    }

    function confirmAssign(assetId) {
        const btn = document.getElementById('confirmAssignBtn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = "Assigning...";

        $.post("<?= base_url('onboarding/assign-asset') ?>", {
            request_id: currentRequestId,
            asset_id: assetId
        }, function (res) {
            btn.disabled = false;
            btn.innerText = originalText;

            if (res.success) {
                showToast("Asset updated successfully!", "success");
                closeScanner();
                // Refresh to update the "View Assigned Asset" button and details immediately
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                showToast(res.message, "error");
            }
        });
    }

    function closeScanner() {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().then(() => {
                document.getElementById('qrModal').style.display = 'none';
            }).catch(() => {
                document.getElementById('qrModal').style.display = 'none';
            });
        } else {
            document.getElementById('qrModal').style.display = 'none';
        }
    }
</script>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/html5-qrcode"></script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>