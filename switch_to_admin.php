<?php
session_start();

$_SESSION['role'] = 'admin';

header("Location: admin.php");
exit;
?>