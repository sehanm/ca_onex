<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Initiate Hardware Request<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function toggleDueDate(show) {
        const wrapper = document.getElementById('dueDateWrapper');
        const input = document.getElementById('due_date');
        if (!wrapper || !input) return;

        if (show) {
            wrapper.classList.remove('d-none');
            input.required = true;
        } else {
            wrapper.classList.add('d-none');
            input.required = false;
            input.value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const tempRadio = document.getElementById('temporary');
        if (tempRadio) {
            toggleDueDate(tempRadio.checked);
        }
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <a href="<?= base_url('hardware-requests') ?>" class="btn-secondary">View History</a>
</div>

<div class="card form-card">
    <div class="card-body">
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger border-0 bg-danger-subtle text-danger rounded-3 mb-4 small">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> <strong>Validation required:</strong>
                <ul class="mb-0 mt-1 ps-4">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('hardware-requests/store') ?>" method="post" autocomplete="off">
            <?= csrf_field() ?>

            <div class="row mb-4">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="item_type">Item Category</label>
                        <select name="item_type" id="item_type" class="form-control">
                            <option value="accessory" <?= old('item_type') == 'accessory' ? 'selected' : '' ?>>Accessories
                            </option>
                            <option value="asset" <?= old('item_type') == 'asset' ? 'selected' : '' ?>>Asset</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="form-group">
                        <label for="category">Model / Description</label>
                        <input type="text" name="category" id="category" class="form-control"
                            value="<?= old('category') ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-group mb-4">
                <label class="d-block mb-3 fw-600">Requirement Type</label>
                <div class="d-flex gap-4 mt-2">
                    <div class="lifecycle-option">
                        <input type="radio" name="requirement_type" id="permanent" value="permanent"
                            <?= old('requirement_type', 'permanent') === 'permanent' ? 'checked' : '' ?>
                            onchange="toggleDueDate(false)">
                        <label for="permanent" class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-infinity text-muted"></i>
                            <div>
                                <span class="d-block fw-bold">Permanent</span>
                            </div>
                        </label>
                    </div>
                    <div class="lifecycle-option">
                        <input type="radio" name="requirement_type" id="temporary" value="temporary"
                            <?= old('requirement_type') === 'temporary' ? 'checked' : '' ?> onchange="toggleDueDate(true)">
                        <label for="temporary" class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-hourglass-half text-muted"></i>
                            <div>
                                <span class="d-block fw-bold">Temporary</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div id="dueDateWrapper"
                class="form-group mb-4 <?= old('requirement_type') === 'temporary' ? '' : 'd-none' ?>">
                <label for="due_date">Return Due Date</label>
                <input type="date" name="due_date" id="due_date" class="form-control" value="<?= old('due_date') ?>"
                    <?= old('requirement_type') === 'temporary' ? 'required' : '' ?>>
            </div>

            <div class="form-group mb-4">
                <label for="reason">Reason For Request</label>
                <textarea name="reason" id="reason" class="form-control" rows="3"
                    required><?= old('reason') ?></textarea>
            </div>

            <div class="form-actions mt-5">
                <button type="submit" class="btn-primary w-100 py-3 rounded-3 shadow-sm">
                    SEND REQUISITION <i class="fa-solid fa-paper-plane ms-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .d-none {
        display: none !important;
    }

    .form-card {
        max-width: 850px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .form-group label,
    .fw-600 {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: border-color 0.2s;
    }

    .form-control:focus {
        border-color: #800000;
        outline: none;
    }

    .lifecycle-option {
        position: relative;
    }

    .lifecycle-option input[type="radio"] {
        display: none;
    }

    .lifecycle-option label {
        padding: 15px 25px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        min-width: 200px;
    }

    .lifecycle-option input[type="radio"]:checked+label {
        border-color: #800000;
        background-color: rgba(128, 0, 0, 0.03);
    }

    .lifecycle-option input[type="radio"]:checked+label i {
        color: #800000 !important;
    }

    .btn-primary {
        background-color: #800000;
        border: none;
        color: white;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .btn-primary:hover {
        background-color: #660000;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: white;
        border: 1px solid #d1d5db;
        color: #374151;
    }

    .btn-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: #f3f4f6;
        color: #4b5563;
        transition: all 0.2s;
        text-decoration: none;
        font-size: 1.25rem;
    }

    .btn-icon:hover {
        background-color: #e5e7eb;
        color: #111827;
    }
</style>
<?= $this->endSection() ?>