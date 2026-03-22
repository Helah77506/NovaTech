<?php
require 'Config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgot_password.php');
    exit();
}

$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: forgot_password.php?error=invalid');
    exit();
}

// find user by email
$stmt = $conn->prepare("
    SELECT ID, Email
    FROM users
    WHERE Email = ?
    LIMIT 1
");

if (!$stmt) {
    die("FORGOT PASSWORD SELECT prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $email);

if (!$stmt->execute()) {
    die("FORGOT PASSWORD SELECT execute failed: " . $stmt->error);
}

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header('Location: forgot_password.php?status=notfound');
    exit();
}

$user = $result->fetch_assoc();
$userId = (int)$user['ID'];

// generate reset token
$token = bin2hex(random_bytes(32));
$tokenHash = hash('sha256', $token);
$expires = date('Y-m-d H:i:s', time() + 3600);

// store token and expiry
$update = $conn->prepare("
    UPDATE users
    SET Reset_Token_Hash = ?, Reset_Token_Expires = ?
    WHERE ID = ?
");

if (!$update) {
    die("FORGOT PASSWORD UPDATE prepare failed: " . $conn->error);
}

$update->bind_param("ssi", $tokenHash, $expires, $userId);

if (!$update->execute()) {
    die("FORGOT PASSWORD UPDATE execute failed: " . $update->error);
}

// IMPORTANT: keep this matching your localhost folder name
$resetLink = "http://localhost/novatech/reset_password.php?token=" . urlencode($token);

// send email using same settings as send_email.php
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'novatech2025nt@gmail.com';
    $mail->Password   = 'gdgxhunkjduephvb';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('novatech2025nt@gmail.com', 'NovaTech');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Password Reset - NovaTech';
    $mail->Body = "
        <h2>Password Reset</h2>
        <p>Hello,</p>
        <p>We received a request to reset your password.</p>
        <p>Click the link below to reset it:</p>
        <p><a href='$resetLink'>Reset Password</a></p>
        <p>This link expires in 1 hour.</p>
        <p>If you did not request this, please ignore this email.</p>
    ";
    $mail->AltBody = "Reset your password using this link: $resetLink";

    $mail->send();

    header('Location: forgot_password.php?status=sent');
    exit();

} catch (Exception $e) {
    die("Mailer Error: " . $mail->ErrorInfo);
}
?>