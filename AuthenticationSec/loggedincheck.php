<?php 
//this file will be used to validate if a user is logged in - based on what pages they can access 
//if the user is not logged in 
require __DIR__ . '/../Config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 
if(!isset($_SESSION['user_id'])){
    header("Location: /NovaTech/Loginpage.php");
    exit();
}
?>