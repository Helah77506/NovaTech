<?php
require 'Config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgot_password.php');
    exit();
}

$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: forgot_password.php?error=invalid');
    exit();
}

$stmt = $conn->prepare("SELECT id, email FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header('Location: forgot_password.php?status=notfound');
    exit();
}

$user = $result->fetch_assoc();
$userId = (int)$user['id'];

$token = bin2hex(random_bytes(32));
$tokenHash = hash('sha256', $token);
$expires = date('Y-m-d H:i:s', time() + 3600);

$update = $conn->prepare("
    UPDATE users
    SET Reset_Token_Hash = ?, Reset_Token_Expires = ?
    WHERE id = ?
");
$update->bind_param("ssi", $tokenHash, $expires, $userId);
$update->execute();

$resetLink = "http://localhost/NOVATECH/reset_password.php?token=" . urlencode($token);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Link Generated - NovaTech</title>
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

            <h1>Reset Link Generated</h1>
            <p>Testing on localhost: click the link below to reset the password.</p>

            <p class="auth-info" style="display:block; width:90%; word-break:break-all;">
                <a href="<?php echo htmlspecialchars($resetLink); ?>">
                    <?php echo htmlspecialchars($resetLink); ?>
                </a>
            </p>

            <p class="login-text register-text">
                <a href="Loginpage.php">Back to login</a>
            </p>
        </div>
    </div>

</body>
</html>