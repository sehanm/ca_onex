<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Facilitator Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-premium">
    <div class="header-main">
        <h2 class="title-gradient">Facilitator Task Queue</h2>
        <p class="subtitle">Real-time management of onboarding facilities and assets.</p>
    </div>
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
                                    <button class="btn-icon"
                                        onclick="openIctTasksModal(<?= htmlspecialchars(json_encode($req)) ?>)"
                                        title="Fill/Update ICT Tasks"
                                        style="background: rgba(124, 58, 237, 0.1); color: #7c3aed;">
                                        <i class="fa-solid fa-list-check"></i>
                                    </button>

                                    <button class="btn-icon" onclick="openScanner(<?= $req['request_id'] ?>, 'camera')"
                                        title="Scan with Camera" style="background: rgba(124, 58, 237, 0.1); color: #7c3aed;">
                                        <i class="fa-solid fa-camera"></i>
                                    </button>

                                    <button class="btn-icon" onclick="openScanner(<?= $req['request_id'] ?>, 'usb')"
                                        title="Use USB Scanner" style="background: rgba(30, 64, 175, 0.1); color: #1e40af;">
                                        <i class="fa-solid fa-barcode"></i>
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
            <div style="display:flex; align-items:center; gap:10px;">
                <h3 id="scannerModalTitle" style="margin:0;">Asset Scanner</h3>
            </div>
            <div style="display:flex; gap: 10px;">
                <button id="switchCameraBtn" onclick="switchCamera()" class="btn-icon-lite" title="Switch Camera"
                    style="display:none; color: #7c3aed;">
                    <i class="fa-solid fa-camera-rotate"></i>
                </button>
                <button onclick="closeScanner()"
                    style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#64748b;">&times;</button>
            </div>
        </div>


        <!-- Hidden input for handheld scanners -->
        <input type="text" id="handheldScannerInput" style="position: absolute; opacity: 0; pointer-events: none;">

        <div id="camera-section" style="display:none;">
            <div id="qr-reader"
                style="width: 100%; border-radius: 12px; overflow: hidden; background: #fafafa; border: 1px solid #e2e8f0;">
            </div>
            <div id="scan-feedback" style="margin-top: 15px; text-align: center; color: #64748b; font-size: 0.9rem;">
                Place the asset QR code in front of the camera
            </div>
        </div>

        <div id="usb-scanner-ready"
            style="display:none; margin-top: 10px; padding: 25px; border: 2px dashed #1e40af; border-radius: 12px; background: rgba(30, 64, 175, 0.05); text-align:center;">
            <div class="usb-pulse"
                style="display:inline-block; width:12px; height:12px; background:#1e40af; border-radius:50%; margin-right:12px;">
            </div>
            <h4 style="color:#1e40af; margin-bottom:10px;">USB Scanner Active</h4>
            <p style="margin:0; font-size:0.9rem; color:#475569;">Please scan the barcode now</p>

            <div style="margin-top:20px; border-top: 1px solid #e2e8f0; padding-top:20px;">
                <p style="margin-bottom:10px; font-size:0.8rem; color:#64748b;">Or type Asset ID/Serial manually</p>
                <div style="display:flex; gap:5px;">
                    <input type="text" id="manualAssetInput" placeholder="Enter ID..."
                        style="flex:1; padding:10px; border:1px solid #cbd5e1; border-radius:8px;">
                    <button onclick="processScan(document.getElementById('manualAssetInput').value)" class="btn-primary"
                        style="padding:0 20px;">Go</button>
                </div>
            </div>
        </div>

        <div id="asset-scanned-details"
            style="display:none; margin-top: 20px; background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #7c3aed;">
            <h4 style="margin-top:0; color: #1e293b;"><i class="fa-solid fa-laptop"></i> Asset Details</h4>
            <div id="scanned-info-content" style="font-size: 0.95rem; line-height: 1.6;"></div>
            <div style="margin-top: 20px; text-align: right;">
                <button id="confirmAssignBtn" class="btn-primary"
                    style="width:100%; background: #7c3aed; border-color: #7c3aed;">Confirm & Assign Asset</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for ICT Tasks Fill -->
