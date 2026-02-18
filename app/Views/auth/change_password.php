<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - CA OnEx</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>

<body class="login-page">

    <div class="login-container">
        <!-- Left Side: Visual -->
        <div class="login-visual">
            <div class="visual-header">
                <div class="logo-text">
                    CA OnEx
                </div>
            </div>

            <div class="visual-content">
                <h1>Secure Your <br><span class="highlight-text">Account.</span></h1>
                <p>For your security, you are required to change your password on your first login or after a password
                    reset.</p>
            </div>

            <div class="visual-footer">
                &copy;
                <?= date('Y') ?> CA Sri Lanka. All rights reserved.
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="login-form-wrapper">
            <div class="login-card">
                <div class="form-header">
                    <h2>Change Password</h2>
                    <p>Please enter a new password for your account.</p>
                </div>

                <form action="<?= base_url('auth/updatePassword') ?>" method="POST">
                    <div class="form-group">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••"
                            required minlength="6">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                            placeholder="••••••••" required minlength="6">
                    </div>

                    <button type="submit" class="btn-login">Update Password</button>
                    <a href="<?= base_url('logout') ?>" class="btn-link"
                        style="display: block; text-align: center; margin-top: 1rem; color: var(--text-muted); text-decoration: none; font-size: 0.875rem;">Cancel
                        and Logout</a>
                </form>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/toast.js') ?>"></script>
    <script>
        <?php if (session()->getFlashdata('error')): ?>
                showToast("<?= esc(session()->getFlashdata('error')) ?>", 'error');
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
                showToast("<?= esc(session()->getFlashdata('success')) ?>", 'success');
        <?php endif; ?>
        <?php if (session()->getFlashdata('info')): ?>
                showToast("<?= esc(session()->getFlashdata('info')) ?>", 'info');
        <?php endif; ?>
    </script>
</body>

</html>