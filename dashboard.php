<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | NovaTech</title>
    <link rel="stylesheet" href="Styles/Home.css">
<link rel="stylesheet" href="Styles/dashboard.css">
    
</head>

<body>

<header class="header">
    <img src="Assets/Home/Logo.png" alt="logo" class="logo" />
    <?php require_once __DIR__ . '/topbar.php';?>
</header>



<div class="header2">
    <nav class="nav2">
        
    </nav>
</div>

<section class="hero">
    <img src="Assets/Home/Hero.png" alt="Dashboard Banner">
    <div class="hero-text">
        <h1>Your Dashboard</h1>
        <p>Welcome back! Here are your recent orders.</p>
    </div>
</section>

<main>
    <section class="dashboard-section">
        <h2>Your Past Orders</h2>
        <div id="ordersContainer" class="orders-grid"></div>
        <p id="noOrdersMessage" class="empty-msg">You have not placed any orders yet.</p>
    </section>
</main>

<footer class="footer">
    <div class="col">
        <h4>Store Location</h4>
        <p>Aston University<br>Birmingham</p>
        <p>NovaTech@gmail.com<br>07378867181</p>
    </div>
    <div class="col">
        <h4>Shop</h4>
           <a href="productpage.php">Shop All</a>
        <a href="productpage.php">Computers</a>
        <a href="productpage.php">Projectors</a>
        <a href="productpage.php">Smart Boards</a>
        <a href="productpage.php">Classroom Audio</a>
        <a href="productpage.php">Wireless Presentation Clickers</a>
        <a href="productpage.php">Chromebooks</a>
        <a href="productpage.php">Tablets</a>
        <a href="productpage.php">Printers and Scanners</a>
    </div>
    <div class="col">
        <h4>Support</h4>
        <a href="ContactUs.php">Contact Us</a>
        <a href="aboutpage.php">About Us</a>
    </div>
</footer>

<script src="javascript/dashboard.js"></script>
</body>
</html>
