<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Inventory Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h2>Inventory Control Tower</h2>
    <div class="action-buttons">
        <a href="<?= base_url('inventory/scan') ?>" class="btn-primary">
            <i class="fa-solid fa-qrcode"></i> Quick Scan
        </a>
        <a href="<?= base_url('inventory/items') ?>" class="btn-secondary">
            <i class="fa-solid fa-list"></i> View All Assets
        </a>
    </div>
</div>

<div class="dashboard-stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--primary-light); color: white;">
            <i class="fa-solid fa-microchip"></i>
        </div>
        <div class="stat-info">
            <h3><?= $total_assets ?></h3>
            <p>Total Assets Logged</p>
        </div>
    </div>
    
    <?php 
    foreach($by_status as $status): 
        $color = '#cbd5e1'; // Default
        if($status['status'] == 'In Store') $color = '#3b82f6';
        if($status['status'] == 'Assigned') $color = '#10b981';
        if($status['status'] == 'Repair') $color = '#f59e0b';
        if($status['status'] == 'Retired') $color = '#ef4444';
    ?>
    <div class="stat-card">
        <div class="stat-icon" style="background: <?= $color ?>22; color: <?= $color ?>;">
            <i class="fa-solid fa-circle-dot"></i>
        </div>
        <div class="stat-info">
            <h3><?= $status['count'] ?></h3>
            <p><?= $status['status'] ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row" style="margin-top: 30px;">
    <div class="col-md-12">
        <div class="card inventory-card">
            <div class="card-header">
                <h3>Asset Management Summary</h3>
            </div>
            <p style="color: #64748b;">This portal allows you to manage IT hardware with serial numbers and asset codes. You can assign assets to specific users and departments, and track their current status via QR scanning.</p>
        </div>
    </div>
</div>

<style>
    .dashboard-stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; }
    .stat-card { background: white; padding: 25px; border-radius: 16px; display: flex; align-items: center; gap: 20px; border: 1px solid #f1f5f9; transition: transform 0.3s ease; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
    .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .stat-info h3 { margin: 0; font-size: 1.5rem; color: #1e293b; }
    .stat-info p { margin: 0; color: #64748b; font-size: 0.85rem; }
    
    .inventory-card { padding: 20px; border-radius: 16px; background: white; border: 1px solid #f1f5f9; }
    .inventory-card h3 { font-size: 1.1rem; margin-bottom: 20px; color: #1e293b; }
    .health-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
    .health-item:last-child { border-bottom: none; }
</style>
<?= $this->endSection() ?>
