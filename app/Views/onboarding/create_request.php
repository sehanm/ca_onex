<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Initiate Onboarding<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h2>Initiate Onboarding Process</h2>
    <a href="<?= base_url('dashboard') ?>" class="btn-secondary">Back to Dashboard</a>
</div>

<div class="card form-card">
    <form action="<?= base_url('onboarding/store') ?>" method="post">
        <?= csrf_field() ?>
        
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="candidate_name">Candidate Name</label>
                    <input type="text" name="candidate_name" id="candidate_name" class="form-control" required value="<?= old('candidate_name') ?>">
                </div>
            </div>
            <div class="col">
                 <div class="form-group">
                    <label for="designation">Designation</label>
                    <input type="text" name="designation" id="designation" class="form-control" required value="<?= old('designation') ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="department_id">Department</label>
                    <select name="department_id" id="department_id" class="form-control" required>
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= old('department_id') == $dept['id'] ? 'selected' : '' ?>>
                                <?= esc($dept['department_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col">
                 <div class="form-group">
                    <label for="hod_user_id">Assign HOD/Manager</label>
                    <select name="hod_user_id" id="hod_user_id" class="form-control" required>
                        <option value="">Select Manager</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= old('hod_user_id') == $user['id'] ? 'selected' : '' ?>>
                                <?= esc($user['full_name']) ?> (<?= esc($user['role_name']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Select the HOD who needs to fill the facility request.</small>
                </div>
            </div>
        </div>

        <div class="form-actions" style="margin-top: 20px;">
            <button type="submit" class="btn-primary">Send Request to HOD</button>
        </div>
    </form>
</div>

<style>
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--secondary-color);
    }
    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        outline: none;
        transition: border-color 0.2s;
    }
    .form-control:focus {
        border-color: var(--primary-color);
    }
</style>
<?= $this->endSection() ?>
