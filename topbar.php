<?php
session_start();
?>


<nav class="nav">
    <a href="Homepage.php">Home</a>      
    <a href="ContactUs.php">Contact</a>
    <a href="aboutpage.php">About</a>       
    <a href="productpage.php">Products</a>

    <div class="nav-icons">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="Loginpage.php" class="login">Log in</a>
        <?php else: ?>
            <a href="Logout.php" class="login">Log out</a>
        <?php endif; ?>

        <img src="Assets/Home/user.png" alt="User" />
        <a href="cartpage.php">Cart</a>
        <img src="Assets/Home/cart .png" alt="Cart" />
    </div>
</nav>