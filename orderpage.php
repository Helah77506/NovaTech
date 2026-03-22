<?php
require 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Loginpage.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all orders for this user
$orders = $conn->query("
    SELECT * 
    FROM orders
    WHERE User_ID = $user_id
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Orders | NovaTech</title>
  <link rel="stylesheet" href="Styles/order.css" />
</head>

<body>

<!-- HEADER -->
<header class="header">
  <img src="Assets/Home/Logo.png" alt="logo" class="logo" />
  <?php require_once __DIR__ . '/topbar.php';?>
</header>

<div class="header2">
  <nav class="nav2"></nav>
</div>

<!-- HERO -->
<section class="hero">
  <img src="Assets/Home/Hero.png" alt="Orders Banner" />
  <div class="hero-text">
    <h1>Your Orders</h1>
    <p>View all your past purchases and their status.</p>
  </div>
</section>

<!-- MAIN -->
<main class="order-main">
  <h2>Order History</h2>

  <?php if ($orders->num_rows == 0): ?>
    <p>You have no orders yet.</p>
  <?php endif; ?>

  <?php while($order = $orders->fetch_assoc()): ?>

    <div class="order-box">

      <!-- ORDER HEADER -->
      <div class="order-header">
        <div><strong>Order #<?= $order['ID']; ?></strong></div>
        <div>Status: 
          <span class="status <?= strtolower($order['Status']); ?>">
            <?= $order['Status']; ?>
          </span>
        </div>
        <div>Total: £<?= number_format($order['Total'], 2); ?></div>
        <div>Date: <?= $order['created_at']; ?></div>
      </div>

      <!-- ITEMS -->
      <?php
      $items = $conn->query("
        SELECT order_items.quantity,
               order_items.price,
               product.Product_Name,
               product.Image
        FROM order_items
        INNER JOIN product ON order_items.product_id = product.ID
        WHERE order_items.order_id = {$order['ID']}
      ");
      ?>

      <div class="order-items">
        <?php while($item = $items->fetch_assoc()): ?>
          <div class="order-row">

            <div class="order-img">
              <img src="<?= $item['Image']; ?>" alt="">
            </div>

            <div class="order-name">
              <?= $item['Product_Name']; ?>
            </div>

            <div>£<?= $item['price']; ?></div>

            <div><?= $item['quantity']; ?></div>

            <div>£<?= number_format($item['price'] * $item['quantity'], 2); ?></div>

          </div>
        <?php endwhile; ?>
      </div>

    </div>

  <?php endwhile; ?>

</main>

<!-- FOOTER -->
<footer class="footer">
  <div class="col">
    <h4>Store Location</h4>
    <p>Aston University<br>Birmingham</p>
    <p>NovaTech@gmail.com<br>07378867181</p>
  </div>
  <div class="col">
    <a href="productpage.php">Shop All</a>
    <a href="productpage.php">Computers</a>
    <a href="productpage.php">Projectors</a>
    <a href="productpage.php">Smart Boards</a>
  </div>
  <div class="col">
    <h4>Support</h4>
    <a href="ContactUs.php">Contact Us</a>
    <a href="aboutpage.php">About Us</a>
  </div>
</footer>

</body>
</html>