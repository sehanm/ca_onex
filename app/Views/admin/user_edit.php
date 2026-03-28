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

        <div class="row" style="margin-top: 15px; margin-bottom: 20px;">
            <div class="col">
                <div class="form-group role-selection-group">
                    <label class="group-label">System Role Access</label>
                    <div class="checkbox-grid">
                        <?php foreach($roles as $role): ?>
                            <label class="checkbox-pill">
                                <input type="checkbox" name="system_role[]" value="<?= $role['id'] ?>" <?= in_array($role['id'], $user['role_ids'] ?? []) ? 'checked' : '' ?>>
                                <span class="pill-btn">
                                    <i class="fa-solid fa-shield-halved"></i> <?= esc($role['role_name']) ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group role-selection-group">
                    <label class="group-label">Divisional Role Access</label>
                    <div class="checkbox-grid">
                        <?php foreach($div_roles as $role): ?>
                            <label class="checkbox-pill">
                                <input type="checkbox" name="divisional_role[]" value="<?= $role['id'] ?>" <?= in_array($role['id'], $user['role_ids'] ?? []) ? 'checked' : '' ?>>
                                <span class="pill-btn">
                                    <i class="fa-solid fa-briefcase"></i> <?= esc($role['role_name']) ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
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

        <button type="submit" class="btn-primary" style="margin-top: 20px;">Update User</button>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .role-selection-group {
        background: #f8fafc;
        padding: 20px;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        height: 100%;
    }

    .group-label {
        font-weight: 800;
        color: #1e293b;
        font-size: 0.9rem;
        margin-bottom: 15px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .checkbox-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .checkbox-pill {
        cursor: pointer;
        position: relative;
    }

    .checkbox-pill input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .pill-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: white;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #64748b;
        transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        user-select: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .pill-btn i {
        color: #94a3b8;
        transition: all 0.2s;
    }

    .checkbox-pill:hover .pill-btn {
        border-color: #6366f1;
        color: #6366f1;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
    }

    .checkbox-pill input:checked + .pill-btn {
        background: #6366f1;
        border-color: #6366f1;
        color: white;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }

    .checkbox-pill input:checked + .pill-btn i {
        color: white;
    }

    @media (max-width: 768px) {
        .checkbox-grid {
            flex-direction: column;
        }
        .pill-btn {
            width: 100%;
        }
    }
</style>
<?= $this->endSection() ?>
