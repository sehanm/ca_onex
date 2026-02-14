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
                    <input type="text" name="candidate_name" id="candidate_name" class="form-control" required
                        value="<?= old('candidate_name') ?>">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="designation">Designation</label>
                    <input type="text" name="designation" id="designation" class="form-control" required
                        value="<?= old('designation') ?>">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="joining_date">Date of Join</label>
                    <input type="date" name="joining_date" id="joining_date" class="form-control" required
                        value="<?= old('joining_date') ?>">
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
                            <option value="<?= $dept['id'] ?>" data-manager-id="<?= esc($dept['manager_id']) ?>"
                                data-manager-name="<?= esc($dept['manager_name'] ?? 'Unassigned') ?>"
                                <?= old('department_id') == $dept['id'] ? 'selected' : '' ?>>
                                <?= esc($dept['department_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="manager_name_display">Assign HOD/Manager</label>
                    <input type="text" id="manager_name_display" class="form-control" readonly
                        placeholder="Auto-populated based on Department">
                    <input type="hidden" name="hod_user_id" id="hod_user_id">
                </div>
            </div>
        </div>

        <div class="form-group" style="margin-top: 25px;">
            <div class="switch-group">
                <label class="switch">
                    <input type="checkbox" name="send_email" id="sendEmailToggle" value="1" checked>
                    <span class="slider"></span>
                </label>
                <div class="switch-label">
                    <span class="switch-title">Send Notification Email</span>
                    <span class="switch-desc">Notify <strong id="hodNameLabel">the HOD</strong> about this onboarding
                        request.</span>
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

    .form-control[readonly] {
        background-color: #f3f4f6;
        color: #6b7280;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const departmentSelect = document.getElementById('department_id');
        const managerDisplay = document.getElementById('manager_name_display');
        const managerIdInput = document.getElementById('hod_user_id');

        departmentSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const managerId = selectedOption.getAttribute('data-manager-id');
            const managerName = selectedOption.getAttribute('data-manager-name');
            const hodLabel = document.getElementById('hodNameLabel');

            if (managerId) {
                managerIdInput.value = managerId;
                managerDisplay.value = managerName;
                hodLabel.innerText = managerName;
            } else {
                managerIdInput.value = "";
                managerDisplay.value = "Unassigned";
                hodLabel.innerText = "the HOD";
            }
        });
    });
</script>
<?= $this->endSection() ?>