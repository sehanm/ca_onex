<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Audit Logs<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h2>System Audit Logs</h2>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Method</th>
                    <th>Action/Path</th>
                    <th class="hide-mobile">Details</th>
                    <th class="hide-tablet">IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= esc($log['created_at']) ?></td>
                        <td>
                            <strong><?= esc($log['username'] ?? 'Unknown') ?></strong><br>
                            <small><?= esc($log['full_name'] ?? '-') ?></small>
                        </td>
                        <td>
                            <span class="badge <?= ($log['method'] ?? 'GET') === 'GET' ? 'badge-secondary' : 'badge-warning' ?>">
                                <?= esc($log['method'] ?? 'GET') ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-info"><?= esc($log['action']) ?></span><br>
                            <small class="text-muted"><?= esc($log['path'] ?? '-') ?></small>
                        </td>
                        <td class="hide-mobile">
                            <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= esc($log['details']) ?>">
                                <?= esc($log['details']) ?>
                            </div>
                        </td>
                        <td class="hide-tablet"><?= esc($log['ip_address']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>