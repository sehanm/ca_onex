<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Asset Inventory<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-premium">
    <div class="header-main">
        <h2 class="title-gradient">IT Asset Repository</h2>
        <p class="subtitle">Full lifecycle tracking of organizational hardware assets.</p>
    </div>
    <div class="action-buttons action-flex">
        <a href="<?= base_url('inventory/scan') ?>" class="btn-premium-outline">
            <i class="fa-solid fa-qrcode"></i> Quick Scan
        </a>
        <button class="btn-primary" onclick="openModal('createAssetModal')">
            <i class="fa-solid fa-plus"></i> Register New Asset
        </button>
    </div>
</div>

<!-- Modal for Creating Asset -->
<div id="createAssetModal" class="modal-premium-overlay">
    <div class="modal-premium-content">
        <div class="modal-header">
            <h3>Register New Hardware Asset</h3>
            <button class="close-btn" onclick="closeModal('createAssetModal')">&times;</button>
        </div>
        <form action="<?= base_url('inventory/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-grid">
                <div class="form-group-premium">
                    <label>Device Type *</label>
                    <select name="type" class="form-control" required>
                        <option value="Laptop">Laptop</option>
                        <option value="Desktop">Desktop</option>
                        <option value="Monitor">Monitor</option>
                        <option value="Printer">Printer</option>
                        <option value="Accessory">Accessory</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group-premium">
                    <label>Hardware Model *</label>
                    <input type="text" name="model" class="form-control" placeholder="e.g. Dell Latitude 5420" required>
                </div>
                <div class="form-group-premium">
                    <label>Serial Number *</label>
                    <input type="text" name="serial_number" class="form-control" placeholder="e.g. SN-998877" required>
                </div>
                <div class="form-group-premium">
                    <label>Asset Code</label>
                    <input type="text" name="asset_code" class="form-control" placeholder="e.g. ICT-LP-050">
                </div>
            </div>

            <div class="form-group-premium mt-1">
                <label>Inventory Notes</label>
                <textarea name="notes" class="form-control" rows="2"
                    placeholder="Condition details or storage location..."></textarea>
            </div>

            <div class="modal-footer mt-4">
                <button type="button" onclick="closeModal('createAssetModal')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Initialize Asset Record</button>
            </div>
        </form>
    </div>
</div>

<div class="card inventory-list-card shadow-premium">
    <div class="table-responsive">
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Asset Profile</th>
                    <th class="hide-md-desktop">Serial Identity</th>
                    <th>Availability</th>
                    <th class="hide-tablet">Department</th>
                    <th class="hide-tablet">Assignment</th>
                    <th class="text-center">QR Identity</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item):
                    $statusClass = 'status-default';
                    if ($item['status'] == 'In Store')
                        $statusClass = 'status-instore';
                    if ($item['status'] == 'Assigned')
                        $statusClass = 'status-assigned';
                    if ($item['status'] == 'Repair')
                        $statusClass = 'status-repair';
                    if ($item['status'] == 'Retired')
                        $statusClass = 'status-retired';
                    ?>
                    <tr>
                        <td>
                            <div class="item-id-cell">
                                <div class="item-icon-box">
                                    <?php if (($item['type'] ?? '') == 'Laptop'): ?>
                                        <i class="fa-solid fa-laptop"></i>
                                    <?php elseif (($item['type'] ?? '') == 'Desktop'): ?>
                                        <i class="fa-solid fa-computer"></i>
                                    <?php elseif (($item['type'] ?? '') == 'Monitor'): ?>
                                        <i class="fa-solid fa-tv"></i>
                                    <?php elseif (($item['type'] ?? '') == 'Printer'): ?>
                                        <i class="fa-solid fa-print"></i>
                                    <?php else: ?>
                                        <i class="fa-solid fa-box-open"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="item-primary-info">
                                    <span class="item-model">
                                        <?= esc($item['model']) ?>
                                        <?php if (!empty($item['type'])): ?>
                                            <span class="badge"
                                                style="font-size: 0.65rem; background:#f1f5f9; color:#64748b; margin-left:5px; border:1px solid #e2e8f0;"><?= esc($item['type']) ?></span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="item-code"><?= esc($item['asset_code'] ?: 'PENDING_CODE') ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="hide-md-desktop"><code class="serial-code"><?= esc($item['serial_number']) ?></code></td>
                        <td>
                            <span class="status-pill <?= $statusClass ?>">
                                <span class="dot"></span>
                                <?= esc($item['status']) ?>
                            </span>
                        </td>
                        <td class="hide-tablet">
                            <div class="dept-chip">
                                <i class="fa-solid fa-building-user"></i>
                                <?= esc($item['department_name'] ?? 'Warehouse') ?>
                            </div>
                        </td>
                        <td class="hide-tablet">
                            <div class="user-chip-modern">
                                <?php if ($item['assigned_to']): ?>
                                    <div class="avatar-lite"><?= strtoupper(substr($item['assigned_to'], 0, 1)) ?></div>
                                    <span class="hide-mobile"><?= esc($item['assigned_to']) ?></span>
                                <?php else: ?>
                                    <span class="unassigned-text"><i class="fa-solid fa-folder-open"></i> Unassigned</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="qr-thumb-wrapper"
                                onclick="downloadQR(<?= $item['id'] ?>, '<?= esc($item['serial_number']) ?>')">
                                <img src="<?= site_url('inventory/generate-qr/' . $item['id']) ?>?t=<?= time() ?>" alt="QR"
                                    class="qr-min-thumb">
                                <div class="qr-hover-icon"><i class="fa-solid fa-download"></i></div>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="action-flex">
                                <a href="<?= site_url('inventory/view/' . $item['id']) ?>" class="btn-action-view"
                                    title="Control Panel">
                                    <i class="fa-solid fa-sliders"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Local QR Identity Styles */
    .qr-thumb-wrapper {
        position: relative;
        width: 44px;
        height: 44px;
        margin: 0 auto;
        cursor: pointer;
        border: 1.5px solid #f1f5f9;
        border-radius: 10px;
        padding: 4px;
        background: white;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .qr-min-thumb {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .qr-hover-icon {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(99, 102, 241, 0.9);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transition: all 0.2s;
    }

    .qr-thumb-wrapper:hover {
        transform: scale(1.1);
        border-color: #6366f1;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    }

    .qr-thumb-wrapper:hover .qr-hover-icon {
        opacity: 1;
    }

    @media (max-width: 992px) {
        .hide-tablet {
            display: none !important;
        }
    }

    @media (max-width: 576px) {
        .hide-mobile {
            display: none !important;
        }

        .shadow-premium {
            padding: 15px 10px;
            border-radius: 12px;
        }

        .qr-thumb-wrapper {
            width: 36px;
            height: 36px;
            padding: 2px;
        }
    }
</style>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    // Modal close when clicking outside
    window.onclick = function (event) {
        if (event.target.classList.contains('modal-premium-overlay')) {
            event.target.style.display = 'none';
        }
    }

    async function downloadQR(id, serial) {
        const url = `<?= site_url('inventory/generate-qr/') ?>${id}`;
        try {
            const response = await fetch(url);
            const blob = await response.blob();
            const downloadUrl = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = downloadUrl;
            a.download = `QR_${serial}.png`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(downloadUrl);
            a.remove();
        } catch (error) {
            console.error('Download failed:', error);
            alert('Failed to download QR code');
        }
    }
</script>
<?= $this->endSection() ?>