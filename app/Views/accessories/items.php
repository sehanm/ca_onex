<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Accessories Inventory
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-premium">
    <div class="header-main">
        <h2 class="title-gradient">Accessories Repository</h2>
        <p class="subtitle">ICT Hardware assignment and stock management.</p>
    </div>
    <div class="header-actions">
        <button class="btn-primary" onclick="openAddModal()">
            <i class="fa-solid fa-plus"></i> New Accessory
        </button>
    </div>
</div>

<div class="card shadow-premium mt-4">
    <div class="table-responsive">
        <table class="table datatable datatable-premium">
            <thead>
                <tr>
                    <th>Accessory Profile</th>
                    <th>Serial Identity</th>
                    <th>Category</th>
                    <th>Availability</th>
                    <th>Assignment</th>
                    <th class="text-end">Command</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <div class="item-id-cell">
                                <div class="item-icon-box">
                                    <?php
                                    $icon = 'fa-plug';
                                    if (stripos($item['category'], 'Mouse') !== false)
                                        $icon = 'fa-mouse';
                                    if (stripos($item['category'], 'Keyboard') !== false)
                                        $icon = 'fa-keyboard';
                                    if (stripos($item['category'], 'Headset') !== false)
                                        $icon = 'fa-headset';
                                    if (stripos($item['category'], 'Webcam') !== false)
                                        $icon = 'fa-video';
                                    if (stripos($item['category'], 'UPS') !== false)
                                        $icon = 'fa-battery-full';
                                    if (stripos($item['category'], 'Docking') !== false)
                                        $icon = 'fa-link';
                                    ?>
                                    <i class="fa-solid <?= $icon ?>"></i>
                                </div>
                                <div class="item-primary-info">
                                    <span class="item-model"><?= esc($item['brand']) ?>     <?= esc($item['model']) ?></span>
                                    <span class="item-code"><?= esc($item['asset_code']) ?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <code class="serial-code"><?= esc($item['serial_number'] ?: 'N/A') ?></code>
                        </td>
                        <td>
                            <div class="category-chip">
                                <i class="fa-solid fa-tag"></i>
                                <?= esc($item['category']) ?>
                            </div>
                        </td>
                        <td>
                            <?php
                            $status_class = 'status-stock';
                            if ($item['status'] == 'Assigned')
                                $status_class = 'status-assigned';
                            if ($item['status'] == 'Damaged')
                                $status_class = 'status-damaged';
                            ?>
                            <span class="status-pill <?= $status_class ?>">
                                <span class="dot"></span>
                                <?= esc($item['status']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="user-chip-modern">
                                <?php if ($item['assigned_user_id']): ?>
                                    <div class="avatar-lite"><?= strtoupper(substr($item['assigned_to'], 0, 1)) ?></div>
                                    <div class="assignee-details">
                                        <span class="assignee-name"><?= esc($item['assigned_to']) ?></span>
                                        <form action="<?= base_url('accessories/return') ?>" method="POST"
                                            style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <button type="submit" class="btn-link-action" title="Return to Stock">Return to
                                                Stock</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <button class="btn-assign-mini" onclick='openAssignModal(<?= json_encode($item) ?>)'>
                                        <i class="fa-solid fa-user-plus"></i> Assign User
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="action-flex">
                                <button class="btn-action-view" onclick='openStatusModal(<?= json_encode($item) ?>)'
                                    title="Status History/Update">
                                    <i class="fa-solid fa-rotate-right"></i>
                                </button>
                                <button class="btn-action-trash"
                                    onclick="confirmDeletion('<?= base_url('accessories/delete/' . $item['id']) ?>', 'Remove Accessory?', 'This data will be permanently purged from the hardware ledger.')"
                                    title="Purge Record">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="modal-premium-overlay">
    <div class="modal-premium-content">
        <div class="modal-header">
            <h3>Register New Accessory</h3>
            <button class="close-btn" onclick="closeModal('addModal')">&times;</button>
        </div>
        <form action="<?= base_url('accessories/store') ?>" method="POST">
            <div class="form-grid">
                <div class="form-group-premium">
                    <label>Category *</label>
                    <select name="category" class="form-control" required>
                        <option value="Mouse">Mouse</option>
                        <option value="Keyboard">Keyboard</option>
                        <option value="Headset">Headset</option>
                        <option value="Docking Station">Docking Station</option>
                        <option value="Adapter">Adapter</option>
                        <option value="UPS">UPS</option>
                        <option value="Webcam">Webcam</option>
                        <option value="Cables">Cables</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group-premium">
                    <label>Asset Code *</label>
                    <input type="text" name="asset_code" class="form-control" placeholder="ICT-ACC-XXXX" required>
                </div>
                <div class="form-group-premium">
                    <label>Brand</label>
                    <input type="text" name="brand" class="form-control" placeholder="e.g. Logitech, Dell">
                </div>
                <div class="form-group-premium">
                    <label>Model *</label>
                    <input type="text" name="model" class="form-control" placeholder="e.g. MX Master 3" required>
                </div>
                <div class="form-group-premium">
                    <label>Serial Number</label>
                    <input type="text" name="serial_number" class="form-control" placeholder="Optional S/N">
                </div>
            </div>

            <div class="form-group-premium mt-1">
                <label>Inventory Notes</label>
                <textarea name="notes" class="form-control" rows="2"
                    placeholder="Optional condition details..."></textarea>
            </div>

            <div class="modal-footer mt-4">
                <button type="button" class="btn-secondary" onclick="closeModal('addModal')">Cancel</button>
                <button type="submit" class="btn-primary">Register Accessory</button>
            </div>
        </form>
    </div>
</div>

<!-- Assign Modal -->
<div id="assignModal" class="modal-premium-overlay">
    <div class="modal-premium-content mini">
        <div class="modal-header">
            <h3>Assign Accessory</h3>
            <button class="close-btn" onclick="closeModal('assignModal')">&times;</button>
        </div>
        <form action="<?= base_url('accessories/assign') ?>" method="POST">
            <input type="hidden" name="accessory_id" id="assign_id">
            <p id="assign_item_display" class="mb-3 font-weight-bold"></p>
            <div class="form-group-premium">
                <label>Select Staff Member</label>
                <select name="user_id" class="form-control select2" required>
                    <option value="">Choose User...</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['user_id'] ?>">
                            <?= esc($user['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-footer mt-4">
                <button type="button" class="btn-secondary" onclick="closeModal('assignModal')">Cancel</button>
                <button type="submit" class="btn-primary">Confirm Assignment</button>
            </div>
        </form>
    </div>
</div>

<!-- Status Modal -->
<div id="statusModal" class="modal-premium-overlay">
    <div class="modal-premium-content mini">
        <div class="modal-header">
            <h3>Update Operational Status</h3>
            <button class="close-btn" onclick="closeModal('statusModal')">&times;</button>
        </div>
        <form action="<?= base_url('accessories/update-status') ?>" method="POST">
            <input type="hidden" name="id" id="status_id">
            <div class="form-group-premium">
                <label>Update Status</label>
                <select name="status" id="status_select" class="form-control" onchange="toggleStatusUser(this.value)" required>
                    <option value="Stock">ICT Stock</option>
                    <option value="Assigned">Assigned</option>
                    <option value="Damaged">Damaged</option>
                </select>
            </div>
            <div class="form-group-premium" id="status_user_div" style="display:none;">
                <label>Assigned To</label>
                <select name="user_id" id="status_user_select" class="form-control">
                    <option value="">Select Staff Member...</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['user_id'] ?>"><?= esc($user['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-footer mt-4">
                <button type="button" class="btn-secondary" onclick="closeModal('statusModal')">Cancel</button>
                <button type="submit" class="btn-primary">Update Status</button>
            </div>
        </form>
    </div>
</div>

<style>
    .assignee-details {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .assignee-name {
        font-weight: 700;
        color: #1e293b;
    }

    .btn-link-action {
        background: none;
        border: none;
        color: #ef4444;
        font-size: 0.7rem;
        cursor: pointer;
        text-decoration: underline;
        padding: 0;
        text-align: left;
    }

    .btn-assign-mini {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-assign-mini:hover {
        background: #e2e8f0;
        color: #1e293b;
        transform: translateY(-1px);
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    @media (max-width: 600px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }
</style>

<script>
    function openAddModal() { document.getElementById('addModal').style.display = 'flex'; }
    function openAssignModal(item) {
        document.getElementById('assign_id').value = item.id;
        document.getElementById('assign_item_display').innerText = item.brand + ' ' + item.model + ' (' + item.asset_code + ')';
        document.getElementById('assignModal').style.display = 'flex';
    }
    function openStatusModal(item) {
        document.getElementById('status_id').value = item.id;
        document.getElementById('status_select').value = item.status;
        document.getElementById('status_user_select').value = item.assigned_user_id || '';
        toggleStatusUser(item.status);
        document.getElementById('statusModal').style.display = 'flex';
    }

    function toggleStatusUser(status) {
        const userDiv = document.getElementById('status_user_div');
        const userSelect = document.getElementById('status_user_select');
        if (status === 'Assigned') {
            userDiv.style.display = 'block';
            userSelect.required = true;
        } else {
            userDiv.style.display = 'none';
            userSelect.required = false;
        }
    }

    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    window.onclick = function (event) {
        if (event.target.classList.contains('modal-premium-overlay')) {
            event.target.style.display = 'none';
        }
    }
</script>
<?= $this->endSection() ?>