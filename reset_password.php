<?php
$token = $_GET['token'] ?? '';
if ($token === '') {
    die("Invalid link");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="Styles/Login.css">
</head>
<body>

<div class="container">
    <div class="right">

        <h1>Reset Password</h1>

        <form action="reset_password_process.php" method="post">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="password" required />
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required />
            </div>

            <button type="submit">Reset</button>
        </form>

    </div>
</div>

</body>
</html>