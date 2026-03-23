<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<nav class="nav">
    <a href="Homepage.php">Home</a>      
    <a href="ContactUs.php">Contact</a>
    <a href="aboutpage.php">About</a>    
    <a href="profile.php" class="login">Account Details</a>   
    <a href="productpage.php">Products</a>
    
    <a href="cart.php" style="position: relative;">
        🛒Cart
        <span id="cartCount" style="
    position: absolute;
    top: -8px;
    right: -10px;
    background: red;
    color: white;
    font-size: 12px;
    padding: 2px 6px;
    border-radius: 50%;
  ">0</span>
</a>

    <div class="nav-icons">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="Loginpage.php" class="login">Log in</a>
        <?php else: ?>
            <a href="Logout.php" class="login">Log out</a>
        <?php endif; ?>

        <img src="Assets/Home/user.png" alt="User" />
        <?php if (isset($_SESSION['role']) && strtolower(trim($_SESSION['role'])) !== 'customer'): ?>
            <a href="switch_to_admin.php">Back to Admin</a>
        <?php endif; ?>
    </div>

    
</nav>