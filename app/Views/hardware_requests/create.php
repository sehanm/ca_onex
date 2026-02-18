<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Request Hardware<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
        <div class="page-header-premium mb-4 text-center">
            <h2 class="title-gradient mb-1">New Hardware Request</h2>
            <p class="subtitle mb-0">Submit a request for IT assets or accessories.</p>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4 p-md-5">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger border-0 bg-danger-subtle text-danger rounded-3 mb-4">
                        <ul class="mb-0 ps-3">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('hardware-requests/store') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label for="item_type" class="form-label fw-bold text-dark small text-uppercase">Item
                            Type</label>
                        <select name="item_type" id="item_type"
                            class="form-select form-select-lg border-light bg-light">
                            <option value="accessory">Accessory (Mouse, Keyboard, Headset)</option>
                            <option value="asset">Asset (Laptop, Monitor, Desktop)</option>
                        </select>
                        <div class="form-text text-muted small mt-1">Select standard accessory or tracked asset.</div>
                    </div>

                    <div class="mb-4">
                        <label for="category" class="form-label fw-bold text-dark small text-uppercase">Category /
                            Model</label>
                        <input type="text" name="category" id="category"
                            class="form-control form-control-lg border-light bg-light"
                            placeholder="e.g., Wireless Mouse, Dell Monitor..." value="<?= old('category') ?>" required>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small text-uppercase">Requirement Type</label>
                            <div class="d-flex gap-3">
                                <div class="form-check custom-radio-card flex-fill">
                                    <input class="form-check-input" type="radio" name="requirement_type" id=" permanent"
                                        value="permanent" checked>
                                    <label
                                        class="form-check-label w-100 p-3 border rounded-3 text-center cursor-pointer"
                                        for="permanent">
                                        <i class="fa-solid fa-infinity mb-2 d-block text-primary"></i>
                                        <span class="fw-bold d-block text-dark">Permanent</span>
                                        <span class="small text-muted">Long-term assignment</span>
                                    </label>
                                </div>
                                <div class="form-check custom-radio-card flex-fill">
                                    <input class="form-check-input" type="radio" name="requirement_type" id="temporary"
                                        value="temporary">
                                    <label
                                        class="form-check-label w-100 p-3 border rounded-3 text-center cursor-pointer"
                                        for="temporary">
                                        <i class="fa-solid fa-hourglass-half mb-2 d-block text-warning"></i>
                                        <span class="fw-bold d-block text-dark">Temporary</span>
                                        <span class="small text-muted">Short-term loan</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="reason" class="form-label fw-bold text-dark small text-uppercase">Reason for
                            Request</label>
                        <textarea name="reason" id="reason" class="form-control border-light bg-light" rows="3"
                            placeholder="Briefly describe why you need this item..."
                            required><?= old('reason') ?></textarea>
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-dark btn-lg py-3 rounded-pill fw-bold shadow-sm">
                            <i class="fa-solid fa-paper-plane me-2"></i> Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="<?= base_url('hardware-requests') ?>" class="text-muted text-decoration-none small fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to My Requests
            </a>
        </div>
    </div>
</div>

<style>
    .form-control:focus,
    .form-select:focus {
        border-color: #212529;
        box-shadow: 0 0 0 0.25rem rgba(33, 37, 41, 0.1);
        background-color: white;
    }

    /* Custom Radio Card Styling */
    .custom-radio-card .form-check-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .custom-radio-card .form-check-label {
        transition: all 0.2s ease;
        background-color: white;
        border-color: #e9ecef !important;
    }

    .custom-radio-card .form-check-input:checked+.form-check-label {
        background-color: #f8f9fa;
        border-color: #212529 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .cursor-pointer {
        cursor: pointer;
    }
</style>
<?= $this->endSection() ?>