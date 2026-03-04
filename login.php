<?php
session_start();
require 'Config.php'; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Loginpage.php');
    exit();
}

$identifier = trim($_POST['identifier'] ?? '');
$password   = $_POST['password'] ?? '';

// basic check
if ($identifier === '' || $password === '') {
    header('Location: Loginpage.php?error=wrong');
    exit();
}

// look for a user with this email or username
$stmt = $conn->prepare(
    "SELECT id, full_name, email, password_hash 
     FROM users 
     WHERE email = ? OR full_name = ?"
);

$stmt->bind_param("ss", $identifier, $identifier);
$stmt->execute();
$stmt->store_result();

// found exactly one user
if ($stmt->num_rows === 1) {
    $stmt->bind_result($id, $full_name, $email, $hash);
    $stmt->fetch();

 // check password
    if (password_verify($password, $hash)) {
        $_SESSION['user_id']   = $id;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email']     = $email;

        header('Location: Homepage.php');
        exit();
    }
}

// wrong login
header('Location: Loginpage.php?error=wrong');
exit();
