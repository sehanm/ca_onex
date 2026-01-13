<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - CA OnEx</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
</head>
<body>
    
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="brand-logo">
                    <img src="<?= base_url('assets/img/logo_icon.jpg') ?>" alt="Logo">
                    <span>CA OnEx</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="<?= uri_string() == 'dashboard' ? 'active' : '' ?>">
                        <a href="<?= base_url('dashboard') ?>">
                            <i class="fa-solid fa-chart-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <!-- Add more dynamic menu items based on role later -->
                    <li>
                        <a href="#">
                            <i class="fa-solid fa-users"></i>
                            <span>Employees</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa-solid fa-building"></i>
                            <span>Departments</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa-solid fa-file-contract"></i>
                            <span>Clearance</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="<?= base_url('logout') ?>" class="logout-link">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="main-wrapper">
            <!-- Topbar -->
            <header class="topbar">
                <div class="toggle-sidebar">
                    <i class="fa-solid fa-bars"></i>
                </div>
                
                <div class="user-profile">
                    <div class="user-info">
                        <span class="user-name"><?= session()->get('full_name') ?></span>
                        <span class="user-role"><?= session()->get('role') ?></span>
                    </div>
                    <div class="user-avatar">
                        <?= strtoupper(substr(session()->get('full_name'), 0, 1)) ?>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="content-area">
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <script src="<?= base_url('assets/js/dashboard.js') ?>"></script>
</body>
</html>
