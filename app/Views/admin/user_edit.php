<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Edit User<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h2>Edit User: <?= esc($user['username']) ?></h2>
    <a href="<?= base_url('admin/users') ?>" class="btn-secondary">Back</a>
</div>

<div class="card form-card">
    <form action="<?= base_url('admin/users/update/'.$user['id']) ?>" method="POST">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="<?= esc($user['username']) ?>" readonly style="background-color: var(--gray-100);">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Password (Leave blank to keep current)</label>
                    <input type="password" name="password" class="form-control" placeholder="New Password">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?= esc($user['full_name']) ?>" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>EPF Number</label>
                    <input type="text" name="epf_number" class="form-control" value="<?= esc($user['epf_number']) ?>" required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= esc($user['email']) ?>" required>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label>System Role</label>
                    <select name="system_role" class="form-control" required>
                        <?php foreach($roles as $role): ?>
                        <option value="<?= $role['id'] ?>" <?= $role['id'] == $user['system_role_id'] ? 'selected' : '' ?>><?= $role['role_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Divisional Role</label>
                    <select name="divisional_role" class="form-control">
                        <?php foreach($div_roles as $role): ?>
                        <option value="<?= $role['id'] ?>" <?= $role['id'] == $user['divisional_role_id'] ? 'selected' : '' ?>><?= $role['role_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Department</label>
            <select name="department" class="form-control">
                <option value="">Select Department</option>
                <?php foreach($departments as $dept): ?>
                <option value="<?= $dept['id'] ?>" <?= $dept['id'] == $user['department_id'] ? 'selected' : '' ?>><?= $dept['department_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn-primary">Update User</button>
    </form>
</div>
<?= $this->endSection() ?>
