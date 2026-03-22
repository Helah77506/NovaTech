<?php
session_start();

// Change role temporarily
$_SESSION['role'] = 'customer';

// Redirect to homepage
header("Location: Homepage.php");
exit;
?>