<div id="ictTasksModal" class="modal"
    style="display:none; position: fixed; z-index: 1003; left: 0; top: 0; width: 100%; height: 100%; padding: 20px; background-color: rgba(0,0,0,0.6); overflow-y: auto;">
    <div class="modal-content"
        style="background-color: #fefefe; margin: 2% auto; padding: 30px; border-radius: 20px; width: 100%; max-width: 650px; box-shadow: 0 15px 50px rgba(0,0,0,0.3);">
        <div
            style="display:flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 20px;">
            <h3 style="margin:0; color: #1e293b;"><i class="fa-solid fa-laptop-code"
                    style="color:#7c3aed; margin-right:10px;"></i> ICT Facility Task Checklist</h3>
            <button onclick="closeIctTasksModal()"
                style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#64748b;">&times;</button>
        </div>

        <form id="ictTasksForm">
            <?= csrf_field() ?>
            <input type="hidden" name="request_id" id="ict_task_request_id">

            <div class="modal-section-title">Hardware Setup Checklist</div>
            <div class="checkbox-grid-modal">
                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="ict_os_install" value="1">
                    <span class="pill-btn-premium"><i class="fa-solid fa-window-restore"></i> Install OS</span>
                </label>

                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="ict_admin_pass_change" value="1">
                    <span class="pill-btn-premium"><i class="fa-solid fa-key"></i> Admin Pass</span>
                </label>

                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="ict_domain_add" value="1">
                    <span class="pill-btn-premium"><i class="fa-solid fa-network-wired"></i> Domain</span>
                </label>

                <div class="pill-group-container">
                    <label class="checkbox-pill-premium">
                        <input type="checkbox" name="ict_comp_name_change" id="modal_ict_comp_name_change" value="1">
                        <span class="pill-btn-premium"><i class="fa-solid fa-pen-to-square"></i> Comp Name</span>
                    </label>
                    <div id="modal_comp_name_field" style="display:none; margin-top: 10px;">
                        <input type="text" name="ict_updated_comp_name" class="fac-input"
                            placeholder="Enter updated name...">
                    </div>
                </div>
            </div>

            <div class="modal-section-title">Base Software Checklist</div>
            <div class="checkbox-grid-modal">
                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="soft_eset" value="1">
                    <span class="pill-btn-premium"><i class="fa-solid fa-shield-virus"></i> ESET</span>
                </label>
                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="soft_office365" value="1">
                    <span class="pill-btn-premium"><i class="fa-solid fa-file-word"></i> Office 365</span>
                </label>
                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="soft_chrome" value="1">
                    <span class="pill-btn-premium"><i class="fa-brands fa-chrome"></i> Chrome</span>
                </label>
                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="soft_pdf_reader" value="1">
                    <span class="pill-btn-premium"><i class="fa-solid fa-file-pdf"></i> PDF Reader</span>
                </label>
                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="soft_vlc" value="1">
                    <span class="pill-btn-premium"><i class="fa-solid fa-play"></i> VLC</span>
                </label>
                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="soft_winrar" value="1">
                    <span class="pill-btn-premium"><i class="fa-solid fa-file-zipper"></i> Winrar</span>
                </label>
                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="soft_zoom" value="1">
                    <span class="pill-btn-premium"><i class="fa-solid fa-video"></i> Zoom</span>
                </label>
            </div>

            <div class="modal-section-title">System Modules Access</div>
            <div class="checkbox-grid-modal">
                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="soft_modules[]" value="soft_smms">
                    <span class="pill-btn-premium"><i class="fa-solid fa-code-branch"></i> SMMS</span>
                </label>
                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="soft_modules[]" value="soft_receipt">
                    <span class="pill-btn-premium"><i class="fa-solid fa-file-invoice"></i> Receipt Module</span>
                </label>
                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="soft_modules[]" value="soft_training">
                    <span class="pill-btn-premium"><i class="fa-solid fa-graduation-cap"></i> Training Module</span>
                </label>
                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="soft_modules[]" value="soft_ecole">
                    <span class="pill-btn-premium"><i class="fa-solid fa-school"></i> Ecole</span>
                </label>
                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="soft_modules[]" value="soft_sap">
                    <span class="pill-btn-premium"><i class="fa-solid fa-briefcase"></i> SAP B1</span>
                </label>
                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="soft_modules[]" value="soft_pronto">
                    <span class="pill-btn-premium"><i class="fa-solid fa-database"></i> Pronto</span>
                </label>
                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="soft_modules[]" value="soft_ims">
                    <span class="pill-btn-premium"><i class="fa-solid fa-warehouse"></i> IMS</span>
                </label>
                <label class="checkbox-pill-premium">
                    <input type="checkbox" name="soft_modules[]" value="soft_imeet">
                    <span class="pill-btn-premium"><i class="fa-solid fa-handshake"></i> iMeet</span>
                </label>
            </div>

            <div style="margin-top: 30px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeIctTasksModal()" class="btn-secondary"
                    style="padding: 10px 25px;">Cancel</button>
                <button type="submit" class="btn-primary"
                    style="padding: 10px 35px; background: #7c3aed; border-color: #7c3aed;">Save Checklist</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Assigned Asset Details -->
