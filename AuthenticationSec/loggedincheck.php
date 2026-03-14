<?php 
//this file will be used to validate if a user is logged in - based on what pages they can access 
//if the user is not logged in 
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: /Loginpage.php");
    exit();
}
?>