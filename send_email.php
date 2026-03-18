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


    $conn = new mysqli("localhost", "root", "", "novatech");

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    $stmt->execute();
    $stmt->close();
    $conn->close();


    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'novatech2025nt@gmail.com';
        $mail->Password   = 'gdgxhunkjduephvb'; // Your App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('novatech2025nt@gmail.com', 'NovaTech Contact Form');
        $mail->addAddress('novatech2025nt@gmail.com');
        $mail->addReplyTo($email, $name);

        $mail->Subject = "Contact Form Message: $subject";
        $mail->Body = "You received a new message:\n\nName: $name\nEmail: $email\nSubject: $subject\n\nMessage:\n$message";

        $mail->send();


        header("Location: MessageSent.php");
        exit();

    } catch (Exception $e) {
        echo "Message could not be sent. Error: {$mail->ErrorInfo}";
    }
}
?>