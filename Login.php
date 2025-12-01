<?php
// start session so we can remember the logged in user
session_start();

// connect to the database
require 'config.php';

// place to store any error messages
$errors = [];

// run this only when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // read form fields safely
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // basic validation
    if ($email === '' || $password === '') {
        $errors[] = "Please enter email and password.";
    }

    // only talk to DB if no validation errors
    if (empty($errors)) {

        // look up user by email
        $stmt = $conn->prepare("SELECT id, full_name, password_hash FROM users WHERE email = ?");
        if (!$stmt) {
            // fallback message if prepare fails
            $errors[] = "Database error. Please try again later.";
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                // get the row
                $stmt->bind_result($id, $full_name, $hash);
                $stmt->fetch();

                // check password against stored hash
                if (password_verify($password, $hash)) {
                    // save user info in session and go to Home
                    $_SESSION['user_id']   = $id;
                    $_SESSION['full_name'] = $full_name;
                    header("Location: Home.html");
                    exit;
                } else {
                    $errors[] = "Incorrect email or password.";
                }
            } else {
                $errors[] = "Incorrect email or password.";
            }

            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>

    <!-- show any PHP errors above the form -->
    <?php if (!empty($errors)): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $e): ?>
                <li><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="login.php" method="post">
        <label>Email</label><br>
        <input type="text" name="email" id="email"><br><br>

        <label>Password</label><br>
        <input type="password" name="password" id="password"><br><br>

        <input type="submit" value="Login">
    </form>
</body>
</html>
