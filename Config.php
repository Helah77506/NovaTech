<?php
// Basic database settings
$host   = "localhost";
$dbname = "novatech";
$user   = "root";
$pass   = "";

// Connect to MySQL using mysqli
$conn = new mysqli($host, $user, $pass, $dbname);

// Stop script if connection fails
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
