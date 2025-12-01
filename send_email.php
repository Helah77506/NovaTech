<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    $to = "novatech2025nt@gmail.com";
    $email_subject = "Contact Form Submission: " . $subject;
    $email_body = "You have received a new message from the contact form on your website.

    Name: $name
    Email: $email
    Subject: $subject
    Message:
    $message
    ";
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";

    if (mail($to, $email_subject, $email_body, $headers)) {
        echo "Thank you for contacting us, $name. We will get back to you shortly.";
    } else {
        echo "Sorry, there was an error sending your message. Please try again later.";
    }
    ?>