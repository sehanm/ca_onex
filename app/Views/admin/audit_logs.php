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
                    <th>Action</th>
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
                    <td><span class="badge badge-info"><?= esc($log['action']) ?></span></td>
                    <td class="hide-mobile"><?= esc($log['details']) ?></td>
                    <td class="hide-tablet"><?= esc($log['ip_address']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
