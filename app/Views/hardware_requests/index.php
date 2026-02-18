<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>My Hardware Requests<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-premium mb-4">
    <div class="header-main">
        <h2 class="title-gradient">My Itemes Requests</h2>
        <p class="subtitle">Track your assigned assets and pending requests.</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('hardware-requests/create') ?>" class="btn-premium">
            <i class="fa-solid fa-plus me-2"></i> New Request
        </a>
    </div>
</div>

<div class="card shadow-premium border-0"
    style="border-radius: 20px; overflow: hidden; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
    <div class="table-responsive">
        <table class="table align-middle mb-0 datatable datatable-premium">
            <thead>
                <tr>
                    <th class="ps-4">Hardware Item</th>
                    <th>Type</th>
                    <th>Request Status</th>
                    <th class="text-end pe-4">Submission Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr class="request-row-hover">
                        <td class="ps-4">
                            <div class="item-id-cell py-2">
                                <div class="item-icon-box"
                                    style="width: 45px; height: 45px; font-size: 1.1rem; background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <i class="fa-solid <?= $request['item_type'] == 'asset' ? 'fa-laptop-code' : 'fa-plug-circle-bolt' ?>"
                                        style="color: <?= $request['item_type'] == 'asset' ? '#6366f1' : '#8b5cf6' ?>;"></i>
                                </div>
                                <div class="item-primary-info">
                                    <span class="item-model"><?= esc($request['category']) ?></span>
                                    <span class="item-code d-flex align-items-center gap-2">
                                        <?= ucfirst($request['item_type']) ?>
                                        <?php if ($request['requirement_type'] === 'temporary' && $request['due_date']): ?>
                                            <span class="badge"
                                                style="background: rgba(245, 158, 11, 0.1); color: #b45309; font-size: 0.65rem;">
                                                <i class="fa-solid fa-hourglass-half me-1"></i>Due
                                                <?= date('M d', strtotime($request['due_date'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($request['requirement_type'] === 'temporary'): ?>
                                <div class="req-type-pill temporary">
                                    <i class="fa-solid fa-calendar-day small me-1"></i>
                                    Temporary
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $statusMap = match ($request['status']) {
                                'assigned' => ['bg' => '#ecfdf5', 'text' => '#059669', 'label' => 'Active Item', 'icon' => 'fa-check-circle'],
                                'pending' => ['bg' => '#fffbeb', 'text' => '#d97706', 'label' => 'Processing', 'icon' => 'fa-spinner fa-spin'],
                                'rejected' => ['bg' => '#fef2f2', 'text' => '#dc2626', 'label' => 'Declined', 'icon' => 'fa-circle-xmark'],
                                'returned' => ['bg' => '#f1f5f9', 'text' => '#475569', 'label' => 'Returned', 'icon' => 'fa-rotate-left'],
                                default => ['bg' => '#f8fafc', 'text' => '#64748b', 'label' => 'Unknown', 'icon' => 'fa-circle-question']
                            };
                            ?>
                            <span class="status-pill-premium"
                                style="background: <?= $statusMap['bg'] ?>; color: <?= $statusMap['text'] ?>;">
                                <i class="fa-solid <?= $statusMap['icon'] ?> me-1"></i>
                                <?= $statusMap['label'] ?>
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex flex-column align-items-end">
                                <span class="fw-bold text-dark"
                                    style="font-size: 0.85rem;"><?= date('M d, Y', strtotime($request['created_at'])) ?></span>
                                <span class="text-muted small"
                                    style="font-size: 0.7rem;"><?= date('H:i', strtotime($request['created_at'])) ?></span>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .request-row-hover {
        transition: all 0.2s;
    }

    .request-row-hover:hover {
        background: rgba(248, 250, 252, 0.8);
    }

    .status-pill-premium {
        padding: 6px 14px;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .req-type-pill {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        background: #f1f5f9;
        color: #475569;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid #e2e8f0;
    }

    .req-type-pill.permanent {
        color: var(--c-primary);
        border-color: rgba(99, 102, 241, 0.2);
        background: rgba(99, 102, 241, 0.05);
    }

    .req-type-pill.temporary {
        color: #d97706;
        border-color: rgba(245, 158, 11, 0.2);
        background: rgba(245, 158, 11, 0.05);
    }

    .datatable-premium thead th {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        color: #64748b;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding-top: 15px;
        padding-bottom: 15px;
    }
</style>
<?= $this->endSection() ?>