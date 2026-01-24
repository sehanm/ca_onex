<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Facility Request Form<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h2>Onboarding Facility Request</h2>
    <a href="<?= base_url('onboarding/pending') ?>" class="btn-secondary">Back to Tasks</a>
</div>

<div class="card form-card-premium">
    <div class="candidate-info-glass">
        <div class="banner-top">
            <h3>Candidate Information</h3>
        </div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Candidate Name</span>
                <span class="value"><?= esc($request['candidate_name']) ?></span>
            </div>
            <div class="info-item">
                <span class="label">Designation</span>
                <span class="value"><?= esc($request['designation']) ?></span>
            </div>
            <div class="info-item">
                <span class="label">Joining Date</span>
                <span class="value date"><?= date('M d, Y', strtotime($request['joining_date'])) ?></span>
            </div>
            <div class="info-item">
                <span class="label">Department</span>
                <span class="value"><?= esc($request['department_name']) ?></span>
            </div>
        </div>
    </div>

    <form action="<?= base_url('onboarding/save-form/'.$request['id']) ?>" method="post" enctype="multipart/form-data" id="facilityForm">
        <?= csrf_field() ?>

        <!-- Type Selection -->
        <div class="form-section">
            <div class="section-badge">General Detail</div>
            <div class="row">
                <div class="col">
                    <div class="form-group-premium">
                        <label>Onboarding Type</label>
                        <div class="select-wrapper">
                            <select name="onboarding_type" required>
                                <option value="New Recruit">New Recruit</option>
                                <option value="Internal Transfer">Internal Transfer</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group-premium">
                        <label>Designation Type</label>
                        <div class="select-wrapper">
                            <select name="designation_type" id="designation_type" required>
                                <option value="New">New Designation</option>
                                <option value="Replacement">Replacement</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div id="replacement_field" class="form-group-premium animate-in" style="display:none;">
                <label>Previous Employee Name</label>
                <input type="text" name="replacement_employee_name" placeholder="Who is being replaced?">
            </div>

            <div id="budget_doc_field" class="form-group-premium animate-in">
                <label>Budget Approval Document <span class="required">*</span></label>
                <div class="file-upload-wrapper">
                    <input type="file" name="budget_approval_doc" id="budget_approval_doc" required>
                    <div class="file-custom-ui">
                        <span>Click or drag budget approval file here</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1. Administration & Events -->
        <div class="form-section">
            <div class="section-badge admin">Administration & Events</div>
            <div class="facilities-grid">
                <label class="custom-checkbox">
                    <input type="checkbox" name="admin_chair" value="1">
                    <div class="checkbox-tile">
                        <span>Chair</span>
                    </div>
                </label>
                <label class="custom-checkbox">
                    <input type="checkbox" name="admin_table" value="1">
                    <div class="checkbox-tile">
                        <span>Table</span>
                    </div>
                </label>
                <label class="custom-checkbox">
                    <input type="checkbox" name="admin_phone" value="1">
                    <div class="checkbox-tile">
                        <span>Land Phone</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- 2. HR -->
        <div class="form-section">
            <div class="section-badge hr">HR Facilities</div>
            <div class="facilities-grid">
                <label class="custom-checkbox">
                    <input type="checkbox" name="hr_mobile" value="1">
                    <div class="checkbox-tile">
                        <span>Mobile Phone</span>
                    </div>
                </label>
                <label class="custom-checkbox">
                    <input type="checkbox" name="hr_sim" value="1">
                    <div class="checkbox-tile">
                        <span>SIM Card</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- 3. ICT -->
        <div class="form-section">
            <div class="section-badge ict">ICT Facilities</div>
            <div class="row">
                <div class="col">
                    <div class="form-group-premium">
                        <label>Hardware Requirement</label>
                        <div class="select-wrapper">
                            <select name="ict_desktop_laptop">
                                <option value="None">None</option>
                                <option value="Desktop">Desktop</option>
                                <option value="Laptop">Laptop</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="ict-printer-box">
                         <label class="custom-checkbox simple">
                            <input type="checkbox" name="ict_printer" value="1">
                            <span class="checkmark"></span>
                            <span>Printer Access Required</span>
                        </label>
                    </div>
                </div>
            </div>

            <h5 class="sub-heading">Software Access Permissions</h5>
            <div class="software-grid">
                <label class="soft-item"><input type="checkbox" name="soft_smms" value="1"> <span>SMMS</span></label>
                <label class="soft-item"><input type="checkbox" name="soft_receipt" value="1"> <span>Receipt Module</span></label>
                <label class="soft-item"><input type="checkbox" name="soft_training" value="1"> <span>Training Module</span></label>
                <label class="soft-item"><input type="checkbox" name="soft_ecole" value="1"> <span>Ecole</span></label>
                
                <div class="soft-item-container">
                    <label class="soft-item"><input type="checkbox" name="soft_pronto" id="soft_pronto" value="1"> <span>Pronto</span></label>
                    <div id="pronto_user_field" class="nested-input" style="display:none;">
                        <input type="text" name="pronto_previous_user" placeholder="Previous user name...">
                    </div>
                </div>

                <label class="soft-item"><input type="checkbox" name="soft_ims" value="1"> <span>IMS</span></label>
                <label class="soft-item"><input type="checkbox" name="soft_sap" value="1"> <span>Sap Business One</span></label>
                <label class="soft-item"><input type="checkbox" name="soft_imeet" value="1"> <span>Imeet-Venue Booking</span></label>
            </div>

            <div class="form-group-premium" style="margin-top: 25px; border-top: 1px dashed #eee; padding-top: 20px;">
                <label>Mirror existing user access:</label>
                <input type="text" name="access_copy_user" placeholder="Enter name of user with similar role...">
                <small>The ICT team will replicate this user's permissions for the new joiner.</small>
            </div>
        </div>

        <div class="form-footer">
            <button type="submit" class="btn-submit-premium">
                <span>Submit Facility Request</span>
            </button>
        </div>
    </form>
