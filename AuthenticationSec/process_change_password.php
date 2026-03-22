<?php 
//this checks if 
// its the first time a admin has logged in and redirects them to change password if it is
//php logic to change the password 
session_start();
require __DIR__ . '/../config.php'; 

$userid = $_SESSION['user_id']; // the current admin id to change 
$newpw1 = trim($_POST['new_password']);
$newpw2  = trim($_POST['confirm_password']);
$hashednewpw = password_hash($newpw1, PASSWORD_DEFAULT);

//extra input validation 
if ($newpw1 === '') {
    die("Password cannot be empty");
}

if($newpw1!==$newpw2){
    die("Passwords do not match");
}
//to ensure admins can only request there own password 
//extra input validation to ensure admins can only change password without entering there 
//current password if theyve just logged in 
$getidandlogin = "SELECT ID, Password_Hash, firstlogin FROM users WHERE id = ?";

$data = $conn->prepare($getidandlogin);
$data->bind_param("i", $userid);
$data->execute();

$result = $data->get_result();
$values = $result->fetch_assoc();
//block usaer from any requests on a password which isnt theirs 
if($values['ID']!=$userid){
    die("unauthorised request detected");
}
//check whether current password entered is the same as in the database 
if($values['firstlogin']!==0){
    // NOT first login  must verify current password
    $currentpw = trim($_POST['current_password'] ?? '');

    if ($currentpw === '') {
        die("Current password required");
    }

    if (!password_verify($currentpw, $values['Password_Hash'])) {
        die("Current password is incorrect");
    }
}


//sql statment which changes the password and 
//makes it so first login is not = 1 - so admins arnt asked to change again
$sql = "UPDATE users 
        SET Password_Hash = ?, firstlogin = 1 
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $hashednewpw, $userid);
$stmt->execute();
// redirect after success
header("Location: ../admin.php");
exit();
?>