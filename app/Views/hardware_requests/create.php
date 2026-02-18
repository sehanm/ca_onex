<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Initiate Hardware Request<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function toggleDueDate(show) {
        const wrapper = document.getElementById('dueDateWrapper');
        const input = document.getElementById('due_date');
        if (show) {
            wrapper.classList.remove('d-none');
            input.required = true;
            wrapper.classList.add('animate-fadeIn');
        } else {
            wrapper.classList.add('d-none');
            input.required = false;
            input.value = '';
        }
    }
</script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-7 col-md-10">
        <div class="page-header-premium mb-4 text-center">
            <h2 class="title-gradient mb-1">Asset Requisition</h2>
            <p class="subtitle mb-0">Secure organization-approved hardware for your workstation.</p>
        </div>

        <div class="card border-0 shadow-premium"
            style="border-radius: 24px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
            <div class="card-body p-4 p-md-5">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger border-0 bg-danger-subtle text-danger rounded-4 mb-4 small">
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

                    <div class="form-group-modern mb-4">
                        <label for="item_type" class="modern-label">Target Item Category</label>
                        <div class="input-with-icon">
                            <i class="fa-solid fa-layer-group"></i>
                            <select name="item_type" id="item_type" class="modern-select">
                                <option value="accessory">Standard Accessory (Peripherals)</option>
                                <option value="asset">Tracked Asset (Compute/Display)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group-modern mb-4">
                        <label for="category" class="modern-label">Specific Model / Description</label>
                        <div class="input-with-icon">
                            <i class="fa-solid fa-tag"></i>
                            <input type="text" name="category" id="category" class="modern-input"
                                placeholder="e.g. Logitech MX Master, Dell UltraSharp 27..."
                                value="<?= old('category') ?>" required>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <label class="modern-label mb-3">Service Lifecycle</label>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="custom-radio-modern">
                                        <input type="radio" name="requirement_type" id="permanent" value="permanent"
                                            checked onchange="toggleDueDate(false)">
                                        <label for="permanent" class="radio-card h-100">
                                            <div class="card-glow"></div>
                                            <i class="fa-solid fa-infinity icon"></i>
                                            <span class="label">Permanent</span>
                                            <span class="desc">Standard Assignment</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="custom-radio-modern">
                                        <input type="radio" name="requirement_type" id="temporary" value="temporary"
                                            onchange="toggleDueDate(true)">
                                        <label for="temporary" class="radio-card h-100">
                                            <div class="card-glow"></div>
                                            <i class="fa-solid fa-hourglass-half icon"></i>
                                            <span class="label">Temporary</span>
                                            <span class="desc">Short-term Loan</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern mb-4 d-none" id="dueDateWrapper">
                        <label for="due_date" class="modern-label">Expected Return Timeline</label>
                        <div class="input-with-icon">
                            <i class="fa-solid fa-calendar-check"></i>
                            <input type="date" name="due_date" id="due_date" class="modern-input">
                        </div>
                    </div>

                    <div class="form-group-modern mb-4">
                        <label for="reason" class="modern-label">Business Justification</label>
                        <div class="input-with-icon align-items-start">
                            <i class="fa-solid fa-comment-dots mt-3"></i>
                            <textarea name="reason" id="reason" class="modern-input py-3" rows="3"
                                placeholder="State the requirement for this hardware resource..."
                                required><?= old('reason') ?></textarea>
                        </div>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="btn-premium w-100 py-3 rounded-pill shadow-lg">
                            <i class="fa-solid fa-paper-plane me-2"></i> TRANSMIT REQUEST
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="<?= base_url('hardware-requests') ?>" class="btn-premium-outline btn-sm">
                <i class="fa-solid fa-arrow-left me-2"></i> REQUISITION HISTORY
            </a>
        </div>
    </div>
</div>

<style>
    /* Modern Form Styling */
    .form-group-modern {
        position: relative;
    }

    .modern-label {
        font-size: 0.75rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        display: block;
    }

    .input-with-icon {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-with-icon i {
        position: absolute;
        left: 20px;
        color: #94a3b8;
        font-size: 1rem;
        transition: color 0.3s;
    }

    .modern-input,
    .modern-select {
        width: 100%;
        padding: 14px 20px 14px 50px;
        background: #f8fafc;
        border: 2px solid #f1f5f9;
        border-radius: 14px;
        font-weight: 600;
        color: #1e293b;
        transition: all 0.3s;
    }

    .modern-input:focus,
    .modern-select:focus {
        background: white;
        border-color: var(--c-primary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        outline: none;
    }

    .modern-input:focus+i,
    .modern-select:focus+i {
        color: var(--c-primary);
    }

    /* Custom Radio Modern Cards */
    .custom-radio-modern input {
        position: absolute;
        opacity: 0;
    }

    .radio-card {
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 24px 15px;
        background: #f8fafc;
        border: 2px solid #f1f5f9;
        border-radius: 20px;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .radio-card .icon {
        font-size: 1.5rem;
        color: #94a3b8;
        margin-bottom: 12px;
        transition: all 0.3s;
    }

    .radio-card .label {
        font-weight: 800;
        color: #475569;
        font-size: 0.95rem;
        margin-bottom: 2px;
    }

    .radio-card .desc {
        font-size: 0.7rem;
        color: #94a3b8;
        font-weight: 600;
    }

    .custom-radio-modern input:checked+.radio-card {
        background: white;
        border-color: var(--c-primary);
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.1);
    }

    .custom-radio-modern input:checked+.radio-card .icon {
        color: var(--c-primary);
        transform: scale(1.1);
    }

    .custom-radio-modern input:checked+.radio-card .label {
        color: #1e293b;
    }

    .card-glow {
        position: absolute;
        width: 100px;
        height: 100px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.1), transparent 70%);
        top: -50px;
        right: -50px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .custom-radio-modern input:checked+.radio-card .card-glow {
        opacity: 1;
    }

    .animate-fadeIn {
        animation: fadeIn 0.4s ease-out;
    }
</style>
<?= $this->endSection() ?>