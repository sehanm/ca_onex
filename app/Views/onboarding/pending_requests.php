<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Onboarding Tasks<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h2>My Onboarding Requests</h2>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Designation</th>
                    <th>Date of Join</th>
                    <th>Department</th>
                    <th>Requested By</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $req): ?>
                <tr>
                    <td>
                        <div style="font-weight: 500;"><?= esc($req['candidate_name']) ?></div>
                    </td>
                    <td><?= esc($req['designation']) ?></td>
                    <td>
                        <?= !empty($req['joining_date']) ? date('M d, Y', strtotime($req['joining_date'])) : '-' ?>
                    </td>
                    <td><?= esc($req['department_name']) ?></td>
                    <td><?= esc($req['hr_name']) ?></td>
                    <td>
                        <?php 
                            $statusClass = 'badge';
                            if ($req['status'] == 'Pending_HOD') $statusClass .= ' badge-info'; 
                            elseif ($req['status'] == 'Processing') $statusClass .= ' badge-role'; // Grayish
                            elseif ($req['status'] == 'Completed') $statusClass .= ' stats-green';
                            else $statusClass .= ' badge-role';
                        ?>
                        <span class="<?= $statusClass ?>"><?= str_replace('_', ' ', esc($req['status'])) ?></span>
                    </td>
                    <td><?= date('M d, Y', strtotime($req['created_at'])) ?></td>
                    <td>
                        <?php if ($req['status'] === 'Pending_HOD'): ?>
                        <a href="<?= base_url('onboarding/fill-form/'.$req['id']) ?>" class="btn-primary" style="padding: 6px 12px; font-size: 0.8rem;">
                            Fill Form
                        </a>
                        <?php else: ?>
                        <a href="<?= base_url('onboarding/view/'.$req['id']) ?>" class="btn-icon" title="View Details">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($requests)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; color: #777;">No onboarding requests assigned to you.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
