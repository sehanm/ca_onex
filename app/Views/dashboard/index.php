<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Welcome Banner -->
<div class="welcome-banner"
    style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 40px; border-radius: 24px; color: white; margin-bottom: 32px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); position: relative; overflow: hidden;">
    <!-- Abstract background shape -->
    <div
        style="position: absolute; top: -50px; right: -50px; width: 250px; height: 250px; background: rgba(255,255,255,0.05); border-radius: 50%; filter: blur(40px);">
    </div>
    <div style="position: relative; z-index: 1;">
        <h1 style="font-size: 2.2rem; font-weight: 800; margin-bottom: 8px; color: white;">Welcome back,
            <?= esc($full_name) ?>! 👋
        </h1>
        <p style="color: #94a3b8; font-size: 1.1rem; max-width: 600px;">Here is an overview of the system and your personal workspace.</p>
    </div>
</div>

<?php 
$userRoles = (array)(session()->get('roles') ?: [session()->get('role')]);
$isAdmin = !empty(array_intersect($userRoles, ['Super Admin', 'HR Admin', 'ICT Admin']));
?>

<?php if ($isAdmin): ?>
    <!-- ============================================== -->
    <!--          ADMIN SYSTEM OVERVIEW SECTION         -->
    <!-- ============================================== -->
    <div class="admin-dashboard-section" style="margin-bottom: 40px;">
        <div class="section-title-flex"
            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin:0;">
                <i class="fa-solid fa-chart-pie" style="color: #0ea5e9; margin-right: 10px;"></i>System Overview
            </h2>
        </div>

        <!-- Quick Stats Cards -->
        <div class="dashboard-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 24px;">
            <div class="stat-card" style="background: white; border-radius: 20px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 20px;">
                <div class="stat-icon" style="background: #dcfce7; color: #16a34a; width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-details">
                    <h3 style="font-size: 0.9rem; color: #64748b; font-weight: 600; margin-bottom: 4px;">Total Employees</h3>
                    <p style="font-size: 1.5rem; color: #1e293b; font-weight: 800; margin: 0;"><?= number_format($totalEmployees ?? 0) ?></p>
                </div>
            </div>

            <div class="stat-card" style="background: white; border-radius: 20px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 20px;">
                <div class="stat-icon" style="background: #e0f2fe; color: #0284c7; width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-laptop"></i>
                </div>
                <div class="stat-details">
                    <h3 style="font-size: 0.9rem; color: #64748b; font-weight: 600; margin-bottom: 4px;">Total Assets</h3>
                    <p style="font-size: 1.5rem; color: #1e293b; font-weight: 800; margin: 0;"><?= number_format($totalAssets ?? 0) ?></p>
                </div>
            </div>

            <div class="stat-card" style="background: white; border-radius: 20px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 20px;">
                <div class="stat-icon" style="background: #ffedd5; color: #ea580c; width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-keyboard"></i>
                </div>
                <div class="stat-details">
                    <h3 style="font-size: 0.9rem; color: #64748b; font-weight: 600; margin-bottom: 4px;">Accessories</h3>
                    <p style="font-size: 1.5rem; color: #1e293b; font-weight: 800; margin: 0;"><?= number_format($totalAccessories ?? 0) ?></p>
                </div>
            </div>

            <div class="stat-card" style="background: white; border-radius: 20px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 20px;">
                <div class="stat-icon" style="background: #fee2e2; color: #dc2626; width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div class="stat-details">
                    <h3 style="font-size: 0.9rem; color: #64748b; font-weight: 600; margin-bottom: 4px;">Pending Requests</h3>
                    <p style="font-size: 1.5rem; color: #1e293b; font-weight: 800; margin: 0;"><?= number_format($pendingRequests ?? 0) ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <hr style="border-top: 2px dashed #e2e8f0; margin: 30px 0;">
<?php endif; ?>

