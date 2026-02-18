<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>My Hardware Requests<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-premium mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold text-dark mb-1">My Requests</h2>
            <p class="text-muted small mb-0">Track your hardware assignment status.</p>
        </div>
        <a href="<?= base_url('hardware-requests/create') ?>" class="btn btn-dark d-flex align-items-center gap-2">
            <i class="fa-solid fa-plus"></i> New Request
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3 border-0">Item Details</th>
                        <th class="py-3 border-0">Type</th>
                        <th class="py-3 border-0">Reason</th>
                        <th class="py-3 border-0">Status</th>
                        <th class="pe-4 text-end py-3 border-0">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-light text-secondary"
                                        style="width: 40px; height: 40px;">
                                        <i
                                            class="fa-solid <?= $request['item_type'] == 'asset' ? 'fa-laptop' : 'fa-plug' ?>"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?= esc($request['category']) ?></div>
                                        <div class="text-muted small"><?= ucfirst($request['item_type']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal">
                                    <?= ucfirst($request['requirement_type']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="text-muted small text-truncate" style="max-width: 250px;">
                                    <?= esc($request['reason']) ?>
                                </div>
                            </td>
                            <td>
                                <?php
                                $statusConfig = match ($request['status']) {
                                    'assigned' => ['color' => '#10b981', 'label' => 'Active'],
                                    'pending' => ['color' => '#f59e0b', 'label' => 'Pending'],
                                    'rejected' => ['color' => '#ef4444', 'label' => 'Rejected'],
                                    'returned' => ['color' => '#6b7280', 'label' => 'Returned'],
                                    default => ['color' => '#9ca3af', 'label' => 'Unknown']
                                };
                                ?>
                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        style="width: 8px; height: 8px; border-radius: 50%; background-color: <?= $statusConfig['color'] ?>;">
                                    </div>
                                    <span class="text-dark small fw-bold"><?= $statusConfig['label'] ?></span>
                                </div>
                            </td>
                            <td class="pe-4 text-end text-muted small">
                                <?= date('M d, Y', strtotime($request['created_at'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($requests)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>