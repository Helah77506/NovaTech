<?php
$message = '';

if (isset($_GET['error']) && $_GET['error'] === 'invalid') {
    $message = 'Please enter a valid email address.';
}

if (isset($_GET['status']) && $_GET['status'] === 'notfound') {
    $message = 'No account was found with that email.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - NovaTech</title>
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

            <h1>Forgot Password</h1>
            <p>Enter your email to generate a password reset link.</p>

            <?php if ($message !== ''): ?>
                <p class="auth-info" style="display:block;"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <form action="forgot_password_process.php" method="post">
                <div class="form-group">
                    <label>Email Address</label>
                    <input
                        type="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    />
                </div>

                <button type="submit">Generate Reset Link</button>
            </form>

            <p class="login-text register-text">
                <a href="Loginpage.php">Back to login</a>
            </p>
        </div>
    </div>

</body>
</html>