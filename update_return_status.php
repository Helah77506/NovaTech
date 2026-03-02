<?php
session_start();
require 'config.php';

if ($_SESSION['role'] !== 'admin') exit("Unauthorized");

$return_id = $_POST['return_id'];
$status = $_POST['status'];

$stmt = $conn->prepare("UPDATE returns SET status=? WHERE id=?");
$stmt->bind_param("si", $status, $return_id);
$stmt->execute();

header("Location: admin.php");
exit();