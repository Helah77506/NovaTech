<?php 
//this file ensures only admins are able to access admin pages 
require __DIR__ . '/../config.php'; 
session_start();
if(!isset($_SESSION['user_id'])&&$_SESSION['role']!=='admin'){
    header("Location: /novatech/403error.html");
    exit();
}
?>