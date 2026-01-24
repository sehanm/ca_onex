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
                    <th>My Tasks (<?= implode(', ', $roles) ?>)</th>
                    <th>Request Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $req): ?>
                <tr>
                    <td>
                        <div class="cand-info">
                            <span class="cand-name"><?= esc($req['candidate_name']) ?></span>
                            <span class="cand-meta"><?= esc($req['department_name']) ?> | <?= date('M d, Y', strtotime($req['joining_date'])) ?></span>
                        </div>
                    </td>
                    <td>
                        <div class="task-badges">
                            <?php if (in_array('Admin', $roles)): ?>
                                <div class="task-group">
                                    <span class="badge badge-admin">Admin</span>
                                    <select onchange="updateSectionStatus(<?= $req['request_id'] ?>, 'admin', this.value)" class="fac-select">
                                        <option value="Pending" <?= $req['admin_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Processing" <?= $req['admin_status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                                        <option value="Completed" <?= $req['admin_status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                    </select>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (in_array('HR', $roles)): ?>
                                <div class="task-group">
                                    <span class="badge badge-hr">HR</span>
                                    <select onchange="updateSectionStatus(<?= $req['request_id'] ?>, 'hr', this.value)" class="fac-select">
                                        <option value="Pending" <?= $req['hr_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Processing" <?= $req['hr_status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                                        <option value="Completed" <?= $req['hr_status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <?php if (in_array('ICT', $roles)): ?>
                                <div class="task-group">
                                    <span class="badge badge-ict">ICT</span>
                                    <select onchange="updateSectionStatus(<?= $req['request_id'] ?>, 'ict', this.value)" class="fac-select">
                                        <option value="Pending" <?= $req['ict_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Processing" <?= $req['ict_status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                                        <option value="Completed" <?= $req['ict_status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                    </select>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-role"><?= esc($req['status']) ?></span>
                    </td>
                    <td>
                        <button class="btn-icon" onclick="viewDetails(<?= htmlspecialchars(json_encode($req)) ?>)" title="View Required Facilities">
                            <i class="fa-solid fa-rectangle-list"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Facility Details -->
<div id="facilityModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; padding-top: 100px; background-color: rgba(0,0,0,0.4);">
    <div class="modal-content" style="background-color: #fefefe; margin: auto; padding: 30px; border-radius: 12px; border: 1px solid #888; width: 600px; max-height: 80vh; overflow-y: auto; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
        <h3 id="modalTitle" style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px;">Required Facilities</h3>
        
        <div id="modalBody" class="modal-body-content">
            <!-- Dynamic Content -->
        </div>
        
        <div style="text-align: right; margin-top: 25px;">
            <button onclick="closeModal()" class="btn-secondary">Close</button>
        </div>
    </div>
</div>

<style>
    .cand-info { display: flex; flex-direction: column; }
    .cand-name { font-weight: 600; color: #1e293b; }
    .cand-meta { font-size: 0.8rem; color: #64748b; }
    
    .task-badges { display: flex; gap: 15px; flex-wrap: wrap; }
    .task-group { display: flex; align-items: center; gap: 8px; background: #f8fafc; padding: 5px 10px; border-radius: 8px; border: 1px solid #e2e8f0; }
    
    .badge-admin { background: #0891b2; color: #fff; }
    .badge-hr { background: #db2777; color: #fff; }
    .badge-ict { background: #7c3aed; color: #fff; }
    
    .fac-select { padding: 4px 8px; border-radius: 4px; border: 1px solid #cbd5e1; font-size: 0.85rem; cursor: pointer; }
    .fac-select:focus { border-color: var(--primary-color); outline: none; }

    .modal-body-content h5 { color: #334155; margin: 20px 0 10px 0; border-left: 3px solid var(--primary-color); padding-left: 10px; }
    .fac-list { list-style: none; padding: 0; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .fac-list li { background: #f1f5f9; padding: 8px 12px; border-radius: 6px; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; }
    .fac-list li i { color: #10b981; }
</style>

<script>
    function updateSectionStatus(requestId, section, status) {
        $.post("<?= base_url('onboarding/update-section-status') ?>", {
            request_id: requestId,
            section: section,
            status: status
        }, function(res) {
            if(res.success) {
                showToast("Status updated successfully", "success");
            } else {
                showToast("Failed to update status", "error");
            }
        });
    }

    function viewDetails(req) {
        const roles = <?= json_encode($roles) ?>;
        let html = `
            <p><strong>Candidate:</strong> ${req.candidate_name}</p>
            <p><strong>Joining Date:</strong> ${req.joining_date}</p>
            <hr style="margin: 20px 0; border: none; border-top: 1px dashed #ddd;">
        `;

        if (roles.includes('Admin')) {
            html += '<h5>Administration & Events</h5><ul class="fac-list">';
            if(req.admin_chair) html += '<li>Chair</li>';
            if(req.admin_table) html += '<li>Table</li>';
            if(req.admin_phone) html += '<li>Land Phone</li>';
            html += '</ul>';
        }

        if (roles.includes('HR')) {
            html += '<h5>HR Facilities</h5><ul class="fac-list">';
            if(req.hr_mobile) html += '<li>Mobile Phone</li>';
            if(req.hr_sim) html += '<li>SIM Card</li>';
            html += '</ul>';
        }

        if (roles.includes('ICT')) {
            html += '<h5>ICT Hardware</h5><ul class="fac-list">';
            if(req.ict_desktop_laptop !== 'None') html += `<li>${req.ict_desktop_laptop}</li>`;
            if(req.ict_printer) html += '<li>Printer Access</li>';
            html += '</ul>';

            html += '<h5>Software Access</h5><ul class="fac-list">';
            if(req.soft_smms) html += '<li>SMMS</li>';
            if(req.soft_receipt) html += '<li>Receipt Module</li>';
            if(req.soft_training) html += '<li>Training Module</li>';
            if(req.soft_ecole) html += '<li>Ecole</li>';
            if(req.soft_pronto) html += `<li>Pronto (Prev User: ${req.pronto_previous_user || 'N/A'})</li>`;
            if(req.soft_ims) html += '<li>IMS</li>';
            if(req.soft_sap) html += '<li>SAP Business One</li>';
            if(req.soft_imeet) html += '<li>Imeet-Venue Booking</li>';
            html += '</ul>';
            
            if(req.access_copy_user) {
                html += `<p style="margin-top:15px; background: #fffbeb; padding: 10px; border-radius: 8px; border: 1px solid #fde68a;">
                            <strong>Note:</strong> Mirror permissions from <u>${req.access_copy_user}</u>
                         </p>`;
            }
        }

        document.getElementById('modalBody').innerHTML = html;
        document.getElementById('facilityModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('facilityModal').style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('facilityModal')) {
            closeModal();
        }
    }
</script>
<?= $this->endSection() ?>
