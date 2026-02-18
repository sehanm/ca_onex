<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Hardware Overview<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-premium">
    <div class="header-main">
        <h2 class="title-gradient">Hardware Control Tower</h2>
        <p class="subtitle">Unified visualization of hardware requests and inventory.</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('hardware-requests/manage') ?>" class="btn-premium">
            <i class="fa-solid fa-list-check"></i> Manage Requests
        </a>
    </div>
</div>

<div class="inventory-stats-row">
    <!-- Total Card -->
    <div class="overview-card total">
        <div class="card-icon"><i class="fa-solid fa-microchip"></i></div>
        <div class="card-data">
            <h3 class="stat-number"><?= $stats['total'] ?></h3>
            <p class="stat-label">Total Requests</p>
        </div>
    </div>

    <!-- Active Card -->
    <div class="overview-card">
        <div class="card-icon" style="background: #ecfdf5; color: #047857;">
            <i class="fa-solid fa-laptop"></i>
        </div>
        <div class="card-data">
            <h3 class="stat-number"><?= $stats['assigned'] ?></h3>
            <p class="stat-label">Active Assets</p>
        </div>
    </div>

    <!-- Pending Card -->
    <div class="overview-card">
        <div class="card-icon" style="background: #fffbeb; color: #b45309;">
            <i class="fa-solid fa-clock"></i>
        </div>
        <div class="card-data">
            <h3 class="stat-number"><?= $stats['pending'] ?></h3>
            <p class="stat-label">Pending Approval</p>
        </div>
    </div>

    <!-- Returned Card -->
    <div class="overview-card">
        <div class="card-icon" style="background: #f1f5f9; color: #475569;">
            <i class="fa-solid fa-rotate-left"></i>
        </div>
        <div class="card-data">
            <h3 class="stat-number"><?= $stats['returned'] ?></h3>
            <p class="stat-label">Returned</p>
        </div>
    </div>
</div>

<div class="chart-layout-grid">
    <div class="chart-container-card">
        <div class="card-header-flex">
            <h4><i class="fa-solid fa-layer-group"></i> Request Type Distribution</h4>
        </div>
        <div class="chart-body">
            <canvas id="typeChart"></canvas>
        </div>
    </div>

    <div class="chart-container-card">
        <div class="card-header-flex">
            <h4><i class="fa-solid fa-chart-pie"></i> Request Status</h4>
        </div>
        <div class="chart-body">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Type Chart (Asset vs Accessory)
        const typeCtx = document.getElementById('typeChart').getContext('2d');
        new Chart(typeCtx, {
            type: 'pie',
            data: {
                labels: ['Assets', 'Accessories'],
                datasets: [{
                    data: [<?= $stats['asset'] ?>, <?= $stats['accessory'] ?>],
                    backgroundColor: ['#3b82f6', '#8b5cf6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right' } }
            }
        });

        // Status Chart
        const statCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statCtx, {
            type: 'doughnut',
            data: {
                labels: ['Assigned', 'Pending', 'Returned', 'Rejected'],
                datasets: [{
                    data: [
                        <?= $stats['assigned'] ?>,
                        <?= $stats['pending'] ?>,
                        <?= $stats['returned'] ?>,
                        <?= $stats['rejected'] ?>
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#64748b', '#ef4444'],
                    borderWidth: 0,
                    cutout: '75%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    });
</script>
<?= $this->endSection() ?>