<?php
require 'Config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Loginpage.php');
    exit();
}

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($token === '' || $password === '' || $confirm === '') {
    header('Location: reset_password.php?token=' . urlencode($token) . '&error=empty');
    exit();
}

if ($password !== $confirm) {
    header('Location: reset_password.php?token=' . urlencode($token) . '&error=match');
    exit();
}

if (strlen($password) < 8) {
    header('Location: reset_password.php?token=' . urlencode($token) . '&error=weak');
    exit();
}

$tokenHash = hash('sha256', $token);

// find matching reset token
$stmt = $conn->prepare("
    SELECT ID, Reset_Token_Expires
    FROM users
    WHERE Reset_Token_Hash = ?
    LIMIT 1
");

if (!$stmt) {
    die("RESET PASSWORD SELECT prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $tokenHash);

if (!$stmt->execute()) {
    die("RESET PASSWORD SELECT execute failed: " . $stmt->error);
}

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header('Location: reset_password.php?error=invalid');
    exit();
}

$user = $result->fetch_assoc();

if (empty($user['Reset_Token_Expires']) || strtotime($user['Reset_Token_Expires']) < time()) {
    header('Location: reset_password.php?error=invalid');
    exit();
}

// update password and clear token
$newHash = password_hash($password, PASSWORD_DEFAULT);
$userId = (int)$user['ID'];

$update = $conn->prepare("
    UPDATE users
    SET Password_Hash = ?, Reset_Token_Hash = NULL, Reset_Token_Expires = NULL
    WHERE ID = ?
");

if (!$update) {
    die("RESET PASSWORD UPDATE prepare failed: " . $conn->error);
}

$update->bind_param("si", $newHash, $userId);

if (!$update->execute()) {
    die("RESET PASSWORD UPDATE execute failed: " . $update->error);
}

header('Location: Loginpage.php?reset=success');
exit();
?>