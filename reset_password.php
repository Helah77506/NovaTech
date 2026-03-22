<?php
$token = $_GET['token'] ?? '';
$errorMessage = '';

if ($token === '') {
    die("Invalid reset link.");
}

if (isset($_GET['error'])) {
    if ($_GET['error'] === 'empty') {
        $errorMessage = 'Please fill in all fields.';
    } elseif ($_GET['error'] === 'match') {
        $errorMessage = 'Passwords do not match.';
    } elseif ($_GET['error'] === 'weak') {
        $errorMessage = 'Password must be at least 8 characters.';
    } elseif ($_GET['error'] === 'invalid') {
        $errorMessage = 'This reset link is invalid or expired.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - NovaTech</title>
    <link rel="stylesheet" href="Styles/Login.css">
</head>
<body>

    <a href="Loginpage.php" id="back-home-link">
        <div class="back-home-container">
            <img src="Assets/Home/arrow.png" class="home-icon" alt="Back" />
            <span class="back-home-text">Back to Login</span>
        </div>
    </a>

    <div class="container">
        <div class="left"></div>

        <div class="right">
            <img src="Assets/Home/Logo.png" class="logo" alt="NovaTech Logo" />

            <h1>Reset Password</h1>
            <p>Enter your new password below.</p>

            <?php if ($errorMessage !== ''): ?>
                <p class="auth-info" style="display:block;"><?php echo htmlspecialchars($errorMessage); ?></p>
            <?php endif; ?>

            <form action="reset_password_process.php" method="post">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div class="form-group">
                    <label>New Password</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Enter new password"
                        required
                    />
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input
                        type="password"
                        name="confirm_password"
                        placeholder="Confirm new password"
                        required
                    />
                </div>

                <button type="submit">Reset Password</button>
            </form>

            <p class="login-text register-text">
                <a href="Loginpage.php">Back to login</a>
            </p>
        </div>
    </div>

</body>
</html>