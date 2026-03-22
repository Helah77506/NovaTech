<?php
require 'config.php';
session_start();

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

// Quick stats
$total_orders = $orders->num_rows;
$total_spent_q = $conn->query("SELECT SUM(Total) AS spent FROM orders WHERE User_ID = $user_id");
$total_spent = $total_spent_q->fetch_assoc()['spent'] ?? 0;

$pending_q = $conn->query("SELECT COUNT(*) AS c FROM orders WHERE User_ID = $user_id AND Status = 'Pending'");
$pending_count = $pending_q->fetch_assoc()['c'] ?? 0;

$completed_q = $conn->query("SELECT COUNT(*) AS c FROM orders WHERE User_ID = $user_id AND Status = 'Completed'");
$completed_count = $completed_q->fetch_assoc()['c'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Orders | NovaTech</title>
  <link rel="stylesheet" href="Styles/Home.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    /* ---- LAYOUT ---- */
    .orders-wrapper {
      max-width: 1000px;
      margin: 0 auto;
      padding: 40px 5%;
    }

    /* ---- STATS ---- */
    .stats-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 14px;
      margin-bottom: 30px;
    }
    .stat-card {
      background: #fff;
      border-radius: 12px;
      padding: 18px 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      text-align: center;
    }
    .stat-card .stat-number { font-size: 28px; font-weight: 700; }
    .stat-card .stat-label  { font-size: 13px; color: #888; margin-top: 4px; }
    .stat-card.blue   .stat-number { color: #0d6bcb; }
    .stat-card.green  .stat-number { color: #16a34a; }
    .stat-card.orange .stat-number { color: #d97706; }
    .stat-card.purple .stat-number { color: #7c3aed; }

    /* ---- ACTION BAR ---- */
    .action-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
      margin-bottom: 24px;
    }
    .action-bar h2 {
      font-size: 22px;
      margin: 0;
    }
    .action-buttons {
      display: flex;
      gap: 10px;
    }
    .btn-outline {
      padding: 9px 18px;
      background: none;
      border: 1.5px solid #0d6bcb;
      color: #0d6bcb;
      border-radius: 8px;
      font-weight: 600;
      font-size: 14px;
      text-decoration: none;
      transition: all 0.2s;
      cursor: pointer;
    }
    .btn-outline:hover { background: #0d6bcb; color: #fff; }
    .btn-outline.red { border-color: #dc3545; color: #dc3545; }
    .btn-outline.red:hover { background: #dc3545; color: #fff; }

    /* ---- FILTER TABS ---- */
    .filter-tabs {
      display: flex;
      gap: 8px;
      margin-bottom: 24px;
      flex-wrap: wrap;
    }
    .filter-tab {
      padding: 8px 16px;
      border-radius: 999px;
      border: 1.5px solid #ddd;
      background: #fff;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      color: #666;
      transition: all 0.2s;
    }
    .filter-tab:hover { border-color: #0d6bcb; color: #0d6bcb; }
    .filter-tab.active { background: #0d6bcb; color: #fff; border-color: #0d6bcb; }

    /* ---- ORDER CARDS ---- */
    .order-card {
      background: #fff;
      border-radius: 14px;
      margin-bottom: 20px;
      box-shadow: 0 2px 14px rgba(0,0,0,0.06);
      overflow: hidden;
      transition: transform 0.15s;
    }
    .order-card:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(0,0,0,0.09); }
    .order-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 18px 24px;
      background: #fafbfc;
      border-bottom: 1px solid #f0f0f0;
      flex-wrap: wrap;
      gap: 10px;
    }
    .order-card-header h3 { font-size: 17px; margin: 0; }
    .order-meta-row {
      display: flex;
      gap: 18px;
      align-items: center;
      font-size: 13px;
      color: #666;
    }
    .order-meta-row .meta-item {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    /* ---- STATUS BADGES ---- */
    .status-badge {
      display: inline-block;
      padding: 4px 14px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 600;
    }
    .status-pending    { background: #fff3cd; color: #856404; }
    .status-processing { background: #cce5ff; color: #004085; }
    .status-shipped    { background: #d4edda; color: #155724; }
    .status-completed  { background: #d1ecf1; color: #0c5460; }

    /* ---- ORDER ITEMS ---- */
    .order-items-list { padding: 0 24px 16px; }
    .order-item {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 14px 0;
      border-bottom: 1px solid #f5f5f5;
    }
    .order-item:last-child { border-bottom: none; }
    .order-item-img {
      width: 64px;
      height: 64px;
      object-fit: cover;
      border-radius: 10px;
      background: #f5f5f5;
      flex-shrink: 0;
    }
    .order-item-info { flex: 1; }
    .order-item-info strong { font-size: 15px; display: block; margin-bottom: 3px; }
    .order-item-info .item-meta { font-size: 13px; color: #888; }
    .order-item-price {
      text-align: right;
      flex-shrink: 0;
    }
    .order-item-price .unit { font-size: 13px; color: #888; }
    .order-item-price .subtotal { font-size: 15px; font-weight: 700; color: #333; }

    /* ---- ORDER FOOTER ---- */
    .order-card-footer {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      padding: 14px 24px;
      background: #fafbfc;
      border-top: 1px solid #f0f0f0;
      gap: 16px;
    }
    .order-total {
      font-size: 17px;
      font-weight: 700;
      color: #0d6bcb;
    }

    /* ---- EMPTY STATE ---- */
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #aaa;
    }
    .empty-state .empty-icon { font-size: 52px; margin-bottom: 14px; }
    .empty-state p { font-size: 16px; }
    .empty-state .empty-sub { font-size: 14px; color: #ccc; margin-top: 6px; }
    .empty-state .btn-shop {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 28px;
      background: #0d6bcb;
      color: #fff;
      text-decoration: none;
      border-radius: 10px;
      font-weight: 600;
      transition: background 0.2s;
    }
    .empty-state .btn-shop:hover { background: #0b5aa7; }

    /* ---- RESPONSIVE ---- */
    @media (max-width: 600px) {
      .order-card-header { flex-direction: column; align-items: flex-start; }
      .order-meta-row { flex-wrap: wrap; }
      .order-item { flex-wrap: wrap; }
      .order-item-price { width: 100%; text-align: left; margin-top: 6px; }
      .stats-row { grid-template-columns: repeat(2, 1fr); }
      .action-bar { flex-direction: column; align-items: flex-start; }
    }
  </style>
</head>

<body>

<header class="header">
  <img src="Assets/Home/Logo.png" alt="logo" class="logo" />
  <?php require_once __DIR__ . '/topbar.php';?>
</header>

<div class="header2">
  <nav class="nav2"></nav>
</div>

<section class="hero">
  <img src="Assets/Home/Hero.png" alt="Orders Banner" />
  <div class="hero-text">
    <h1>My Orders</h1>
    <p>View all your past purchases and track their status.</p>
  </div>
</section>

<div class="orders-wrapper">

  

  <!-- Action Bar -->
  <div class="action-bar">
    <h2>Order History</h2>
    <div class="action-buttons">
      <a href="customer_returns.php" class="btn-outline red">Return a Product</a>
      <a href="productpage.php" class="btn-outline">Continue Shopping</a>
    </div>
  </div>

  <!-- Filter Tabs -->
  <div class="filter-tabs">
    <button class="filter-tab active" onclick="filterOrders('all', this)">All</button>
    <button class="filter-tab" onclick="filterOrders('pending', this)">Pending</button>
    <button class="filter-tab" onclick="filterOrders('processing', this)">Processing</button>
    <button class="filter-tab" onclick="filterOrders('shipped', this)">Shipped</button>
    <button class="filter-tab" onclick="filterOrders('completed', this)">Completed</button>
  </div>

  <!-- Orders -->
  <?php if ($total_orders === 0): ?>
    <div class="empty-state">
      <div class="empty-icon">&#128230;</div>
      <p>You haven't placed any orders yet.</p>
      <p class="empty-sub">Start shopping and your orders will appear here.</p>
      <a href="productpage.php" class="btn-shop">Browse Products</a>
    </div>
  <?php else: ?>

    <?php while($order = $orders->fetch_assoc()): ?>
      <?php
        $statusLower = strtolower($order['Status']);
        $statusCls = match($statusLower) {
          'pending'    => 'status-pending',
          'processing' => 'status-processing',
          'shipped'    => 'status-shipped',
          'completed'  => 'status-completed',
          default      => 'status-pending'
        };
      ?>
      <div class="order-card" data-status="<?= $statusLower ?>">
        <div class="order-card-header">
          <h3>Order #<?= $order['ID'] ?></h3>
          <div class="order-meta-row">
            <span class="meta-item"><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></span>
            <span class="status-badge <?= $statusCls ?>"><?= $order['Status'] ?></span>
          </div>
        </div>

        <div class="order-items-list">
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

          <?php while($item = $items->fetch_assoc()): ?>
            <div class="order-item">
              <img class="order-item-img" src="<?= htmlspecialchars($item['Image']) ?>" alt="<?= htmlspecialchars($item['Product_Name']) ?>">
              <div class="order-item-info">
                <strong><?= htmlspecialchars($item['Product_Name']) ?></strong>
                <div class="item-meta">Qty: <?= $item['quantity'] ?> &middot; £<?= number_format($item['price'], 2) ?> each</div>
              </div>
              <div class="order-item-price">
                <div class="subtotal">£<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>

        <div class="order-card-footer">
          <span class="order-total">Total: £<?= number_format($order['Total'], 2) ?></span>
        </div>
      </div>
    <?php endwhile; ?>

  <?php endif; ?>

</div>

<!-- Footer -->
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
  </div>
  <div class="col">
    <h4>Support</h4>
    <a href="ContactUs.php">Contact Us</a>
    <a href="aboutpage.php">About Us</a>
  </div>
</footer>

<section class="payment-options">
  <h4>We accept the following paying methods:</h4>
  <div class="payment-icons">
    <img src="Assets/Home/visa.svg">
    <img src="Assets/Home/mastercard.svg">
    <img src="Assets/Home/paypal.svg">
    <img src="Assets/Home/amex.svg">
  </div>
</section>

<script>
function filterOrders(status, btn) {
  document.querySelectorAll('.filter-tab').forEach(function(t) { t.classList.remove('active'); });
  btn.classList.add('active');

  document.querySelectorAll('.order-card').forEach(function(card) {
    if (status === 'all' || card.dataset.status === status) {
      card.style.display = '';
    } else {
      card.style.display = 'none';
    }
  });
}
</script>
<script src="javascript/cartCount.js"></script>
</body>
</html>