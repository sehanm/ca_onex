<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Asset Inventory<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h2>IT Asset Repository</h2>
    <div class="action-buttons">
        <button class="btn-primary" onclick="$('#createAssetModal').show()">
            <i class="fa-solid fa-plus"></i> Create New Asset
        </button>
        <a href="<?= base_url('inventory/scan') ?>" class="btn-secondary">
            <i class="fa-solid fa-qrcode"></i> Scan to Find
        </a>
    </div>
</div>

<!-- Modal for Creating Asset -->
<div id="createAssetModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; padding: 20px; background-color: rgba(0,0,0,0.4); overflow-y: auto;">
    <div class="modal-content" style="background-color: #fefefe; margin: 20px auto; padding: 30px; border-radius: 12px; width: 100%; max-width: 500px; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
        <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 15px;">Register New Asset</h3>
        <form action="<?= base_url('inventory/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group-premium">
                <label>Hardware Model</label>
                <input type="text" name="model" class="form-control" placeholder="e.g. Dell Latitude 5420" required>
            </div>
            <div class="form-group-premium">
                <label>Serial Number (Unique)</label>
                <input type="text" name="serial_number" class="form-control" placeholder="e.g. SN-998877" required>
            </div>
            <div class="form-group-premium">
                <label>Internal Asset Code</label>
                <input type="text" name="asset_code" class="form-control" placeholder="e.g. ICT-LP-050">
            </div>
            <div style="text-align: right; margin-top: 25px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="$('#createAssetModal').hide()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Create Asset</button>
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
                <?php foreach($items as $item): 
                    $statusClass = 'status-default';
                    if($item['status'] == 'In Store') $statusClass = 'status-instore';
                    if($item['status'] == 'Assigned') $statusClass = 'status-assigned';
                    if($item['status'] == 'Repair') $statusClass = 'status-repair';
                    if($item['status'] == 'Retired') $statusClass = 'status-retired';
                ?>
                <tr>
                    <td>
                        <div class="item-id-cell">
                            <div class="item-icon-box">
                                <i class="fa-solid fa-laptop-medical"></i>
                            </div>
                            <div class="item-primary-info">
                                <span class="item-model"><?= esc($item['model']) ?></span>
                                <span class="item-code"><?= esc($item['asset_code'] ?: 'PENDING_CODE') ?></span>
                            </div>
                        </div>
                    </td>
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
                            <?php if($item['assigned_to']): ?>
                                <div class="avatar-lite"><?= strtoupper(substr($item['assigned_to'], 0, 1)) ?></div>
                                <span class="hide-mobile"><?= esc($item['assigned_to']) ?></span>
                            <?php else: ?>
                                <span class="unassigned-text"><i class="fa-solid fa-folder-open"></i> Unassigned</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="qr-thumb-wrapper" onclick="printQR(<?= $item['id'] ?>, '<?= esc($item['serial_number']) ?>')">
                            <img src="<?= site_url('inventory/generate-qr/'.$item['id']) ?>?t=<?= time() ?>" alt="QR" class="qr-min-thumb">
                            <div class="qr-hover-icon"><i class="fa-solid fa-expand"></i></div>
                        </div>
                    </td>
                    <td class="text-end">
                        <div class="action-flex">
                            <a href="<?= site_url('inventory/view/'.$item['id']) ?>" class="btn-action-view" title="Control Panel">
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
    .shadow-premium { box-shadow: 0 10px 40px rgba(0,0,0,0.04); border-radius: 20px; border: 1px solid #f1f5f9; background: white; padding: 25px; }
    
    .item-id-cell { display: flex; align-items: center; gap: 12px; min-width: 150px; }
    .item-icon-box { min-width: 40px; width: 40px; height: 40px; background: #f8fafc; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #6366f1; border: 1px solid #e2e8f0; }
    .item-model { font-weight: 700; color: #1e293b; display: block; font-size: 0.9rem; line-height: 1.3; }
    .item-code { font-size: 0.7rem; color: #94a3b8; font-family: 'JetBrains Mono', monospace; font-weight: 500; }

    .serial-code { background: #f1f5f9; padding: 4px 10px; border-radius: 6px; color: #475569; font-size: 0.85rem; border: 1px solid #e2e8f0; }

    .status-pill { display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-pill .dot { width: 6px; height: 6px; border-radius: 50%; }
    
    .status-instore { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
    .status-instore .dot { background: #3b82f6; }
    
    .status-assigned { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
    .status-assigned .dot { background: #10b981; }
    
    .status-repair { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
    .status-repair .dot { background: #f59e0b; }
    
    .status-retired { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
    .status-retired .dot { background: #ef4444; }

    .dept-chip { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: #64748b; font-weight: 500; }
    .dept-chip i { color: #94a3b8; }

    .user-chip-modern { display: flex; align-items: center; gap: 10px; font-size: 0.85rem; color: #334155; font-weight: 600; }
    .avatar-lite { width: 24px; height: 24px; background: #e0e7ff; color: #4338ca; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 800; }
    .unassigned-text { color: #cbd5e1; font-weight: 400; font-style: italic; }

    .qr-thumb-wrapper { position: relative; width: 44px; height: 44px; margin: 0 auto; cursor: pointer; border: 1.5px solid #f1f5f9; border-radius: 10px; padding: 4px; background: white; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .qr-min-thumb { width: 100%; height: 100%; object-fit: contain; }
    .qr-hover-icon { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(99, 102, 241, 0.9); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; opacity: 0; transition: all 0.2s; }
    .qr-thumb-wrapper:hover { transform: scale(1.1); border-color: #6366f1; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2); }
    .qr-thumb-wrapper:hover .qr-hover-icon { opacity: 1; }

    .action-flex { display: flex; gap: 8px; justify-content: flex-end; }
    .btn-action-view, .btn-action-print { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 10px; border: 1px solid #e2e8f0; color: #64748b; transition: all 0.2s; text-decoration: none; background: white; }
    .btn-action-view:hover { background: #f8fafc; color: #6366f1; border-color: #6366f1; transform: translateY(-2px); }
    .btn-action-print:hover { background: #6366f1; color: white; border-color: #6366f1; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2); }

    @media (max-width: 992px) {
        .hide-tablet { display: none !important; }
    }
    @media (max-width: 576px) {
        .hide-mobile { display: none !important; }
        .page-header { flex-direction: column; align-items: flex-start; gap: 15px; margin-bottom: 20px; }
        .page-header h2 { font-size: 1.4rem; }
        .action-buttons { width: 100%; display: flex; gap: 10px; }
        .action-buttons button, .action-buttons a { flex: 1; text-align: center; justify-content: center; padding: 10px 5px; font-size: 0.85rem; }
        .shadow-premium { padding: 15px 10px; border-radius: 12px; }
        .item-id-cell { gap: 8px; }
        .item-icon-box { min-width: 32px; width: 32px; height: 32px; font-size: 0.8rem; }
        .item-model { font-size: 0.8rem; line-height: 1.2; }
        .item-code { font-size: 0.65rem; }
        .status-pill { padding: 4px 8px; font-size: 0.65rem; }
        .qr-thumb-wrapper { width: 36px; height: 36px; padding: 2px; }
        .btn-action-view { width: 32px; height: 32px; }
        
        /* Modal tweaks */
        .modal-content { width: 95% !important; padding: 20px !important; margin: 10px auto !important; }
        
        /* DataTables tweaks for mobile */
        .dataTables_filter { margin-bottom: 10px !important; }
        .dataTables_filter input { width: 100% !important; margin-left: 0 !important; margin-top: 5px; }
    }
</style>

<script>
    function printQR(id, serial) {
        const url = `<?= site_url('inventory/generate-qr/') ?>${id}`;
        const win = window.open('', '_blank', 'width=400,height=400');
        win.document.write(`
            <html>
                <head><title>Print Asset Label: ${serial}</title></head>
                <body style="text-align:center; padding: 20px; font-family: Inter, sans-serif;">
                    <h3 style="margin-bottom:10px;">IT ASSET LABEL</h3>
                    <img src="${url}" style="width:200px; height:200px;">
                    <div style="margin-top:10px; font-weight:bold;">${serial}</div>
                    <div style="font-size:12px; color:#666;">CA OnEx Inventory Management</div>
                    <script>window.onload = function() { window.print(); window.close(); }<\/script>
                </body>
            </html>
        `);
        win.document.close();
    }
</script>
<?= $this->endSection() ?>
