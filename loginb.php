<?php
session_start();
require 'Config.php';

//make sure request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Loginpage.php');
    exit();
}

// get form inputs
$identifier = trim($_POST['identifier'] ?? '');
$password   = $_POST['password'] ?? '';

// basic check
if ($identifier === '' || $password === '') {
    header('Location: Loginpage.php?error=empty');
    exit();
}

// look for user by email or full name
$stmt = $conn->prepare("
    SELECT ID, Full_Name, Email, Password_Hash, Role, firstlogin
    FROM users
    WHERE Email = ? OR Full_Name = ?
    LIMIT 1
");

// check if prepare failed
if (!$stmt) {
    die("Login prepare failed: " . $conn->error);
}

// bind parameters
$stmt->bind_param("ss", $identifier, $identifier);

// execute query
$stmt->execute();

// get result
$result = $stmt->get_result();

// check if exactly one user found
if ($result->num_rows === 1) {

    $user = $result->fetch_assoc();

    // verify password
    if (password_verify($password, $user['Password_Hash'])) {

        // set session variables
        $_SESSION['user_id']   = $user['ID'];
        $_SESSION['id']        = $user['ID'];
        $_SESSION['full_name'] = $user['Full_Name'];
        $_SESSION['email']     = $user['Email'];
        $_SESSION['role']      = $user['Role'];

        // admin redirect logic
        if (strtolower($user['Role']) === 'admin') {

            // if admin first login, force password change
            if ((int)$user['firstlogin'] === 0) {
                header('Location: AuthenticationSec/adminchangepw.php?firstlogin=true');
                exit();
            } else {
                header('Location: admin.php');
                exit();
            }
        }

        // normal user redirect
        header('Location: Homepage.php');
        exit();
    }
}

// wrong login
header('Location: Loginpage.php?error=wrong');
exit();
?>