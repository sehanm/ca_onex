<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Manage Requests<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-premium">
    <div class="header-main">
        <h2 class="title-gradient">Request Management</h2>
        <p class="subtitle">Process and fulfill hardware requirements.</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('hardware-requests/overview') ?>" class="btn-premium btn-secondary-premium">
            <i class="fa-solid fa-chart-pie me-2"></i> Overview
        </a>
        <a href="<?= base_url('hardware-requests/scanner') ?>" class="btn-premium">
            <i class="fa-solid fa-qrcode me-2"></i> Quick Scan
        </a>
    </div>
</div>

<div class="card shadow-premium mt-4">
    <div class="table-responsive">
        <table class="table datatable datatable-premium">
            <thead>
                <tr>
                    <th>Request Profile</th>
                    <th>Item Details</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th class="text-end">Command</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td>
                            <div class="item-id-cell">
                                <div class="avatar-lite" style="width: 42px; height: 42px; font-size: 1rem;">
                                    <?= strtoupper(substr($request['requester_name'], 0, 1)) ?>
                                </div>
                                <div class="item-primary-info">
                                    <span class="item-model"><?= esc($request['requester_name']) ?></span>
                                    <span class="item-code"><?= date('M d, Y', strtotime($request['created_at'])) ?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="category-chip" style="background: white; border: none; padding: 0;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="item-icon-box" style="width: 32px; height: 32px; font-size: 0.9rem;">
                                        <i
                                            class="fa-solid <?= $request['item_type'] == 'asset' ? 'fa-laptop' : 'fa-plug' ?>"></i>
                                    </div>
                                    <div class="d-flex flex-column" style="line-height: 1.2;">
                                        <span class="fw-bold text-dark"
                                            style="font-size: 0.85rem;"><?= esc($request['category']) ?></span>
                                        <span class="text-muted text-uppercase"
                                            style="font-size: 0.7rem; letter-spacing: 0.5px;"><?= ucfirst($request['item_type']) ?></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted small"
                                style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; max-width: 250px;">
                                <?= esc($request['reason']) ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $status_class = 'status-stock'; // Default/Returned
                            if ($request['status'] == 'assigned')
                                $status_class = 'status-assigned'; // Active
                            if ($request['status'] == 'pending')
                                $status_class = 'status-damaged'; // Pending (using warning color)
                            if ($request['status'] == 'rejected')
                                $status_class = 'status-damaged'; // Red
                        
                            // Custom map for pending to be distinct if possible, or reuse 'status-damaged' which is red/warning style
                            // In accessories, 'Damaged' is red. 'Stock' is blue. 'Assigned' is green.
                            // Let's stick to the mapped classes.
                            ?>
                            <span class="status-pill <?= $status_class ?>">
                                <span class="dot"></span>
                                <?= ucfirst($request['status']) ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="action-flex justify-content-end">
                                <?php if ($request['status'] == 'pending'): ?>
                                    <a href="<?= base_url('hardware-requests/scanner') ?>" class="btn-action-view"
                                        title="Scan to Fulfill" style="color: #6366f1; background: #eef2ff;">
                                        <i class="fa-solid fa-qrcode"></i>
                                    </a>

                                    <form action="<?= base_url('hardware-requests/reject') ?>" method="POST"
                                        onsubmit="return confirm('Reject this request?');" style="display:inline;">
                                        <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                        <button type="submit" class="btn-action-trash" title="Reject Request">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    </form>
                                <?php elseif ($request['status'] == 'assigned'): ?>
                                    <span class="text-success small fw-bold"><i class="fa-solid fa-check me-1"></i>
                                        Active</span>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
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
    /* Additional overrides to ensure perfect match if base styles differ slightly */
    .btn-secondary-premium {
        background: white;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .btn-secondary-premium:hover {
        background: #f8fafc;
        color: #1e293b;
    }
</style>
<?= $this->endSection() ?>