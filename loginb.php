<?php
session_start();
require 'Config.php'; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Login.php');
    exit();
}

$identifier = trim($_POST['identifier'] ?? '');
$password   = $_POST['password'] ?? '';

// basic check
if ($identifier === '' || $password === '') {
    header('Location: Login.php?error=wrong');
    exit();
}

// look for a user with this email or username
$stmt = $conn->prepare(
    "SELECT id, full_name, email, password_hash,Role
     FROM users 
     WHERE email = ? OR full_name = ?"
);

$stmt->bind_param("ss", $identifier, $identifier);
$stmt->execute();
$stmt->store_result();

// found exactly one user
if ($stmt->num_rows === 1) {
    $stmt->bind_result($id, $full_name, $email, $hash,$role);
    $stmt->fetch();

 // check password
    if (password_verify($password, $hash)) {
        $_SESSION['user_id']   = $id;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email']     = $email;
        $_SESSION['role']      =$role;
        //checks the role of the user 
        if($role=='admin'){
            //check whether they are on the first login 
            $stmt2 = $conn->prepare("SELECT firstlogin FROM users where id = ?");
            $stmt2->bind_param("i",$id);
            $stmt2->execute();
            $stmt2->bind_result($firstlogin);
            $stmt2->fetch();
            if($firstlogin==0){
                //redirect to the admin change password 
                header('Location: AuthenticationSec/adminchangepw.php?firstlogin=true');
                exit();
            }
            else{
                header('Location: admin.php');
                exit();
            }
        }
        header('Location: Homepage.php');
        exit();

        
    }
}
//setting session variables
$_SESSION['id'] = $id;

// wrong login
header('Location: Loginpage.php?error=wrong');
exit();
