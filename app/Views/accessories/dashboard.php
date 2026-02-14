<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Accessories Overview
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-premium">
    <div class="header-main">
        <h2 class="title-gradient">ICT Accessories Control</h2>
        <p class="subtitle">Managing stock, deployment, and health of peripheral hardware.</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('accessories/items') ?>" class="btn-premium">
            <i class="fa-solid fa-list-check"></i> Manage Inventory
        </a>
    </div>
</div>

<div class="inventory-stats-row">
    <div class="overview-card total">
        <div class="card-icon"><i class="fa-solid fa-keyboard"></i></div>
        <div class="card-data">
            <h3 class="stat-number">
                <?= $total ?>
            </h3>
            <p class="stat-label">Total Units</p>
        </div>
    </div>

    <?php
    $status_meta = [
        'Stock' => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'icon' => 'fa-box-open', 'label' => 'In ICT Stock'],
        'Assigned' => ['bg' => '#ecfdf5', 'text' => '#047857', 'icon' => 'fa-user-tag', 'label' => 'In Use'],
        'Damaged' => ['bg' => '#fef2f2', 'text' => '#b91c1c', 'icon' => 'fa-burst', 'label' => 'Damaged/Faulty']
    ];

    $found_statuses = [];
    foreach ($by_status as $s)
        $found_statuses[$s['status']] = $s['count'];

    foreach (['Stock', 'Assigned', 'Damaged'] as $status_key):
        $count = $found_statuses[$status_key] ?? 0;
        $meta = $status_meta[$status_key];
        ?>
        <div class="overview-card">
            <div class="card-icon" style="background: <?= $meta['bg'] ?>; color: <?= $meta['text'] ?>;">
                <i class="fa-solid <?= $meta['icon'] ?>"></i>
            </div>
            <div class="card-data">
                <h3 class="stat-number">
                    <?= $count ?>
                </h3>
                <p class="stat-label">
                    <?= $meta['label'] ?>
                </p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="chart-layout-grid">
    <div class="chart-container-card">
        <div class="card-header-flex">
            <h4><i class="fa-solid fa-layer-group"></i> Category Distribution</h4>
        </div>
        <div class="chart-body">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>

    <div class="chart-container-card">
        <div class="card-header-flex">
            <h4><i class="fa-solid fa-heart-pulse"></i> Operational Status</h4>
        </div>
        <div class="chart-body">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Category Pie Chart
        const catCtx = document.getElementById('categoryChart').getContext('2d');
        const catData = <?= json_encode($by_category) ?>;

        new Chart(catCtx, {
            type: 'pie',
            data: {
                labels: catData.map(d => d.category),
                datasets: [{
                    data: catData.map(d => d.count),
                    backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right' } }
            }
        });

        // Status Doughnut Chart
        const statCtx = document.getElementById('statusChart').getContext('2d');
        const statData = <?= json_encode($by_status) ?>;
        const statusColors = { 'Stock': '#3b82f6', 'Assigned': '#10b981', 'Damaged': '#ef4444' };

        new Chart(statCtx, {
            type: 'doughnut',
            data: {
                labels: statData.map(d => d.status),
                datasets: [{
                    data: statData.map(d => d.count),
                    backgroundColor: statData.map(d => statusColors[d.status] || '#cbd5e1'),
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