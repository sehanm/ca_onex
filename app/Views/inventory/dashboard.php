<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Inventory Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-premium">
    <div class="header-main">
        <h2 class="title-gradient" style="padding: 20px;">Inventory Control Tower</h2>
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

<div class="chart-layout-grid">
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

<style>
    /* Premium Dashboard Styles */
    .title-gradient {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 850;
        font-size: 1.8rem;
        letter-spacing: -0.5px;
    }

    .subtitle {
        color: #64748b;
        font-size: 0.95rem;
        margin-top: 4px;
    }

    .inventory-stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .overview-card {
        background: white;
        padding: 24px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
        border: 1px solid #f1f5f9;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
    }

    .overview-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.05);
    }

    .overview-card.total {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
        border: none;
    }

    .overview-card.total .stat-number {
        color: white;
    }

    .overview-card.total .stat-label {
        color: #94a3b8;
    }

    .overview-card.total .card-icon {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .card-icon {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .stat-number {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    .stat-label {
        margin: 2px 0 0 0;
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .chart-layout-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 25px;
    }

    .chart-container-card {
        background: white;
        border-radius: 24px;
        padding: 30px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .card-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .card-header-flex h4 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-header-flex h4 i {
        color: #6366f1;
    }

    .chart-body {
        position: relative;
        height: 320px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 1200px) {
        .chart-layout-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Buttons */
    .btn-premium {
        background: #1e293b;
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s;
    }

    .btn-premium:hover {
        background: #0f172a;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .btn-premium-outline {
        background: white;
        color: #475569;
        padding: 12px 24px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }

    .btn-premium-outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateY(-2px);
    }
</style>

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