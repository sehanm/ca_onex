<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Manage Asset<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div style="display:flex; align-items:center; gap:15px;">
        <a href="<?= base_url('inventory/items') ?>" class="btn-icon-lite"><i class="fa-solid fa-arrow-left"></i></a>
        <h2>Asset Control Panel</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card asset-profile-card">
            <div class="qr-preview-box">
                <img src="<?= base_url('inventory/generate-qr/'.$item['id']) ?>" alt="QR Code">
                <p>Scan to update from mobile</p>
            </div>
            <div class="asset-meta-data">
                <div class="meta-row immutable-meta">
                    <span>Model</span>
                    <strong><?= esc($item['model']) ?></strong>
                </div>
                <div class="meta-row immutable-meta">
                    <span>Serial Number</span>
                    <strong><?= esc($item['serial_number']) ?></strong>
                </div>
                <p class="lock-notice"><i class="fa-solid fa-lock"></i> Hardware identity is permanent</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card inventory-card">
            <div class="card-header">
                <h3>Asset Management</h3>
            </div>
            <form action="<?= base_url('inventory/update') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group-premium">
                            <label>Internal Asset Code</label>
                            <input type="text" name="asset_code" class="form-control" value="<?= esc($item['asset_code']) ?>" placeholder="e.g. ICT-001">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group-premium">
                            <label>Asset Status</label>
                            <div class="select-wrapper">
                                <select name="status">
                                    <?php foreach(['In Store', 'Assigned', 'Repair', 'Retired'] as $status): ?>
                                    <option value="<?= $status ?>" <?= $item['status'] == $status ? 'selected' : '' ?>>
                                        <?= $status ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group-premium">
                            <label>Location (Department)</label>
                            <div class="select-wrapper">
                                <select name="department_id">
                                    <option value="">Select Department</option>
                                    <?php foreach($departments as $dept): ?>
                                    <option value="<?= $dept['id'] ?>" <?= $item['department_id'] == $dept['id'] ? 'selected' : '' ?>>
                                        <?= esc($dept['department_name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group-premium">
                    <label>Assigned User</label>
                    <div class="select-wrapper">
                        <select name="assigned_user_id">
                            <option value="">No Active User</option>
                            <?php foreach($users as $user): ?>
                            <option value="<?= $user['user_id'] ?>" <?= $item['assigned_user_id'] == $user['user_id'] ? 'selected' : '' ?>>
                                <?= esc($user['full_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group-premium">
                    <label>Maintenance & Usage Notes</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Add hardware info, maintenance logs, or assignment details..."><?= esc($item['notes']) ?></textarea>
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .qr-preview-box { text-align: center; padding-bottom: 25px; border-bottom: 1px dashed #e2e8f0; margin-bottom: 20px; }
    .qr-preview-box img { width: 160px; height: 160px; padding: 10px; background: white; border-radius: 12px; border: 1px solid #f1f5f9; }
    .qr-preview-box p { font-size: 0.8rem; color: #94a3b8; margin-top: 10px; }
    
    .asset-meta-data { display: flex; flex-direction: column; gap: 12px; }
    .meta-row { display: flex; justify-content: space-between; align-items: center; }
    .meta-row span { font-size: 0.85rem; color: #64748b; }
    .meta-row strong { color: #1e293b; }
    
    .immutable-meta { background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #f1f5f9; }
    .lock-notice { font-size: 0.7rem; color: #94a3b8; text-align: center; margin-top: 5px; }
    .lock-notice i { margin-right: 4px; }
    
    .timeline-container { padding-left: 20px; border-left: 2px solid #f1f5f9; margin-left: 10px; }
    .timeline-item { position: relative; padding-bottom: 25px; }
    .timeline-marker { position: absolute; left: -26px; top: 0; width: 10px; height: 10px; border-radius: 50%; background: var(--primary-color); border: 2px solid white; box-shadow: 0 0 0 2px #f1f5f9; }
    .timeline-header { display: flex; justify-content: space-between; margin-bottom: 5px; }
    .event-type { font-weight: 700; font-size: 0.75rem; color: var(--primary-color); text-transform: uppercase; }
    .event-time { font-size: 0.75rem; color: #94a3b8; }
    .event-desc { font-size: 0.9rem; margin: 0; color: #475569; }
    .event-json { background: #f8fafc; padding: 8px; border-radius: 6px; font-size: 0.75rem; margin-top: 8px; overflow-x: auto; color: #64748b; }
</style>
<?= $this->endSection() ?>
