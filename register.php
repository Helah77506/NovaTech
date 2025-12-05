<?php
session_start();

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

    // Check if email already exists (using correct column name from database)
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT ID FROM users WHERE Email = ?");
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

        // Using correct column names from database schema
        $stmt = $conn->prepare(
            "INSERT INTO users (institution_Name, Full_Name, Email, Password_Hash)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $institution_name, $full_name, $email, $hash);

        if ($stmt->execute()) {
            $stmt->close();
            // Success - redirect to login
            header("Location: Login.html");
            exit;
        } else {
            $errors[] = "Error creating account.";
            $stmt->close();
        }
    }

    // If we reach here, there were errors
    // Store errors in session and redirect back to form
    $_SESSION['registration_errors'] = $errors;
    $_SESSION['old_username'] = $username;
    $_SESSION['old_email'] = $email;
    header("Location: register.html");
    exit;
}

// If not POST request, redirect to registration form
header("Location: register.html");
exit;