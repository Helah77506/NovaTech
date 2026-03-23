<?php 
//this file ensures only admins are able to access admin pages 
require __DIR__ . '/../config.php'; 
session_start();
if($_SESSION['role']!=='admin'){
    header("Location: /novatech/403error.html");
    exit();
}
?>