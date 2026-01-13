<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Create User<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h2>Create New User</h2>
    <a href="<?= base_url('admin/users') ?>" class="btn-secondary">Back</a>
</div>

<div class="card form-card">
    <form action="<?= base_url('admin/users/store') ?>" method="POST">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>EPF Number</label>
                    <input type="text" name="epf_number" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label>System Role</label>
                    <select name="system_role" class="form-control" required>
                        <?php foreach($roles as $role): ?>
                        <option value="<?= $role['id'] ?>"><?= $role['role_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Divisional Role</label>
                    <select name="divisional_role" class="form-control">
                        <?php foreach($div_roles as $role): ?>
                        <option value="<?= $role['id'] ?>"><?= $role['role_name'] ?></option>
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
                <option value="<?= $dept['id'] ?>"><?= $dept['department_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn-primary">Create User</button>
    </form>
</div>
<?= $this->endSection() ?>
