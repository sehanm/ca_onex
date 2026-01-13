<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="welcome-banner">
        <h1>Welcome back, <?= esc($full_name) ?>!</h1>
        <p>Here's what's happening today.</p>
    </div>

    <!-- Quick Stats Cards (Placeholder) -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon stats-blue">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="stat-details">
                <h3>Total Employees</h3>
                <p>124</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stats-green">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div class="stat-details">
                <h3>New Onboarding</h3>
                <p>5</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stats-red">
                <i class="fa-solid fa-user-minus"></i>
            </div>
            <div class="stat-details">
                <h3>Pending Exits</h3>
                <p>2</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon stats-orange">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div class="stat-details">
                <h3>Notifications</h3>
                <p>8</p>
            </div>
        </div>
    </div>

    <div class="content-section">
        <h2>Quick Actions</h2>
        <!-- Future content -->
    </div>
<?= $this->endSection() ?>