<div id="assetDetailModal" class="modal"
    style="display:none; position: fixed; z-index: 1004; left: 0; top: 0; width: 100%; height: 100%; padding: 20px; background-color: rgba(0,0,0,0.6); overflow-y: auto;">
    <div class="modal-content"
        style="background-color: #fefefe; margin: 10% auto; padding: 30px; border-radius: 20px; width: 100%; max-width: 450px; box-shadow: 0 15px 50px rgba(0,0,0,0.3);">
        <div
            style="display:flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 20px;">
            <h3 style="margin:0; color: #1e293b;">Asset Information</h3>
            <button onclick="closeAssetModal()"
                style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#64748b;">&times;</button>
        </div>
        <div id="asset-details-content">
            <!-- Dynamic Content -->
        </div>
        <div style="text-align: right; margin-top: 25px;">
            <button onclick="closeAssetModal()" class="btn-secondary">Close</button>
        </div>
    </div>
</div>

<style>
    .modal-section-title {
        font-size: 0.85rem;
        font-weight: 800;
        color: #1e293b;
        text-transform: uppercase;
        margin: 25px 0 15px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid #f1f5f9;
        letter-spacing: 1px;
    }

    .fac-input {
        width: 100%;
        padding: 10px 15px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.85rem;
        color: #1e293b;
        transition: all 0.2s;
    }

    .fac-input:focus {
        border-color: #7c3aed;
        outline: none;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    }

    .fac-multi-select {
        width: 100%;
        min-height: 120px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        font-size: 0.95rem;
        color: #1e293b;
        transition: all 0.2s;
    }

    .fac-multi-select:focus {
        border-color: #7c3aed;
        outline: none;
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
    }

    .checkbox-grid-modal {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 5px;
    }

    .checkbox-pill-premium {
        cursor: pointer;
        position: relative;
    }

    .checkbox-pill-premium input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .pill-btn-premium {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.88rem;
        font-weight: 600;
        color: #64748b;
        transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        user-select: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    .pill-btn-premium i {
        color: #94a3b8;
        transition: all 0.2s;
    }

    .checkbox-pill-premium:hover .pill-btn-premium {
        border-color: #7c3aed;
        color: #7c3aed;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.1);
    }

    .checkbox-pill-premium input:checked+.pill-btn-premium {
        background: #7c3aed;
        border-color: #7c3aed;
        color: #fff;
        box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
    }

    .checkbox-pill-premium input:checked+.pill-btn-premium i {
        color: #fff;
    }

    .pill-group-container {
        display: flex;
        flex-direction: column;
        width: 100%;
    }
</style>

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

    .btn-icon-lite {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: transparent;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-icon-lite:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .mode-select-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 15px;
        padding: 30px 20px;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .mode-select-btn i {
        font-size: 2.5rem;
        color: #7c3aed;
    }

    .mode-select-btn span {
        font-weight: 600;
        color: #1e293b;
    }

    .mode-select-btn:hover {
        background: #f1f5f9;
        border-color: #7c3aed;
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(124, 58, 237, 0.1);
    }

    .usb-pulse {
        animation: usb-glow 1.5s infinite alternate;
    }

    @keyframes usb-glow {
        from {
            opacity: 0.4;
            transform: scale(0.8);
        }

        to {
            opacity: 1;
            transform: scale(1.1);
            box-shadow: 0 0 10px rgba(124, 58, 237, 0.5);
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
            // Setup Tasks
            let tasksHtml = '';
            if (req.ict_os_install == 1) tasksHtml += '<li><i class="fa-solid fa-circle-check"></i> OS Installed</li>';
            if (req.ict_admin_pass_change == 1) tasksHtml += '<li><i class="fa-solid fa-circle-check"></i> Admin Pass Changed</li>';
            if (req.ict_domain_add == 1) tasksHtml += '<li><i class="fa-solid fa-circle-check"></i> Added to Domain</li>';
            if (req.ict_comp_name_change == 1) tasksHtml += `<li><i class="fa-solid fa-circle-check"></i> Comp Name: ${req.ict_updated_comp_name || 'Done'}</li>`;

            if (tasksHtml !== '') {
                html += '<h5>ICT Setup Tasks</h5><ul class="fac-list">' + tasksHtml + '</ul>';
            }

            // Hardware
            let hwHtml = '';
            if (req.ict_desktop_laptop !== 'None') hwHtml += `<li><i class="fa-solid fa-laptop"></i> ${req.ict_desktop_laptop}</li>`;
            if (req.ict_printer == 1) hwHtml += '<li><i class="fa-solid fa-print"></i> Printer Access</li>';
            if (hwHtml !== '') html += '<h5>ICT Hardware</h5><ul class="fac-list">' + hwHtml + '</ul>';

            // Software
            let softHtml = '';
            if (req.soft_eset == 1) softHtml += '<li><i class="fa-solid fa-shield-virus"></i> ESET Security</li>';
            if (req.soft_office365 == 1) softHtml += '<li><i class="fa-solid fa-file-word"></i> Office 365</li>';
            if (req.soft_chrome == 1) softHtml += '<li><i class="fa-brands fa-chrome"></i> Google Chrome</li>';
            if (req.soft_pdf_reader == 1) softHtml += '<li><i class="fa-solid fa-file-pdf"></i> PDF Reader</li>';
            if (req.soft_vlc == 1) softHtml += '<li><i class="fa-solid fa-play"></i> VLC</li>';
            if (req.soft_winrar == 1) softHtml += '<li><i class="fa-solid fa-file-zipper"></i> Winrar</li>';
            if (req.soft_zoom == 1) softHtml += '<li><i class="fa-solid fa-video"></i> Zoom</li>';

            // Modules
            if (req.soft_smms == 1) softHtml += '<li><i class="fa-solid fa-code-branch"></i> SMMS</li>';
            if (req.soft_receipt == 1) softHtml += '<li><i class="fa-solid fa-file-invoice"></i> Receipt Module</li>';
            if (req.soft_training == 1) softHtml += '<li><i class="fa-solid fa-graduation-cap"></i> Training Module</li>';
            if (req.soft_ecole == 1) softHtml += '<li><i class="fa-solid fa-school"></i> Ecole</li>';
            if (req.soft_pronto == 1) softHtml += `<li><i class="fa-solid fa-database"></i> Pronto (Prev User: ${req.pronto_previous_user || 'N/A'})</li>`;
            if (req.soft_ims == 1) softHtml += '<li><i class="fa-solid fa-warehouse"></i> IMS</li>';
            if (req.soft_sap == 1) softHtml += '<li><i class="fa-solid fa-briefcase"></i> SAP Business One</li>';
            if (req.soft_imeet == 1) softHtml += '<li><i class="fa-solid fa-handshake"></i> Imeet-Venue Booking</li>';

            if (softHtml !== '') {
                html += '<h5>Software Access</h5><ul class="fac-list">' + softHtml + '</ul>';
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
    let currentFacingMode = "environment";

    async function openScanner(requestId, mode) {
        currentRequestId = requestId;
        document.getElementById('qrModal').style.display = 'block';
        document.getElementById('asset-scanned-details').style.display = 'none';

        const cameraSection = document.getElementById('camera-section');
        const usbSection = document.getElementById('usb-scanner-ready');
        const modalTitle = document.getElementById('scannerModalTitle');

        if (mode === 'camera') {
            cameraSection.style.display = 'block';
            usbSection.style.display = 'none';
            modalTitle.innerText = "Camera Scanner";
            document.getElementById('scan-feedback').innerText = "Initializing camera...";

            if (html5QrCode) {
                try { await html5QrCode.clear(); } catch (e) { }
            }
            html5QrCode = new Html5Qrcode("qr-reader");
            setTimeout(() => startCamera(currentFacingMode), 100);
        } else {
            cameraSection.style.display = 'none';
            usbSection.style.display = 'block';
            modalTitle.innerText = "USB Scanner";
            setTimeout(() => document.getElementById('manualAssetInput').focus(), 100);
        }

        // Focus management logic
        const manualInput = document.getElementById('manualAssetInput');
        const scannerInput = document.getElementById('handheldScannerInput');

        document.getElementById('qrModal').onclick = (e) => {
            if (e.target !== manualInput && e.target !== document.getElementById('confirmAssignBtn')) {
                scannerInput.focus();
            }
        };

        if (mode === 'usb' && !html5QrCode) {
            html5QrCode = new Html5Qrcode("qr-reader");
        }

        document.addEventListener('keydown', handleGlobalKeydown);
    }


    async function startCamera(facingMode) {
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        try {
            await html5QrCode.start({ facingMode: facingMode }, config, onScanSuccess);
            document.getElementById('scan-feedback').innerText = "Scanning... Place code in frame";
            document.getElementById('switchCameraBtn').style.display = 'block';
        } catch (err) {
            console.error(err);
            document.getElementById('scan-feedback').innerText = "Camera not available. Use Handheld Scanner.";
            document.getElementById('switchCameraBtn').style.display = 'none';
        }
    }

    async function switchCamera() {
        if (html5QrCode && html5QrCode.isScanning) {
            await html5QrCode.stop();
            currentFacingMode = currentFacingMode === "environment" ? "user" : "environment";
            startCamera(currentFacingMode);
        }
    }

    function handleGlobalKeydown(e) {
        if (document.getElementById('qrModal').style.display === 'block') {
            const scannerInput = document.getElementById('handheldScannerInput');
            const manualInput = document.getElementById('manualAssetInput');

            // Allow focus on the visible search bar
            if (document.activeElement !== scannerInput && document.activeElement !== manualInput) {
                manualInput.focus();
            }

            if (e.key === 'Enter') {
                const activeEl = document.activeElement;
                if (activeEl === scannerInput || activeEl === manualInput) {
                    const code = activeEl.value.trim();
                    if (code) {
                        processScan(code);
                        activeEl.value = "";
                    }
                }
            }
        }
    }

    async function onScanSuccess(decodedText, decodedResult) {
        console.log(`Scan result: ${decodedText}`);
        processScan(decodedText);
    }

    async function processScan(scannedText) {
        if (!scannedText) return;

        const feedback = document.getElementById('scan-feedback');
        feedback.innerText = "Searching for asset...";
        console.log("Processing code:", scannedText);

        // Stop scanning once we have a result
        if (html5QrCode && html5QrCode.isScanning) {
            try {
                await html5QrCode.stop();
            } catch (e) { console.warn(e); }
        }

        // Fetch asset from backend
        let code = scannedText.trim();
        // Handle full URLs if scanned from QR (e.g., .../view/123)
        if (code.includes('view/')) {
            const parts = code.split('/');
            code = parts[parts.length - 1];
        }

        $.get("<?= base_url('onboarding/get-asset-by-code') ?>", { code: code }, function (res) {
            if (res.success) {
                const asset = res.asset;
                document.getElementById('scanned-info-content').innerHTML = `
                    <div style="padding: 10px; background: #fff; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <p style="margin-bottom: 5px;"><strong>Model:</strong> ${asset.model}</p>
                        <p style="margin-bottom: 5px;"><strong>Serial:</strong> ${asset.serial_number}</p>
                        <p style="margin-bottom: 5px;"><strong>Asset Code:</strong> ${asset.asset_code}</p>
                        <p style="margin: 0;"><strong>Status:</strong> <span class="badge ${asset.status === 'Available' ? 'badge-success' : 'badge-warning'}">${asset.status}</span></p>
                    </div>
                `;
                document.getElementById('asset-scanned-details').style.display = 'block';
                document.getElementById('confirmAssignBtn').onclick = () => confirmAssign(asset.id);

                // Hide scanner views to focus on details
                document.getElementById('camera-section').style.display = 'none';
                document.getElementById('usb-scanner-ready').style.display = 'none';
                document.getElementById('switchCameraBtn').style.display = 'none';
                document.getElementById('scannerModalTitle').innerText = "Asset Found";

                feedback.innerHTML = `<span style="color:#10b981;"><i class="fa-solid fa-circle-check"></i> Asset identified</span>`;
                showToast("Asset information fetched", "success");
            } else {
                feedback.innerHTML = `<span style="color:#ef4444;"><i class="fa-solid fa-circle-xmark"></i> Not found: <strong>${code}</strong></span>`;
                showToast(res.message || "Asset not found in system", "error");

                // Re-focus scanner if in USB mode
                if (document.getElementById('usb-scanner-ready').style.display === 'block') {
                    setTimeout(() => document.getElementById('handheldScannerInput').focus(), 100);
                } else if (document.getElementById('camera-section').style.display === 'block') {
                    feedback.innerHTML += "<br><small>Please try scanning again.</small>";
                }
            }
        }).fail(function () {
            feedback.innerHTML = `<span style="color:#ef4444;"><i class="fa-solid fa-triangle-exclamation"></i> Network error</span>`;
            showToast("Connection failed. Check your network.", "error");
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
        document.removeEventListener('keydown', handleGlobalKeydown);
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

    // ICT Tasks Modal JS
    function openIctTasksModal(req) {
        document.getElementById('ict_task_request_id').value = req.request_id;

        // Reset form
        const form = document.getElementById('ictTasksForm');
        form.reset();

        // Pre-fill
        form.querySelector('[name="ict_os_install"]').checked = (req.ict_os_install == 1);
        form.querySelector('[name="ict_admin_pass_change"]').checked = (req.ict_admin_pass_change == 1);
        form.querySelector('[name="ict_domain_add"]').checked = (req.ict_domain_add == 1);
        form.querySelector('[name="ict_comp_name_change"]').checked = (req.ict_comp_name_change == 1);
        form.querySelector('[name="ict_updated_comp_name"]').value = req.ict_updated_comp_name || '';

        if (req.ict_comp_name_change == 1) document.getElementById('modal_comp_name_field').style.display = 'block';
        else document.getElementById('modal_comp_name_field').style.display = 'none';

        form.querySelector('[name="soft_eset"]').checked = (req.soft_eset == 1);
        form.querySelector('[name="soft_office365"]').checked = (req.soft_office365 == 1);
        form.querySelector('[name="soft_chrome"]').checked = (req.soft_chrome == 1);
        form.querySelector('[name="soft_pdf_reader"]').checked = (req.soft_pdf_reader == 1);
        form.querySelector('[name="soft_vlc"]').checked = (req.soft_vlc == 1);
        form.querySelector('[name="soft_winrar"]').checked = (req.soft_winrar == 1);
        form.querySelector('[name="soft_zoom"]').checked = (req.soft_zoom == 1);

        // Modules checkbox grid
        const moduleCheckboxes = form.querySelectorAll('[name="soft_modules[]"]');
        moduleCheckboxes.forEach(cb => {
            cb.checked = (req[cb.value] == 1);
        });

        document.getElementById('ictTasksModal').style.display = 'block';
    }

    function closeIctTasksModal() {
        document.getElementById('ictTasksModal').style.display = 'none';
    }

    document.getElementById('modal_ict_comp_name_change').addEventListener('change', function () {
        document.getElementById('modal_comp_name_field').style.display = this.checked ? 'block' : 'none';
    });

    document.getElementById('ictTasksForm').onsubmit = function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerText;

        submitBtn.disabled = true;
        submitBtn.innerText = "Saving...";

        $.ajax({
            url: "<?= base_url('onboarding/save-ict-tasks') ?>",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    showToast("ICT Tasks saved successfully", "success");
                    closeIctTasksModal();
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                    showToast(res.message || "Error saving tasks", "error");
                }
            },
            error: function (xhr) {
                submitBtn.disabled = false;
                submitBtn.innerText = originalText;
                showToast("Server error. Please check if your session is active.", "error");
                console.error(xhr);
            }
        });
    };
</script>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/html5-qrcode"></script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>