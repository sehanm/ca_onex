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
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
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
                    <?php if (session()->get('role') === 'Super Admin'): ?>
                    <li class="<?= uri_string() == 'admin/users' ? 'active' : '' ?>">
                        <a href="<?= base_url('admin/users') ?>">
                            <i class="fa-solid fa-users-gear"></i>
                            <span>User Management</span>
                        </a>
                    </li>
                    <li class="<?= uri_string() == 'admin/audit-logs' ? 'active' : '' ?>">
                        <a href="<?= base_url('admin/audit-logs') ?>">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span>Audit Logs</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <li>
                        <a href="#">
                            <i class="fa-solid fa-users"></i>
                            <span>Employees</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/departments') ?>">
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
                    <?php if (session()->get('role') === 'Super Admin' || session()->get('role') === 'HR Admin'): ?>
                    <li class="<?= uri_string() == 'onboarding/create' ? 'active' : '' ?>">
                        <a href="<?= base_url('onboarding/create') ?>">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Initiate Onboarding</span>
                        </a>
                    </li>
                    <?php endif; ?>
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

    <!-- jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url('assets/js/dashboard.js') ?>"></script>
    <script src="<?= base_url('assets/js/toast.js') ?>"></script>
    <script>
        <?php if (session()->getFlashdata('error')): ?>
            showToast("<?= esc(session()->getFlashdata('error')) ?>", 'error');
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            showToast("<?= esc(session()->getFlashdata('success')) ?>", 'success');
        <?php endif; ?>
        <?php if (session()->getFlashdata('msg')): ?>
            showToast("<?= esc(session()->getFlashdata('msg')) ?>", 'info');
        <?php endif; ?>

        // Initialize DataTables
        $(document).ready(function() {
            $('.datatable').DataTable({
                "pageLength": 10,
                "lengthChange": false,
                "language": {
                    "search": "Filter records:"
                }
            });
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
