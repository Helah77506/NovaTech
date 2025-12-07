<?php
session_start();
require 'Config.php'; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Login.html'); // Changed to .html
    exit();
}

$identifier = trim($_POST['username'] ?? '');
$password   = $_POST['password'] ?? '';

// basic check
if ($identifier === '' || $password === '') {
    header('Location: login.html?error=empty'); // More specific error
    exit();
}

// Check if connection exists
if (!isset($conn)) {
    die("Database connection failed");
}

$stmt = $conn->prepare(
    "SELECT id, full_name, email, password_hash 
     FROM users 
     WHERE email = ?"
);

$stmt->bind_param("s", $identifier);
$stmt->execute();
$stmt->store_result();

// found exactly one user
if ($stmt->num_rows === 1) {
    $stmt->bind_result($id, $full_name, $email, $hash);
    $stmt->fetch();

    // check password
    if (password_verify($password, $hash)) {
        session_regenerate_id(true); // Security: prevent session fixation
        
        $_SESSION['user_id']   = $id;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email']     = $email;

        header('Location: Home.html');
        exit();
    }
}

$stmt->close();
$conn->close();

// wrong login
header('Location: Login.html?error=wrong');
exit();