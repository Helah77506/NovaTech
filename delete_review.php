<?php
session_start();
require 'config.php';

require 'AuthenticationSec/adminlogincheck.php';

$review_id = $_POST['review_id'];

$stmt = $conn->prepare("DELETE FROM reviews WHERE id=?");
$stmt->bind_param("i", $review_id);
$stmt->execute();

header("Location: admin.php");
exit();