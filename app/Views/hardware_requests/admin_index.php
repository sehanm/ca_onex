<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Request Fulfillment Control<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-premium mb-4">
    <div class="header-main">
        <h2 class="title-gradient">Fulfillment Control</h2>
        <p class="subtitle">Orchestrate organization-wide hardware provisioning.</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('hardware-requests/overview') ?>" class="btn-premium-outline">
            <i class="fa-solid fa-chart-line me-2"></i> Analytics
        </a>
        <a href="<?= base_url('hardware-requests/scanner') ?>" class="btn-premium">
            <i class="fa-solid fa-qrcode me-2"></i> Scanner Console
        </a>
    </div>
</div>

<div class="card shadow-premium border-0"
    style="border-radius: 20px; overflow: hidden; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
    <div class="table-responsive">
        <table class="table align-middle mb-0 datatable datatable-premium">
            <thead>
                <tr>
                    <th class="ps-4">Requester</th>
                    <th>Target Hardware</th>
                    <th>Fulfillment Status</th>
                    <th class="text-end pe-4">Operations</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr class="mgmt-row">
                        <td class="ps-4">
                            <div class="item-id-cell py-2">
                                <div class="avatar-lite"
                                    style="width: 45px; height: 45px; font-size: 1.1rem; background: var(--c-primary-light); color: var(--c-primary); border: 2px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                                    <?= strtoupper(substr($request['requester_name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <div class="item-primary-info">
                                    <span class="item-model"><?= esc($request['requester_name']) ?></span>
                                    <span class="item-code d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-calendar-day small opacity-50"></i>
                                        <?= date('M d, Y', strtotime($request['created_at'])) ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="item-icon-box" style="width: 40px; height: 40px; background: #f8fafc;">
                                    <i
                                        class="fa-solid <?= $request['item_type'] == 'asset' ? 'fa-laptop-code' : 'fa-plug-circle-bolt' ?> text-muted"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark"
                                        style="font-size: 0.9rem;"><?= esc($request['category']) ?></span>
                                    <span class="small text-muted d-flex align-items-center gap-1">
                                        <?= ucfirst($request['item_type']) ?>
                                        <?php if ($request['requirement_type'] === 'temporary'): ?>
                                            <span class="opacity-25 mx-1">|</span>
                                            <span class="text-warning fw-bold" style="font-size: 0.75rem;">Temporary</span>
                                            <?php if ($request['due_date']): ?>
                                                <span class="badge bg-warning-subtle text-warning border-0 ms-1"
                                                    style="font-size: 0.6rem;">
                                                    DUE <?= date('M d', strtotime($request['due_date'])) ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                            $status_class = match ($request['status']) {
                                'assigned' => 'status-assigned',
                                'pending' => 'status-damaged', // reusing yellow/warning
                                'rejected' => 'status-retired', // reusing red
                                'returned' => 'status-instore', // reusing blue/neutral
                                default => 'status-default'
                            };
                            ?>
                            <span class="status-pill <?= $status_class ?>">
                                <span class="dot"></span>
                                <?= ucfirst($request['status']) ?>
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="action-flex justify-content-end">
                                <?php if ($request['status'] == 'pending'): ?>
                                    <a href="<?= base_url('hardware-requests/scanner') ?>" class="btn-action-view"
                                        title="Scan to Assign" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                                        <i class="fa-solid fa-qrcode"></i>
                                    </a>

                                    <form action="<?= base_url('hardware-requests/reject') ?>" method="POST"
                                        onsubmit="return confirm('Deny this request proposal?');" style="display:inline;">
                                        <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                        <button type="submit" class="btn-action-trash" title="Reject Request">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted small px-3 py-1 bg-light rounded-pill">Closed</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .mgmt-row {
        transition: all 0.2s;
    }

    .mgmt-row:hover {
        background: rgba(248, 250, 252, 0.8);
    }

    .status-pill.status-damaged {
        background: #fffbeb;
        color: #b45309;
        border-color: #fde68a;
    }

    /* Pending Overwrite */

    .btn-action-view,
    .btn-action-trash {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border: none;
    }

    .btn-action-view:hover {
        transform: scale(1.1);
        background: #6366f1 !important;
        color: white !important;
    }

    .btn-action-trash:hover {
        background: #ef4444;
        color: white;
        transform: scale(1.1);
    }
</style>
<?= $this->endSection() ?>