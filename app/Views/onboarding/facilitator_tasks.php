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
    }
</script>
<?= $this->endSection() ?>