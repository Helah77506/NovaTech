<?php
session_start();
<<<<<<< HEAD
require 'config.php';     // connect to database
=======
>>>>>>> main

// connect to database
require 'Config.php';

// array to store any errors
$errors = [];

// run this only when form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form inputs safely
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if ($username === '' || $email === '' || $password === '') {
        $errors[] = "Please fill in all fields.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    // Check if email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Email already exists.";
        }

        $stmt->close();
    }

    // If no errors insert new user 
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // adjust these if you later add more fields
        $institution_name = '';
        $full_name        = $username;

        $stmt = $conn->prepare(
            "INSERT INTO users (institution_name, full_name, email, password_hash)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $institution_name, $full_name, $email, $hash);

        if ($stmt->execute()) {
            $stmt->close();

            header("Location: Home.html");
            exit;
        } else {
            $errors[] = "Error creating account.";
            $stmt->close();
        }
    }
}

header("Location: register.html");
exit;
