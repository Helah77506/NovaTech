<?php
session_start();
require "Config.php";

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header("Location: Login.html?error=wrong");
    exit;
}

$stmt = $conn->prepare("SELECT id, full_name, password_hash FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($id, $full_name, $hash);
    $stmt->fetch();

    if (password_verify($password, $hash)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['full_name'] = $full_name;
        header("Location: Home.html");
        exit;
    }
}

header("Location: Login.html?error=wrong");
exit;
?>
