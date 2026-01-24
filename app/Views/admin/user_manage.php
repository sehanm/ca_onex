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
                        <button type="button" class="btn-icon btn-delete" onclick="confirmDelete('<?= base_url('admin/users/delete/'.$user['id']) ?>')" title="Delete"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function confirmDelete(url) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
</script>
<?= $this->endSection() ?>
