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
            <table class="table datatable-premium">
                <thead>
                    <tr>
                        <th>Personnel</th>
                        <th class="hide-mobile">Corporate Email</th>
                        <th>Classification</th>
                        <th class="hide-tablet">Department</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="staff-profile-cell">
                                    <div class="avatar-circle">
                                        <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                                    </div>
                                    <div class="staff-info">
                                        <span class="staff-name"><?= esc($user['full_name']) ?></span>
                                        <span class="staff-username">@<?= esc($user['username']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="hide-mobile">
                                <a href="mailto:<?= esc($user['email']) ?>" class="email-link">
                                    <?= esc($user['email']) ?>
                                </a>
                            </td>
                            <td>
                                <?php
                                $roleClass = 'role-default';
                                if ($user['system_role'] == 'Super Admin')
                                    $roleClass = 'role-admin';
                                if ($user['system_role'] == 'HR Admin')
                                    $roleClass = 'role-hr';
                                if ($user['system_role'] == 'ICT Admin')
                                    $roleClass = 'role-ict';
                                ?>
                                <span class="role-pill <?= $roleClass ?>">
                                    <?= esc($user['system_role']) ?>
                                </span>
                            </td>
                            <td class="hide-tablet">
                                <div class="dept-label">
                                    <i class="fa-solid fa-building"></i>
                                    <?= esc($user['department_name'] ?? 'General') ?>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="staff-actions">
                                    <a href="<?= site_url('admin/users/edit/' . $user['id']) ?>" class="btn-action edit"
                                        title="Edit Profile">
                                        <i class="fa-solid fa-user-pen"></i>
                                    </a>
                                    <button type="button" class="btn-action delete"
                                        onclick="confirmDelete('<?= site_url('admin/users/delete/' . $user['id']) ?>')"
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

    .staff-profile-cell {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .avatar-circle {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #6366f1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #eef2ff;
    }

    .staff-name {
        display: block;
        font-weight: 700;
        color: #1e293b;
        font-size: 0.95rem;
    }

    .staff-username {
        font-size: 0.8rem;
        color: #94a3b8;
        font-weight: 500;
    }

    .email-link {
        color: #64748b;
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.2s;
    }

    .email-link:hover {
        color: #6366f1;
    }

    .role-pill {
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .role-admin {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fee2e2;
    }

    .role-hr {
        background: #fdf2f8;
        color: #db2777;
        border: 1px solid #fce7f3;
    }

    .role-ict {
        background: #f5f3ff;
        color: #7c3aed;
        border: 1px solid #ede9fe;
    }

    .role-default {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #f1f5f9;
    }

    .dept-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #64748b;
        font-size: 0.85rem;
    }

    .dept-label i {
        color: #cbd5e1;
    }

    .user-qr-wrapper {
        position: relative;
        width: 44px;
        height: 44px;
        margin: 0 auto;
        padding: 4px;
        background: white;
        border: 1px solid #f1f5f9;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .user-qr-thumb {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .qr-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transition: 0.2s;
        font-size: 0.9rem;
    }

    .user-qr-wrapper:hover {
        transform: scale(1.15) rotate(5deg);
        border-color: #6366f1;
    }

    .user-qr-wrapper:hover .qr-overlay {
        opacity: 1;
    }

    .staff-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
        cursor: pointer;
        background: white;
    }

    .btn-action.edit {
        color: #64748b;
        text-decoration: none;
    }

    .btn-action.edit:hover {
        background: #f8fafc;
        color: #6366f1;
        border-color: #6366f1;
        transform: translateY(-2px);
    }

    .btn-action.delete {
        color: #f43f5e;
        border: none;
    }

    .btn-action.delete:hover {
        background: #fff1f2;
        color: #e11d48;
        transform: translateY(-2px);
    }

    /* Responsiveness */
    @media (max-width: 992px) {
        .hide-tablet {
            display: none !important;
        }
    }

    @media (max-width: 576px) {
        .hide-mobile {
            display: none !important;
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

        .avatar-circle {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
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

        .user-qr-wrapper {
            width: 36px;
            height: 36px;
            padding: 2px;
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

    function confirmDelete(url) {
        if (confirm('Are you sure you want to terminate access? This user will lose all system access and their directory record will be archived.')) {
            window.location.href = url;
        }
    }
</script>
<?= $this->endSection() ?>