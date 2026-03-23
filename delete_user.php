<?php
require 'config.php';
require 'AuthenticationSec/adminlogincheck.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_POST['user_id'];

    // Prevent deleting admins
    $check = $conn->query("SELECT Role FROM users WHERE ID = $user_id");
    $user = $check->fetch_assoc();

    if ($user && $user['Role'] === 'admin') {
        die("Cannot delete admin");
    }

    // Delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE ID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    header("Location: admin_customers.php");
}
?>