<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Inventory Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-premium">
    <div class="header-main">
        <h2 class="title-gradient">Inventory Control Tower</h2>
        <p class="subtitle">Unified visualization of IT asset lifecycle and deployment.</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('inventory/scan') ?>" class="btn-premium-outline">
            <i class="fa-solid fa-qrcode"></i> Quick Scan
        </a>
        <a href="<?= base_url('inventory/items') ?>" class="btn-premium">
            <i class="fa-solid fa-boxes-stacked"></i> Asset Repository
        </a>
    </div>
</div>

<div class="inventory-stats-row">
    <div class="overview-card total">
        <div class="card-icon"><i class="fa-solid fa-microchip"></i></div>
        <div class="card-data">
            <h3 class="stat-number"><?= $total_assets ?></h3>
            <p class="stat-label">Total Global Assets</p>
        </div>
        <div class="card-decoration"></div>
    </div>

    <?php
    $status_colors = [
        'In Store' => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'icon' => 'fa-warehouse'],
        'Assigned' => ['bg' => '#ecfdf5', 'text' => '#047857', 'icon' => 'fa-user-check'],
        'Repair' => ['bg' => '#fffbeb', 'text' => '#b45309', 'icon' => 'fa-screwdriver-wrench'],
        'Retired' => ['bg' => '#fef2f2', 'text' => '#b91c1c', 'icon' => 'fa-trash-can']
    ];

    foreach ($by_status as $status):
        $meta = $status_colors[$status['status']] ?? ['bg' => '#f1f5f9', 'text' => '#475569', 'icon' => 'fa-circle-dot'];
        ?>
        <div class="overview-card">
            <div class="card-icon" style="background: <?= $meta['bg'] ?>; color: <?= $meta['text'] ?>;">
                <i class="fa-solid <?= $meta['icon'] ?>"></i>
            </div>
            <div class="card-data">
                <h3 class="stat-number"><?= $status['count'] ?></h3>
                <p class="stat-label"><?= $status['status'] ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="chart-layout-grid inventory">
    <div class="chart-container-card">
        <div class="card-header-flex">
            <h4><i class="fa-solid fa-chart-pie"></i> Asset Distribution by Type</h4>
        </div>
        <div class="chart-body">
            <canvas id="typePieChart"></canvas>
        </div>
    </div>

    <div class="chart-container-card">
        <div class="card-header-flex">
            <h4><i class="fa-solid fa-chart-simple"></i> Assets by Department</h4>
        </div>
        <div class="chart-body">
            <canvas id="deptBarChart"></canvas>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Asset Type Pie Chart
        const typeCtx = document.getElementById('typePieChart').getContext('2d');
        const typeData = <?= json_encode($by_type) ?>;

        new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: typeData.map(d => d.type),
                datasets: [{
                    data: typeData.map(d => d.count),
                    backgroundColor: [
                        '#6366f1', '#a855f7', '#ec4899', '#f43f5e', '#f97316', '#eab308'
                    ],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: "'Inter', sans-serif", weight: '600' }
                        }
                    }
                },
                cutout: '70%'
            }
        });

        // 2. Department Bar Chart
        const deptCtx = document.getElementById('deptBarChart').getContext('2d');
        const deptData = <?= json_encode($by_dept) ?>;

        new Chart(deptCtx, {
            type: 'bar',
            data: {
                labels: deptData.map(d => d.department),
                datasets: [{
                    label: 'Hardware Count',
                    data: deptData.map(d => d.count),
                    backgroundColor: '#6366f1',
                    borderRadius: 8,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: { font: { weight: '600' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: '600' } }
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>