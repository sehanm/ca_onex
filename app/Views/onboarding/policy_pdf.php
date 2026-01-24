<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Policy Acceptance - <?= $r['candidate_name'] ?></title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #800000; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #800000; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; color: #666; font-size: 14px; }
        
        .section { margin-bottom: 25px; }
        .section-title { background: #f8f8f8; padding: 8px 12px; font-weight: bold; border-left: 4px solid #800000; margin-bottom: 15px; text-transform: uppercase; font-size: 14px; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 8px; border: 1px solid #eee; font-size: 13px; }
        .info-label { width: 30%; background: #fafafa; font-weight: bold; color: #555; }
        
        .hardware-box { border: 1px solid #ddd; padding: 15px; border-radius: 5px; background: #fff; }
        .hardware-title { font-weight: bold; margin-bottom: 10px; color: #333; }
        
        .software-list { margin: 0; padding-left: 20px; column-count: 2; }
        .software-list li { font-size: 13px; margin-bottom: 5px; }
        
        .policy-text { font-size: 12px; text-align: justify; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
        
        .signature-section { margin-top: 60px; }
        .sig-box { width: 45%; float: left; border-top: 1px solid #333; text-align: center; padding-top: 10px; font-size: 12px; }
        .sig-date { width: 30%; float: right; border-top: 1px solid #333; text-align: center; padding-top: 10px; font-size: 12px; }
        .clear { clear: both; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>IT Assets & Policy Acceptance</h1>
        <p>Institute of Chartered Accountants of Sri Lanka</p>
    </div>

    <div class="section">
        <div class="section-title">Employee Details</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Employee Name</td>
                <td><?= esc($r['candidate_name']) ?></td>
            </tr>
            <tr>
                <td class="info-label">Designation</td>
                <td><?= esc($r['designation']) ?></td>
            </tr>
            <tr>
                <td class="info-label">Department</td>
                <td><?= esc($r['department_name']) ?></td>
            </tr>
            <tr>
                <td class="info-label">Date of Join</td>
                <td><?= date('F d, Y', strtotime($r['joining_date'])) ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Hardware Asset Details</div>
        <div class="hardware-box">
            <div class="hardware-title"><?= $r['ict_desktop_laptop'] ?> Details</div>
            <table class="info-table" style="margin-bottom: 0;">
                <tr>
                    <td class="info-label">Model</td>
                    <td><?= esc($r['ict_model'] ?: 'N/A') ?></td>
                </tr>
                <tr>
                    <td class="info-label">Serial Number</td>
                    <td><?= esc($r['ict_serial_number'] ?: 'N/A') ?></td>
                </tr>
                <tr>
                    <td class="info-label">Asset Tag</td>
                    <td><?= esc($r['ict_asset_code'] ?: 'N/A') ?></td>
                </tr>
                <?php if ($r['ict_desktop_laptop'] == 'Desktop'): ?>
                <tr>
                    <td class="info-label">Monitor Details</td>
                    <td>
                        Model: <?= esc($r['ict_monitor_model'] ?: 'N/A') ?><br>
                        S/N: <?= esc($r['ict_monitor_serial'] ?: 'N/A') ?><br>
                        Asset: <?= esc($r['ict_monitor_asset'] ?: 'N/A') ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Software Access Provided</div>
        <?php if (!empty($softwares)): ?>
            <ul class="software-list">
                <?php foreach ($softwares as $soft): ?>
                    <li><?= $soft ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p style="font-size: 13px; color: #777;">No specific software modules requested.</p>
        <?php endif; ?>
        
        <?php if ($r['access_copy_user']): ?>
            <p style="font-size: 12px; margin-top: 10px; font-style: italic;">Note: Permissions mirrored from <?= esc($r['access_copy_user']) ?></p>
        <?php endif; ?>
    </div>

    <div class="policy-text">
        <strong>Acceptance of IT Policy:</strong><br>
        I hereby acknowledge receipt of the IT assets mentioned above. I agree to comply with the organization's IT Security Policy, including but not limited to: appropriate use of hardware, maintaining the security of my credentials, and not installing unauthorized software. I understand that these assets remain the property of the Institute and must be returned upon cessation of employment.
    </div>

    <div class="signature-section">
        <div class="sig-box">Employee Signature</div>
        <div class="sig-date">Date</div>
        <div class="clear"></div>
        
        <div style="margin-top: 40px;">
            <div class="sig-box">Handed Over By (ICT Department)</div>
            <div class="sig-date">Date</div>
            <div class="clear"></div>
        </div>
    </div>

    <div class="footer">
        This document is automatically generated by CA OnEx System. Page 1 of 1
    </div>
</body>
</html>