<!-- ============================================== -->
<!--             MY WORKSPACE SECTION               -->
<!-- ============================================== -->
<div class="dashboard-user-view">
    <div class="section-title-flex"
        style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin:0;">
            <i class="fa-solid fa-briefcase" style="color: #6366f1; margin-right: 10px;"></i>My Workspace
        </h2>
        
        <a href="<?= base_url('hardware-requests/create') ?>" class="btn-premium"
            style="font-size: 0.9rem; padding: 8px 16px; border-radius: 10px; background: #8b5cf6; color: white; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 600;">
            <i class="fa-solid fa-plus"></i> New Request
        </a>
    </div>

    <!-- 2 Column Layout for Equipment and Info -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px;">
        
        <!-- Left Column: Equipment & Accessories -->
        <div>
            <!-- My Equipment -->
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #334155; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-laptop-code" style="color: #3b82f6;"></i> Equipment
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 32px;">
                <?php if (empty($assignedAssets)): ?>
                    <div class="empty-state"
                        style="background: white; padding: 30px; border-radius: 20px; text-align: center; border: 2px dashed #e2e8f0;">
                        <i class="fa-solid fa-desktop" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 12px;"></i>
                        <h4 style="color: #475569; font-size: 1rem; font-weight: 600; margin: 0;">No Equipment</h4>
                        <p style="color: #94a3b8; font-size: 0.9rem; margin-top: 4px;">No hardware assets assigned.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($assignedAssets as $asset): ?>
                        <div class="asset-card"
                            style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; transition: transform 0.2s, box-shadow 0.2s;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                <div style="display: flex; gap: 16px; align-items: center;">
                                    <div style="width: 45px; height: 45px; background: #eff6ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #3b82f6; flex-shrink: 0;">
                                        <i class="fa-solid fa-<?= stripos($asset['type'], 'laptop') !== false ? 'laptop' : (stripos($asset['type'], 'desktop') !== false ? 'desktop' : 'server') ?>"></i>
                                    </div>
                                    <div>
                                        <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0 0 4px 0;"><?= esc($asset['model']) ?></h3>
                                        <p style="color: #64748b; font-size: 0.85rem; margin: 0;"><?= esc($asset['type']) ?> • <?= esc($asset['asset_code']) ?></p>
                                    </div>
                                </div>
                                <span class="badge"
                                    style="background: <?= $asset['status'] == 'Assigned' ? '#dcfce7; color: #16a34a;' : '#f3f4f6; color: #475569;' ?> padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                                    <?= esc($asset['status']) ?>
                                </span>
                            </div>

                            <div style="background: #f8fafc; padding: 10px 12px; border-radius: 10px; font-size: 0.8rem;">
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: #64748b;">Serial No.</span>
                                    <span style="color: #334155; font-weight: 600;"><?= esc($asset['serial_number']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- My Accessories -->
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #334155; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-keyboard" style="color: #8b5cf6;"></i> Accessories
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <?php if (empty($assignedAccessories)): ?>
                    <div class="empty-state"
                        style="background: white; padding: 30px; border-radius: 20px; text-align: center; border: 2px dashed #e2e8f0;">
                        <i class="fa-solid fa-mouse" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 12px;"></i>
                        <h4 style="color: #475569; font-size: 1rem; font-weight: 600; margin: 0;">No Accessories</h4>
                        <p style="color: #94a3b8; font-size: 0.9rem; margin-top: 4px;">No accessories assigned.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($assignedAccessories as $acc): ?>
                        <div class="asset-card"
                            style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; transition: transform 0.2s, box-shadow 0.2s; display: flex; gap: 16px; align-items: center;">
                            <div style="width: 45px; height: 45px; background: #f3e8ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #8b5cf6; flex-shrink: 0;">
                                <i class="fa-solid fa-<?= stripos($acc['category'], 'mouse') !== false ? 'mouse' : (stripos($acc['category'], 'keyboard') !== false ? 'keyboard' : 'headphones') ?>"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0 0 4px 0;"><?= esc($acc['brand']) ?> <?= esc($acc['model']) ?></h3>
                                <p style="color: #64748b; font-size: 0.85rem; margin: 0;"><?= esc($acc['category']) ?> • <?= esc($acc['asset_code']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Recent Requests -->
        <div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: #334155; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-file-signature" style="color: #f59e0b;"></i> Recent Requests
                </h3>
                <a href="<?= base_url('hardware-requests') ?>" style="font-size: 0.85rem; color: #3b82f6; text-decoration: none; font-weight: 600;">View All</a>
            </div>

            <div class="card" style="padding: 0; overflow: hidden; border: 1px solid #f1f5f9; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                <?php if (empty($recentRequests)): ?>
                    <div style="padding: 40px; text-align: center;">
                        <i class="fa-solid fa-inbox" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 12px; display: block;"></i>
                        <h4 style="color: #475569; font-size: 1rem; font-weight: 600; margin: 0;">No Active Requests</h4>
                        <p style="color: #94a3b8; font-size: 0.9rem; margin-top: 4px;">You haven't made any requests recently.</p>
                    </div>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column;">
                        <?php foreach (array_slice($recentRequests, 0, 1) as $idx => $req): ?>
                            <div style="padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: <?= ($idx < count(array_slice($recentRequests, 0, 1)) - 1) ? '1px solid #f1f5f9' : 'none' ?>;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                        <span style="font-weight: 700; color: #1e293b; font-size: 0.95rem; text-transform: capitalize;"><?= esc($req['item_type']) ?></span>
                                        <span style="color: #94a3b8; font-size: 0.85rem;">•</span>
                                        <span style="color: #64748b; font-size: 0.85rem;"><?= esc($req['category']) ?></span>
                                    </div>
                                    <div style="color: #94a3b8; font-size: 0.8rem;">
                                        <i class="fa-regular fa-clock" style="margin-right: 4px;"></i> <?= date('M d, Y', strtotime($req['created_at'])) ?>
                                    </div>
                                </div>
                                
                                <?php
                                $statusConf = [
                                    'Approved' => ['bg' => '#dcfce7', 'color' => '#16a34a'],
                                    'Assigned' => ['bg' => '#dcfce7', 'color' => '#16a34a'],
                                    'Pending'  => ['bg' => '#fef3c7', 'color' => '#d97706'],
                                    'Rejected' => ['bg' => '#fee2e2', 'color' => '#dc2626'],
                                    'Returned' => ['bg' => '#f1f5f9', 'color' => '#64748b']
                                ];
                                $sConf = $statusConf[$req['status']] ?? ['bg' => '#e0f2fe', 'color' => '#0284c7'];
                                ?>
                                <span class="badge" style="background: <?= $sConf['bg'] ?>; color: <?= $sConf['color'] ?>; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">
                                    <?= esc($req['status']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .asset-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
    }
</style>

<?= $this->endSection() ?>