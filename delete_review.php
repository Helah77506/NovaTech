<?php
session_start();
require 'config.php';

if ($_SESSION['role'] !== 'admin') exit("Unauthorized");

$review_id = $_POST['review_id'];

$stmt = $conn->prepare("DELETE FROM reviews WHERE id=?");
$stmt->bind_param("i", $review_id);
$stmt->execute();

header("Location: admin.php");
exit();