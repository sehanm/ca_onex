<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Manage Asset - <?= esc($item['serial_number']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="asset-view-container">
    <div class="asset-header-sticky">
        <div class="header-left">
            <a href="<?= base_url('inventory/items') ?>" class="btn-back">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
            <div class="header-title">
                <h1>Asset Identity Control</h1>
                <p>Identity Management for Device #<?= $item['id'] ?></p>
            </div>
        </div>
        <div class="header-actions">
            <button class="btn-print-lite" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Print Label
            </button>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar: QR & Immutable Data -->
        <div class="col-xl-4 col-lg-5">
            <div class="glass-card shadow-premium">
                <div class="qr-section">
                    <div class="qr-frame pulse-border">
                        <img src="<?= site_url('inventory/generate-qr/'.$item['id']) ?>?t=<?= time() ?>" alt="QR Code" draggable="false">
                    </div>
                    <div class="qr-status-badge">
                        <span class="dot live-dot"></span>
                        LIVE IDENTITY
                    </div>
                </div>

                <div class="immutable-details">
                    <div class="identity-block">
                        <label>Hardware Model</label>
                        <div class="val-pill">
                            <i class="fa-solid fa-laptop-code"></i>
                            <?= esc($item['model']) ?>
                        </div>
                    </div>
                    <div class="identity-block mt-3">
                        <label>Factory Serial</label>
                        <div class="val-pill serial-pill">
                            <i class="fa-solid fa-barcode"></i>
                            <?= esc($item['serial_number']) ?>
                        </div>
                    </div>
                    <div class="lock-box mt-4">
                        <i class="fa-solid fa-lock"></i>
                        <span>Hardware parameters are cryptographically locked and cannot be edited to maintain traceability.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Workspace: Editable Data -->
        <div class="col-xl-8 col-lg-7">
            <div class="form-card shadow-premium">
                <div class="workspace-header">
                    <h3>Lifecycle Management</h3>
                    <p>Update assignments, status, and internal tracking codes.</p>
                </div>

                <form action="<?= base_url('inventory/update') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $item['id'] ?>">

                    <div class="form-section">
                        <h4 class="section-tag">Organization Tracking</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="modern-input-group">
                                    <label><i class="fa-solid fa-hashtag"></i> Internal Asset Code</label>
                                    <input type="text" name="asset_code" value="<?= esc($item['asset_code']) ?>" placeholder="e.g. ICT-MB-2024-001">
                                    <small>Company-wide identification number</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section mt-4">
                        <h4 class="section-tag">Status & Deployment</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="modern-input-group">
                                    <label><i class="fa-solid fa-signal"></i> Operational Status</label>
                                    <div class="custom-select-v2">
                                        <select name="status">
                                            <?php foreach(['In Store', 'Assigned', 'Repair', 'Retired'] as $status): ?>
                                            <option value="<?= $status ?>" <?= $item['status'] == $status ? 'selected' : '' ?>><?= $status ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="modern-input-group">
                                    <label><i class="fa-solid fa-building"></i> Current Department</label>
                                    <div class="custom-select-v2">
                                        <select name="department_id">
                                            <option value="">-- No Department Assigned --</option>
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
                    </div>

                    <div class="form-section mt-4">
                        <h4 class="section-tag">Personnel Assignment</h4>
                        <div class="modern-input-group">
                            <label><i class="fa-solid fa-user-tag"></i> Custodian (Assigned User)</label>
                            <div class="custom-select-v2">
                                <select name="assigned_user_id">
                                    <option value="">-- Unassigned / In Store --</option>
                                    <?php foreach($users as $user): ?>
                                    <option value="<?= $user['user_id'] ?>" <?= $item['assigned_user_id'] == $user['user_id'] ? 'selected' : '' ?>>
                                        <?= esc($user['full_name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section mt-4">
                        <h4 class="section-tag">Activity Log & Condition Notes</h4>
                        <div class="modern-input-group">
                            <textarea name="notes" rows="4" placeholder="Describe recent repairs, physical condition, or specific usage constraints..."><?= esc($item['notes']) ?></textarea>
                        </div>
                    </div>

                    <div class="footer-actions mt-5">
                        <button type="submit" class="btn-save-glow">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Save Lifecycle Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Premium Styles for Asset Management */
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        --glass-bg: rgba(255, 255, 255, 0.95);
        --input-focus: #818cf8;
    }

    .asset-view-container { padding: 20px; max-width: 1400px; margin: 0 auto; }

    .asset-header-sticky {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 30px; position: sticky; top: 0; z-index: 10;
        background: rgba(248, 250, 252, 0.8); backdrop-filter: blur(10px);
        padding: 15px 0; border-bottom: 1px solid #e2e8f0;
    }

    .header-left { display: flex; align-items: center; gap: 20px; }
    .btn-back { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: white; border: 1px solid #e2e8f0; color: #64748b; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); text-decoration: none; }
    .btn-back:hover { border-color: #6366f1; color: #6366f1; transform: translateX(-5px); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15); }

    .header-title h1 { font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0; letter-spacing: -0.5px; }
    .header-title p { margin: 0; color: #64748b; font-size: 0.85rem; }

    .btn-print-lite { padding: 10px 20px; border-radius: 10px; background: white; border: 1px solid #e2e8f0; font-weight: 600; font-size: 0.9rem; color: #1e293b; cursor: pointer; transition: all 0.3s; }
    .btn-print-lite:hover { background: #f8fafc; border-color: #6366f1; color: #6366f1; }

    /* Glass Card Sidebar */
    .glass-card { background: var(--glass-bg); border-radius: 24px; padding: 35px; border: 1px solid #f1f5f9; height: 100%; }
    .qr-section { display: flex; flex-direction: column; align-items: center; margin-bottom: 40px; }
    
    .qr-frame { width: 220px; height: 220px; background: white; padding: 15px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 20px; display: flex; align-items: center; justify-content: center; position: relative; }
    .qr-frame img { width: 100%; height: 100%; border-radius: 8px; object-fit: contain; }
    
    .pulse-border { border: 2px solid #e2e8f0; animation: frame-pulse 4s infinite; }
    @keyframes frame-pulse { 0% { border-color: #e2e8f0; } 50% { border-color: #a855f7; } 100% { border-color: #e2e8f0; } }

    .qr-status-badge { background: #f0fdf4; color: #15803d; padding: 6px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; display: flex; align-items: center; gap: 8px; border: 1px solid #dcfce7; }
    .live-dot { width: 8px; height: 8px; background: #22c55e; border-radius: 50%; box-shadow: 0 0 8px #22c55e; animation: blink 2s infinite; }
    @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }

    .identity-block label { font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; display: block; }
    .val-pill { background: #f8fafc; border: 1px solid #f1f5f9; padding: 12px 18px; border-radius: 12px; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 12px; }
    .val-pill i { color: #6366f1; }
    .serial-pill { font-family: 'JetBrains Mono', 'Courier New', monospace; color: #7c3aed; }

    .lock-box { background: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; padding: 15px; display: flex; gap: 12px; font-size: 0.8rem; color: #92400e; line-height: 1.5; }
    .lock-box i { font-size: 1.1rem; margin-top: 2px; }

    /* Form Workspace */
    .form-card { background: white; border-radius: 24px; padding: 40px; border: 1px solid #f1f5f9; }
    .workspace-header { margin-bottom: 40px; }
    .workspace-header h3 { font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0; }
    .workspace-header p { color: #64748b; margin-top: 5px; }

    .section-tag { font-size: 0.8rem; font-weight: 800; color: #6366f1; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 20px; display: inline-block; padding-bottom: 5px; border-bottom: 2px solid #e0e7ff; }

    .modern-input-group { margin-bottom: 10px; }
    .modern-input-group label { display: block; font-size: 0.9rem; font-weight: 600; color: #475569; margin-bottom: 10px; }
    .modern-input-group label i { margin-right: 8px; color: #94a3b8; }
    
    .modern-input-group input, .modern-input-group textarea { width: 100%; padding: 14px 20px; border: 1.5px solid #e2e8f0; border-radius: 14px; font-size: 1rem; color: #1e293b; transition: all 0.2s; background: #fcfdfe; }
    .modern-input-group input:focus, .modern-input-group textarea:focus { border-color: var(--input-focus); outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); background: white; }
    .modern-input-group small { display: block; margin-top: 8px; color: #94a3b8; font-size: 0.8rem; }

    .custom-select-v2 { position: relative; }
    .custom-select-v2 select { width: 100%; padding: 14px 20px; border: 1.5px solid #e2e8f0; border-radius: 14px; appearance: none; background: #fcfdfe; cursor: pointer; font-size: 1rem; color: #1e293b; }
    .custom-select-v2::after { content: '\f078'; font-family: 'Font Awesome 6 Free'; font-weight: 900; position: absolute; right: 20px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8; font-size: 0.8rem; }
    .custom-select-v2 select:focus { border-color: var(--input-focus); box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); outline: none; }

    .btn-save-glow { width: 100%; padding: 18px; border-radius: 16px; border: none; background: var(--primary-gradient); color: white; font-weight: 700; font-size: 1.1rem; cursor: pointer; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); display: flex; align-items: center; justify-content: center; gap: 12px; box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3); }
    .btn-save-glow:hover { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(99, 102, 241, 0.45); }
    .btn-save-glow:active { transform: translateY(0); }

    .shadow-premium { box-shadow: 0 20px 50px rgba(0,0,0,0.03); }

    @media (print) {
        .dashboard-container { padding: 0 !important; }
        .sidebar, .topbar, .asset-header-sticky, .col-xl-8, .lock-box { display: none !important; }
        .col-xl-4 { width: 100% !important; border: none !important; }
        .glass-card { border: none !important; padding: 0 !important; }
        .qr-frame { width: 300px !important; height: 300px !important; border: 1px solid #eee !important; margin: 0 auto !important; }
        .immutable-details { text-align: center; }
        .val-pill { border: none !important; background: transparent !important; display: block !important; font-size: 1.2rem !important; }
    }
</style>
<?= $this->endSection() ?>