</div>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #800000 0%, #b30000 100%);
        --glass-bg: rgba(255, 255, 255, 0.95);
        --shadow-soft: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .form-card-premium {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: var(--shadow-soft);
        border: 1px solid #f0f0f0;
    }

    /* Candidate Info Glassmorphism */
    .candidate-info-glass {
        background: #f8fafc;
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 35px;
        border: 1px solid #e2e8f0;
        position: relative;
        overflow: hidden;
    }

    .banner-top {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        color: #800000;
    }

    .banner-top i { font-size: 1.2rem; }
    .banner-top h3 { margin: 0; font-size: 1.1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .info-item { display: flex; flex-direction: column; gap: 5px; }
    .info-item .label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 600; }
    .info-item .value { font-size: 1rem; color: #1e293b; font-weight: 600; }
    .info-item .value.date { color: #800000; }

    /* Sections */
    .form-section {
        margin-bottom: 40px;
        position: relative;
        padding: 25px;
        border-radius: 16px;
        background: #fff;
        border: 1px solid #f1f5f9;
        transition: transform 0.3s ease;
    }

    .form-section:hover { transform: translateY(-2px); }

    .section-badge {
        position: absolute;
        top: -12px;
        left: 20px;
        background: #1e293b;
        color: white;
        padding: 4px 16px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .section-badge.admin { background: #0891b2; }
    .section-badge.hr { background: #db2777; }
    .section-badge.ict { background: #7c3aed; }

    /* Form Elements */
    .form-group-premium { margin-bottom: 20px; }
    .form-group-premium label { display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem; }
    .form-group-premium input[type="text"] {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        transition: all 0.2s;
    }
    .form-group-premium input:focus { border-color: #800000; outline: none; box-shadow: 0 0 0 4px rgba(128, 0, 0, 0.1); }

    .select-wrapper { position: relative; width: 100%; }
    .select-wrapper::after {
        content: '';
        position: absolute;
        top: 50%;
        right: 18px;
        width: 8px;
        height: 8px;
        border-right: 2px solid #94a3b8;
        border-bottom: 2px solid #94a3b8;
        transform: translateY(-70%) rotate(45deg);
        pointer-events: none;
        transition: all 0.2s ease;
    }
    .select-wrapper select {
        width: 100%;
        height: 48px;
        padding: 0 40px 0 16px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        appearance: none;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 500;
        color: #1e293b;
        font-size: 0.95rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    }
    .select-wrapper select:hover {
        border-color: #cbd5e1;
        background-color: #fcfcfc;
    }
    .select-wrapper select:focus {
        border-color: #800000;
        outline: none;
        box-shadow: 0 0 0 4px rgba(128, 0, 0, 0.08);
    }
    .select-wrapper:focus-within::after {
        border-color: #800000;
        transform: translateY(-30%) rotate(-135deg);
    }

    /* Facility Tiles (Checkboxes) */
    .facilities-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 15px;
    }

    .custom-checkbox input { display: none; }
    .checkbox-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 60px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        background: white;
        text-align: center;
    }

    .checkbox-tile span { font-size: 0.9rem; font-weight: 600; color: #64748b; }

    .custom-checkbox input:checked + .checkbox-tile {
        border-color: #800000;
        background: rgba(128, 0, 0, 0.03);
        box-shadow: 0 4px 12px rgba(128, 0, 0, 0.05);
    }
    .custom-checkbox input:checked + .checkbox-tile span { color: #800000; }

    /* Software Access */
    .sub-heading { font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 25px 0 15px 0; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; }
    .software-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 12px;
    }

    .soft-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 15px;
        background: #f8fafc;
        border-radius: 8px;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.2s;
    }
    .soft-item:hover { background: #f1f5f9; border-color: #e2e8f0; }
    .soft-item input { width: 18px; height: 18px; accent-color: #800000; }
    .soft-item span { font-size: 0.9rem; font-weight: 500; color: #475569; }

    .nested-input input {
        margin-top: 10px;
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.85rem;
    }

    /* File Upload */
    .file-upload-wrapper { position: relative; height: 80px; }
    .file-upload-wrapper input {
        position: absolute; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2;
    }
    .file-custom-ui {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        border: 2px dashed #e2e8f0; border-radius: 12px;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 5px; color: #94a3b8; transition: all 0.2s;
    }
    .file-upload-wrapper:hover .file-custom-ui { border-color: #800000; color: #800000; background: rgba(128, 0, 0, 0.02); }

    /* Footer */
    .form-footer { margin-top: 50px; padding-top: 30px; border-top: 1px solid #f1f5f9; }
    .btn-submit-premium {
        background: var(--primary-gradient);
        color: white;
        border: none;
        padding: 16px 40px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        margin-left: auto;
        box-shadow: 0 4px 15px rgba(128, 0, 0, 0.3);
        transition: all 0.3s;
    }
    .btn-submit-premium:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(128, 0, 0, 0.4); opacity: 0.95; }

    .required { color: #ef4444; margin-left: 4px; }
    .animate-in { animation: fadeIn 0.4s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .ict-printer-box {
        display: flex;
        align-items: center;
        height: 100%;
        padding-top: 25px;
    }
    .custom-checkbox.simple { display: flex; align-items: center; gap: 10px; cursor: pointer; }
    .custom-checkbox.simple input { width: 20px; height: 20px; accent-color: #800000; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const designationType = document.getElementById('designation_type');
        const replacementField = document.getElementById('replacement_field');
        const budgetDocField = document.getElementById('budget_doc_field');
        const budgetInput = document.getElementById('budget_approval_doc');
        const softPronto = document.getElementById('soft_pronto');
        const prontoUserField = document.getElementById('pronto_user_field');

        designationType.addEventListener('change', function() {
            if (this.value === 'Replacement') {
                replacementField.style.display = 'block';
                budgetDocField.style.display = 'none';
                budgetInput.required = false;
            } else {
                replacementField.style.display = 'none';
                budgetDocField.style.display = 'block';
                budgetInput.required = true;
            }
        });

        softPronto.addEventListener('change', function() {
            prontoUserField.style.display = this.checked ? 'block' : 'none';
        });
    });
</script>
<?= $this->endSection() ?>
