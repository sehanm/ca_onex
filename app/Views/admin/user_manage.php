<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Staff Directory<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="staff-page-container">
    <div class="page-header-premium">
        <div class="header-main">
            <h1>Staff & User Directory</h1>
            <p>Manage organizational access and digital identities.</p>
        </div>
        <div class="header-actions">
            <a href="<?= site_url('admin/users/create') ?>" class="btn-create-staff">
                <i class="fa-solid fa-user-plus"></i> Register New Staff
            </a>
        </div>
    </div>

    <div class="card shadow-premium staff-list-card">
        <div class="table-responsive">
            <table class="table datatable datatable-premium">
                <thead>
                    <tr>
                        <th>Identity</th>
                        <th>Access Credentials</th>
                        <th>Organization</th>
                        <th class="text-end">Command</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="staff-identity-cell">
                                    <div class="avatar-circle">
                                        <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                                    </div>
                                    <div class="staff-details">
                                        <div class="staff-name"><?= esc($user['full_name']) ?></div>
                                        <div class="staff-username">@<?= esc($user['username']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="access-info">
                                    <span class="email-pill">
                                        <i class="fa-solid fa-envelope"></i> <?= esc($user['email']) ?>
                                    </span>
                                    <span class="role-pill">
                                        <i class="fa-solid fa-shield-halved"></i> <?= esc($user['system_role']) ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="org-info">
                                    <span
                                        class="dept-text"><?= esc($user['department_name'] ?: 'External/Unassigned') ?></span>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="action-stack">
                                    <a href="<?= site_url('admin/users/edit/' . $user['id']) ?>" class="btn-action edit"
                                        title="Modify Profile">
                                        <i class="fa-solid fa-user-pen"></i>
                                    </a>
                                    <button type="button" class="btn-action delete"
                                        onclick="confirmDeletion('<?= site_url('admin/users/delete/' . $user['id']) ?>', 'Terminate Access?', 'This user will immediately lose all system access and their directory record will be archived.')"
                                        title="Terminate Access">
                                        <i class="fa-solid fa-user-xmark"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .staff-page-container {
        padding: 10px;
    }

    .page-header-premium {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        gap: 20px;
    }

    .header-main h1 {
        font-size: 1.8rem;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .header-main p {
        color: #64748b;
        margin: 5px 0 0 0;
    }

    .btn-create-staff {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }

    .btn-create-staff:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        color: white;
    }

    .staff-list-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        padding: 25px;
        overflow: hidden;
    }

    /* Premium Table Refinement */
    .datatable-premium {
        border-collapse: separate !important;
        border-spacing: 0 12px !important;
        width: 100% !important;
    }

    .datatable-premium thead th {
        background: transparent;
        border: none;
        color: #94a3b8;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
        padding: 10px 20px;
    }

    .datatable-premium tbody tr {
        background: white;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }

    .datatable-premium tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    .datatable-premium tbody td {
        padding: 20px !important;
        border: none !important;
        vertical-align: middle;
    }

    .datatable-premium tbody tr td:first-child {
        border-radius: 16px 0 0 16px;
    }

    .datatable-premium tbody tr td:last-child {
        border-radius: 0 16px 16px 0;
    }

    /* Identity Cell */
    .staff-identity-cell {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .avatar-circle {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #800000 0%, #a00000 100%);
        color: white;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.2rem;
        box-shadow: 0 4px 12px rgba(128, 0, 0, 0.2);
    }

    .staff-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 1rem;
    }

    .staff-username {
        color: #94a3b8;
        font-size: 0.8rem;
        font-weight: 500;
    }

    /* Access Pill */
    .access-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .email-pill {
        color: #475569;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .email-pill i {
        color: #94a3b8;
    }

    .role-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        color: #475569;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        width: fit-content;
    }

    .role-pill i {
        color: #800000;
    }

    /* Org Info */
    .dept-text {
        color: #64748b;
        font-weight: 600;
        font-size: 0.85rem;
    }

    /* Actions */
    .action-stack {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .btn-action {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        border: none;
        transition: all 0.2s;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-action.edit {
        background: #f1f5f9;
        color: #64748b;
    }

    .btn-action.edit:hover {
        background: #e2e8f0;
        color: #1e293b;
        transform: translateY(-2px);
    }

    .btn-action.delete {
        background: #fff1f2;
        color: #e11d48;
    }

    .btn-action.delete:hover {
        background: #e11d48;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3);
    }

    /* Hidden elements on mobile */
    @media (max-width: 768px) {
        .avatar-circle {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }

        .page-header-premium {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .header-actions {
            width: 100%;
        }

        .btn-create-staff {
            justify-content: center;
            width: 100%;
        }

        .staff-list-card {
            padding: 15px 10px;
        }

        .staff-name {
            font-size: 0.85rem;
        }

        .staff-username {
            font-size: 0.7rem;
        }

        .role-pill {
            padding: 4px 8px;
            font-size: 0.65rem;
        }

        /* DataTables tweaks */
        .dataTables_filter input {
            width: 100% !important;
            margin-left: 0 !important;
            margin-top: 5px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Local scripts removed as functionality moved to layout
</script>
<?= $this->endSection() ?>