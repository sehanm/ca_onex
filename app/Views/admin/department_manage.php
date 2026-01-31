<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Department Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h2>Department Management</h2>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Department Name</th>
                    <th>Assigned Manager/HOD</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($departments as $dept): ?>
                <tr>
                    <td><?= esc($dept['department_name']) ?></td>
                    <td>
                        <?php if ($dept['manager_name']): ?>
                            <span class="badge badge-info"><?= esc($dept['manager_name']) ?></span>
                        <?php else: ?>
                            <span class="badge" style="background-color: #eee; color: #777;">Unassigned</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn-icon" onclick="openAssignModal(<?= $dept['id'] ?>, '<?= esc($dept['department_name']) ?>', '<?= $dept['manager_id'] ?>')" title="Assign Manager">
                            <i class="fa-solid fa-user-pen"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Assign Manager Modal -->
<div id="assignModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); padding: 20px;">
    <div class="modal-content" style="background-color: #fefefe; margin: 50px auto; padding: 30px; border: 1px solid #888; width: 100%; max-width: 400px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
        <h3>Assign Manager</h3>
        <p>Department: <span id="modalDeptName" style="font-weight: bold;"></span></p>
        
        <form action="<?= base_url('admin/departments/update-manager') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="department_id" id="modalDeptId">
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="manager_id" style="display:block; margin-bottom: 8px;">Select Manager/HOD:</label>
                <select name="manager_id" id="modalManagerId" class="form-control" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ddd;">
                    <option value="">-- No Manager --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>"><?= esc($user['full_name']) ?> (<?= esc($user['role_name']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="text-align: right;">
                <button type="button" class="btn-secondary" onclick="closeAssignModal()" style="margin-right: 10px;">Cancel</button>
                <button type="submit" class="btn-primary">Save Assignment</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAssignModal(id, name, managerId) {
        document.getElementById('modalDeptId').value = id;
        document.getElementById('modalDeptName').innerText = name;
        document.getElementById('modalManagerId').value = managerId || "";
        document.getElementById('assignModal').style.display = "block";
    }

    function closeAssignModal() {
        document.getElementById('assignModal').style.display = "none";
    }

    // Close modal if clicked outside
    window.onclick = function(event) {
        if (event.target == document.getElementById('assignModal')) {
            closeAssignModal();
        }
    }
</script>
<?= $this->endSection() ?>
