<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CA OnEx</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

    <div class="login-container">
        <!-- Left Side: Visual -->
        <div class="login-visual">
            <div class="visual-header">
                <div class="logo-text">
                    CA OnEx
                </div>
            </div>
            
            <div class="visual-content">
                <h1>Streamline Your <br><span class="highlight-text">Employee Journey.</span></h1>
                <p>From Onboarding to Exit: The entire employee experience, simplified. Manage everything in one secure place.</p>
            </div>

            <div class="visual-footer">
                &copy; <?= date('Y') ?> CA Sri Lanka. All rights reserved.
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="login-form-wrapper">
            <div class="login-card">
                <div class="form-header">
                    <h2>Welcome Back</h2>
                    <p>Enter your credentials to access your account.</p>
                </div>

                <form action="<?= base_url('auth/attemptLogin') ?>" method="POST">
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn-login">Sign In</button>
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
        <?php if (session()->getFlashdata('msg')): ?>
            showToast("<?= esc(session()->getFlashdata('msg')) ?>", 'info');
        <?php endif; ?>
    </script>
</body>
</html>
