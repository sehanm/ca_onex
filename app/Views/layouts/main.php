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
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>?v=<?= time() ?>">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <?= $this->renderSection('styles') ?>
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
                    <li
                        class="<?= uri_string() == 'hardware-requests' || uri_string() == 'hardware-requests/create' ? 'active' : '' ?>">
                        <a href="<?= base_url('hardware-requests') ?>">
                            <i class="fa-solid fa-laptop-medical"></i>
                            <span>My Requests</span>
                        </a>
                    </li>

                    <?php
                    $isAdminOpen = (uri_string() == 'admin/users' || uri_string() == 'admin/audit-logs' || uri_string() == 'admin/departments');
                    ?>
                    <li class="has-submenu <?= $isAdminOpen ? 'open' : '' ?>">
                        <a href="javascript:void(0)" class="submenu-toggle">
                            <span class="menu-label">
                                <i class="fa-solid fa-gears"></i>
                                <span>Administration</span>
                            </span>
                            <i class="fa-solid fa-chevron-down arrow"></i>
                        </a>
                        <ul class="submenu">
                            <?php 
                            $userRoles = (array)(session()->get('roles') ?: [session()->get('role')]);
                            if (in_array('Super Admin', $userRoles)): 
                            ?>
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
                            <li class="<?= uri_string() == 'admin/departments' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/departments') ?>">
                                    <i class="fa-solid fa-building"></i>
                                    <span>Departments</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <?php
                    $managedDepts = (new \App\Models\DepartmentModel())->where('manager_id', session()->get('id'))->findAll();
                    $isHod = count($managedDepts) > 0;

                    $facilitatorRoles = [];
                    foreach ($managedDepts as $dept) {
                        if ($dept['department_name'] == 'Administration & Events')
                            $facilitatorRoles[] = 'Admin';
                        if ($dept['department_name'] == 'HR')
                            $facilitatorRoles[] = 'HR';
                        if ($dept['department_name'] == 'ICT')
                            $facilitatorRoles[] = 'ICT';
                    }

                    $isOnboardingOpen = (uri_string() == 'onboarding/create' || uri_string() == 'onboarding/pending' || uri_string() == 'onboarding/facility-tasks');
                    ?>

                    <li class="has-submenu <?= $isOnboardingOpen ? 'open' : '' ?>">
                        <a href="javascript:void(0)" class="submenu-toggle">
                            <span class="menu-label">
                                <i class="fa-solid fa-user-check"></i>
                                <span>Onboarding</span>
                            </span>
                            <i class="fa-solid fa-chevron-down arrow"></i>
                        </a>
                        <ul class="submenu">
                            <?php if (in_array('Super Admin', $userRoles) || in_array('HR Admin', $userRoles)): ?>
                                <li class="<?= uri_string() == 'onboarding/create' ? 'active' : '' ?>">
                                    <a href="<?= base_url('onboarding/create') ?>">
                                        <i class="fa-solid fa-user-plus"></i>
                                        <span>Initiate Onboarding</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($isHod): ?>
                                <li class="<?= uri_string() == 'onboarding/pending' ? 'active' : '' ?>">
                                    <a href="<?= base_url('onboarding/pending') ?>">
                                        <i class="fa-solid fa-list-check"></i>
                                        <span>Onboarding Tasks</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($facilitatorRoles)): ?>
                                <li class="<?= uri_string() == 'onboarding/facility-tasks' ? 'active' : '' ?>">
                                    <a href="<?= base_url('onboarding/facility-tasks') ?>">
                                        <i class="fa-solid fa-screwdriver-wrench"></i>
                                        <span>Facility Tasks</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <?php if (in_array('ICT', $facilitatorRoles) || in_array('Super Admin', $userRoles)):
                        $isInventoryOpen = (strpos(uri_string(), 'inventory') !== false);
                        ?>
                        <li class="has-submenu <?= $isInventoryOpen ? 'open' : '' ?>">
                            <a href="javascript:void(0)" class="submenu-toggle">
                                <span class="menu-label">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                    <span>Inventory</span>
                                </span>
                                <i class="fa-solid fa-chevron-down arrow"></i>
                            </a>
                            <ul class="submenu">
                                <li class="<?= uri_string() == 'inventory' ? 'active' : '' ?>">
                                    <a href="<?= base_url('inventory') ?>">
                                        <i class="fa-solid fa-chart-pie"></i>
                                        <span>Overview</span>
                                    </a>
                                </li>
                                <li class="<?= uri_string() == 'inventory/items' ? 'active' : '' ?>">
                                    <a href="<?= base_url('inventory/items') ?>">
                                        <i class="fa-solid fa-list-ul"></i>
                                        <span>Asset List</span>
                                    </a>
                                </li>
                                <li class="<?= uri_string() == 'inventory/scan' ? 'active' : '' ?>">
                                    <a href="<?= base_url('inventory/scan') ?>">
                                        <i class="fa-solid fa-qrcode"></i>
                                        <span>Scan Assets</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <?php $isAccOpen = (strpos(uri_string(), 'accessories') !== false); ?>
                        <li class="has-submenu <?= $isAccOpen ? 'open' : '' ?>">
                            <a href="javascript:void(0)" class="submenu-toggle">
                                <span class="menu-label">
                                    <i class="fa-solid fa-keyboard"></i>
                                    <span>Accessories</span>
                                </span>
                                <i class="fa-solid fa-chevron-down arrow"></i>
                            </a>
                            <ul class="submenu">
                                <li class="<?= uri_string() == 'accessories' ? 'active' : '' ?>">
                                    <a href="<?= base_url('accessories') ?>">
                                        <i class="fa-solid fa-chart-line"></i>
                                        <span>Overview</span>
                                    </a>
                                </li>
                                <li class="<?= uri_string() == 'accessories/items' ? 'active' : '' ?>">
                                    <a href="<?= base_url('accessories/items') ?>">
                                        <i class="fa-solid fa-layer-group"></i>
                                        <span>Stock Management</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <?php $isReqOpen = (strpos(uri_string(), 'hardware-requests/manage') !== false || strpos(uri_string(), 'hardware-requests/scanner') !== false); ?>
                        <li class="has-submenu <?= $isReqOpen ? 'open' : '' ?>">
                            <a href="javascript:void(0)" class="submenu-toggle">
                                <span class="menu-label">
                                    <i class="fa-solid fa-file-signature"></i>
                                    <span>Request Fulfillment</span>
                                </span>
                                <i class="fa-solid fa-chevron-down arrow"></i>
                            </a>
                            <ul class="submenu">
                                <li class="<?= uri_string() == 'hardware-requests/overview' ? 'active' : '' ?>">
                                    <a href="<?= base_url('hardware-requests/overview') ?>">
                                        <i class="fa-solid fa-chart-pie"></i>
                                        <span>Overview</span>
                                    </a>
                                </li>
                                <li class="<?= uri_string() == 'hardware-requests/manage' ? 'active' : '' ?>">
                                    <a href="<?= base_url('hardware-requests/manage') ?>">
                                        <i class="fa-solid fa-list-check"></i>
                                        <span>Manage Tasks</span>
                                    </a>
                                </li>
                                <li class="<?= uri_string() == 'hardware-requests/scanner' ? 'active' : '' ?>">
                                    <a href="<?= base_url('hardware-requests/scanner') ?>">
                                        <i class="fa-solid fa-qrcode"></i>
                                        <span>Scanner (Assign/Return)</span>
                                    </a>
                                </li>
                            </ul>
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
                        <span class="user-role"><?= esc(is_array(session()->get('roles')) ? implode(', ', session()->get('roles')) : session()->get('role')) ?></span>
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

    <!-- Global Premium Delete Modal -->
    <div id="globalDeleteModal" class="premium-modal">
        <div class="modal-overlay" onclick="closeGlobalDeleteModal()"></div>
        <div class="modal-content-wrapper">
            <div class="modal-icon-header">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3 id="globalDeleteTitle">Confirm Action</h3>
            <p id="globalDeleteText">Are you sure you want to proceed with this deletion? This action cannot be
                reversed.</p>
            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeGlobalDeleteModal()">Cancel</button>
                <a id="globalDeleteConfirmBtn" href="#" class="btn-modal-confirm">Delete Permanently</a>
            </div>
        </div>
    </div>

    <!-- Premium Modal Styles -->
    <style>
        .premium-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .premium-modal.active {
            display: flex;
        }

        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease;
        }

        .modal-content-wrapper {
            background: white;
            width: 100%;
            max-width: 400px;
            border-radius: 24px;
            padding: 40px 30px;
            position: relative;
            z-index: 10001;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            animation: scaleIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .modal-icon-header {
            width: 70px;
            height: 70px;
            background: #fff1f2;
            color: #e11d48;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 25px;
            transform: rotate(-5deg);
        }

        .modal-content-wrapper h3 {
            font-size: 1.4rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 12px;
        }

        .modal-content-wrapper p {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .modal-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-modal-confirm {
            background: #e11d48;
            color: white;
            padding: 14px;
            border-radius: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-modal-confirm:hover {
            background: #be123c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(225, 29, 72, 0.3);
            color: white;
        }

        .btn-modal-cancel {
            background: #f1f5f9;
            color: #475569;
            padding: 14px;
            border-radius: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-modal-cancel:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(20px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
    </style>

    <!-- jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="<?= base_url('assets/js/dashboard.js') ?>"></script>
    <script src="<?= base_url('assets/js/toast.js') ?>"></script>
    <script>
        // Global Confirmation Function
        function confirmDeletion(url, title = "Confirm Deletion", text = "Are you sure you want to proceed? This action cannot be reversed.") {
            const modal = document.getElementById('globalDeleteModal');
            document.getElementById('globalDeleteTitle').innerText = title;
            document.getElementById('globalDeleteText').innerText = text;
            document.getElementById('globalDeleteConfirmBtn').href = url;

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeGlobalDeleteModal() {
            const modal = document.getElementById('globalDeleteModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Initialize DataTables
        $(document).ready(function () {
            $('.datatable').DataTable({
                "pageLength": 10,
                "lengthChange": false,
                "language": {
                    "search": "Filter records:"
                }
            });

            // Handle Flash Messages with Toasts
            <?php if (session()->getFlashdata('success')): ?>
                showToast("<?= esc(session()->getFlashdata('success')) ?>", 'success');
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                showToast("<?= esc(session()->getFlashdata('error')) ?>", 'error');
            <?php endif; ?>
            <?php if (session()->getFlashdata('msg')): ?>
                showToast("<?= esc(session()->getFlashdata('msg')) ?>", 'info');
            <?php endif; ?>
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>