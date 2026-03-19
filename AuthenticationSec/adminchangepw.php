<?php 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link rel="stylesheet" href="cssadmin/changepw.css">
</head>
<body>

<div class="container">
    <h2>Change Your Password</h2>

    <form action="process_change_password.php" method="POST">
        <!-- if statment which only asks the user to enter current password if its not there 
        first login -->
        <?php if(!isset($_GET['firstlogin'])): ?>
            <label for="current_password">Current Password</label>
            <input type="password" name="current_password" required>
        <?php endif; ?>

        <label for="new_password">New Password</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_password">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <p id="error" class="error"></p>

        <button type="submit" id="submitBtn" disabled>Update Password</button>
    </form>

    <p class="note">Make sure your password is strong.</p>

   <script>
    //ensure passwords match and meet requirements
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const submitBtn = document.getElementById('submitBtn');
    const errorText = document.getElementById('error');

    function isStrongPassword(password) {
        // regex for: 8+ chars, 1 lowercase, 1 uppercase, 1 special char
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{8,}$/;
        return regex.test(password);
    }

    function checkPasswords() {
        const pw1 = newPassword.value;
        const pw2 = confirmPassword.value;

        if (pw1 === "" || pw2 === "") {
            submitBtn.disabled = true;
            errorText.textContent = "";
            return;
        }

        if (!isStrongPassword(pw1)) {
            submitBtn.disabled = true;
            errorText.textContent = "Password must be 8+ chars, include upper, lower, and special character";
            return;
        }

        if (pw1 !== pw2) {
            submitBtn.disabled = true;
            errorText.textContent = "Passwords do not match";
            return;
        }

        // all good
        submitBtn.disabled = false;
        errorText.textContent = "";
    }

    newPassword.addEventListener('input', checkPasswords);
    confirmPassword.addEventListener('input', checkPasswords);
</script>

</div>

</body>
</html>