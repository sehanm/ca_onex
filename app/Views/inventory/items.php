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

<div class="card inventory-list-card">
    <div class="table-responsive">
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Asset Detail</th>
                    <th>Serial Number</th>
                    <th>Status</th>
                    <th>Location (Dept)</th>
                    <th>Assigned To</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): 
                    $color = '#cbd5e1';
                    if($item['status'] == 'In Store') $color = '#3b82f6';
                    if($item['status'] == 'Assigned') $color = '#10b981';
                    if($item['status'] == 'Repair') $color = '#f59e0b';
                    if($item['status'] == 'Retired') $color = '#ef4444';
                ?>
                <tr>
                    <td>
                        <div class="item-primary-info">
                            <span class="item-model"><?= esc($item['model']) ?></span>
                            <span class="item-code"><?= esc($item['asset_code'] ?: 'No Code') ?></span>
                        </div>
                    </td>
                    <td><code><?= esc($item['serial_number']) ?></code></td>
                    <td>
                        <span class="status-indicator" style="background: <?= $color ?>;"></span>
                        <?= esc($item['status']) ?>
                    </td>
                    <td><?= esc($item['department_name'] ?? 'Not Set') ?></td>
                    <td>
                        <div class="user-chip">
                            <i class="fa-solid fa-user-tag"></i>
                            <?= esc($item['assigned_to'] ?? 'Unassigned') ?>
                        </div>
                    </td>
                    <td>
                        <div class="action-icons">
                            <a href="<?= base_url('inventory/view/'.$item['id']) ?>" class="btn-icon-lite" title="Manage Asset">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button class="btn-icon-lite" onclick="printQR(<?= $item['id'] ?>, '<?= esc($item['serial_number']) ?>')" title="Print QR Label">
                                <i class="fa-solid fa-print"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .inventory-list-card { border-radius: 16px; padding: 25px; background: white; border: 1px solid #f1f5f9; box-shadow: 0 4px 10px rgba(0,0,0,0.02); }
    .item-primary-info { display: flex; flex-direction: column; }
    .item-model { font-weight: 600; color: #1e293b; }
    .item-code { font-size: 0.75rem; color: #94a3b8; letter-spacing: 0.5px; }
    .tag-category { background: #f1f5f9; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 500; color: #475569; }
    .status-indicator { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 6px; }
    .btn-icon-lite { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #64748b; background: #f8fafc; border: 1px solid #e2e8f0; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .btn-icon-lite:hover { background: #f1f5f9; color: var(--primary-color); border-color: var(--primary-color); }
</style>

<script>
    function printQR(id, serial) {
        const url = `<?= base_url('inventory/generate-qr/') ?>${id}`;
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
