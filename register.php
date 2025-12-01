<?php
session_start();
require 'Config.php';     // connect to database

$errors  = [];
$success = '';

// Handle POST request from register form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form inputs
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if ($username === '' || $email === '' || $password === '') {
        $errors[] = "Please fill in all fields.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    // Check if email is already used
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Email already exists.";
        }

        $stmt->close();
    }

    // If everything is valid, insert new user
    if (empty($errors)) {

        // Hash password before saving
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Map values to database columns
        $institution_name = '';
        $full_name        = $username;

        $stmt = $conn->prepare(
            "INSERT INTO users (institution_name, full_name, email, password_hash)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $institution_name, $full_name, $email, $hash);

        if ($stmt->execute()) {
            $success = "Account created successfully.";
        } else {
            $errors[] = "Error creating account.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <script src="register.js"></script>
</head>
<body>

<h2>Register</h2>

<!-- Error messages -->
<?php if (!empty($errors)): ?>
    <ul style="color:red;">
        <?php foreach ($errors as $e): ?>
            <li><?php echo htmlspecialchars($e); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<!-- Success message -->
<?php if ($success): ?>
    <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>

<!-- Registration form -->
<form method="post" action="register.php">
    Username:
    <input type="text" name="username" id="username"><br>

    Password:
    <input type="password" name="password" id="password"><br>

    Email:
    <input type="email" name="email" id="email"><br><br>

    <input type="submit" value="Register">
    <input type="reset" value="clear">

    <!-- This label is used by your JS for messages -->
    <label id="infolabel" hidden>.</label>
</form>

<p>Already have an account? <a href="login.php">Log in</a></p>

</body>
</html>
