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

$stmt = $conn->prepare("
    SELECT id, Reset_Token_Expires
    FROM users
    WHERE Reset_Token_Hash = ?
    LIMIT 1
");
$stmt->bind_param("s", $tokenHash);
$stmt->execute();
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

$newHash = password_hash($password, PASSWORD_DEFAULT);
$userId = (int)$user['id'];

$update = $conn->prepare("
    UPDATE users
    SET Password_Hash = ?, Reset_Token_Hash = NULL, Reset_Token_Expires = NULL
    WHERE id = ?
");
$update->bind_param("si", $newHash, $userId);
$update->execute();

header('Location: Loginpage.php?reset=success');
exit();
?>