<?php
session_start();
require 'config.php';

require 'AuthenticationSec/adminlogincheck.php';
$order_id = $_POST['order_id'];
$status = $_POST['status'];

$stmt = $conn->prepare("UPDATE orders SET Status=? WHERE ID=?");
$stmt->bind_param("si", $status, $order_id);
$stmt->execute();

header("Location: admin.php");
exit();

