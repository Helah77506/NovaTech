<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = htmlspecialchars($_POST['name']);
    $email   = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'novatech2025nt@gmail.com';
        $mail->Password   = 'gdgxhunkjduephvb';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->setFrom('novatech2025nt@gmail.com', 'NovaTech Contact Form');
        $mail->addAddress('novatech2025nt@gmail.com');
        $mail->addReplyTo($email, $name);
        $mail->Subject = "Contact Form Message: $subject";
        $mail->Body =
            "You received a new message from the contact form:\n\n" .
            "Name: $name\n" .
            "Email: $email\n" .
            "Subject: $subject\n\n" .
            "Message:\n$message";

        $mail->send();
        header)("Location: MessageSent.html");
        exit();
    } 

    catch (Exception $e) {
        echo "Message could not be sent. Error: {$mail->ErrorInfo}";
    }
}
?>