<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>User Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h2>User Management</h2>
    <a href="<?= base_url('admin/users/create') ?>" class="btn-primary">Add New User</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= esc($user['username']) ?></td>
                    <td><?= esc($user['full_name']) ?></td>
                    <td><?= esc($user['email']) ?></td>
                    <td>
                        <span class="badge badge-role"><?= esc($user['system_role']) ?></span>
                    </td>
                    <td><?= esc($user['department_name'] ?? 'N/A') ?></td>
                    <td>
                        <a href="<?= base_url('admin/users/edit/'.$user['id']) ?>" class="btn-icon" title="Edit"><i class="fa-solid fa-pen"></i></a>
                        <a href="<?= base_url('admin/users/delete/'.$user['id']) ?>" class="btn-icon btn-delete" onclick="return confirm('Are you sure you want to delete this user?');" title="Delete"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
