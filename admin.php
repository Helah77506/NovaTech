<?php
require 'config.php';
//require 'AuthenticationSec/adminlogincheck.php';

/* =============================
   ANALYTICS
============================= */

$revenue = $conn->query("
    SELECT SUM(Total) AS totalRevenue
    FROM orders
    WHERE Status IN ('Processing','Shipped','Completed')
")->fetch_assoc()['totalRevenue'] ?? 0;

$totalOrders = $conn->query("
    SELECT COUNT(*) AS total FROM orders
")->fetch_assoc()['total'];

$pendingOrders = $conn->query("
    SELECT COUNT(*) AS total 
    FROM orders 
    WHERE Status='Pending'
")->fetch_assoc()['total'];

$totalCustomers = $conn->query("
    SELECT COUNT(*) AS total 
    FROM users 
    WHERE Role='customer'
")->fetch_assoc()['total'];

$lowStock = $conn->query("
    SELECT COUNT(*) AS total 
    FROM product 
    WHERE Stock < 501
")->fetch_assoc()['total'];

$orderStatusData = [];
$orderStatusQuery = $conn->query("
    SELECT Status, COUNT(*) as count
    FROM orders
    GROUP BY Status
");

while($row = $orderStatusQuery->fetch_assoc()){
    $orderStatusData[$row['Status']] = $row['count'];
}

$recentOrders = $conn->query("
    SELECT orders.ID,
           users.Full_Name,
           orders.Total,
           orders.Status
    FROM orders
    INNER JOIN users ON orders.User_ID = users.ID
    ORDER BY orders.created_at DESC
    LIMIT 5
");

$reviews = $conn->query("
    SELECT reviews.id,
           users.Full_Name,
           product_Name AS product_name,
           reviews.rating,
           reviews.comment
    FROM reviews
    INNER JOIN users ON reviews.user_id = users.ID
    INNER JOIN product ON reviews.product_id = product.ID
    ORDER BY reviews.created_at DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>NovaTech Admin Dashboard</title>
    <link rel="stylesheet" href="Styles/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>NovaTech Admin</h2>
    <ul>
        <li><a href="admin.php">Dashboard</a></li>
        <li><a href="admin_products.php">products</a></li>
        <li><a href="admin_inventory.php">Inventory</a></li>
        <li><a href="admin_orders.php">Orders</a></li>
        <li><a href="admin_customers.php">Customers</a></li>
        <li><a href="Admin_reviews.php">Reviews</a></li>
        <li><a href="Admin_returns.php">Returns</a></li>
        <li><a href="switch_to_customer.php">View as Customer</a></li>
        <li><a href="AuthenticationSec/adminchangepw.php">Change Password</a></li>
    </ul>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <div class="header">
        <h1>Dashboard Overview</h1>
    </div>

    <!-- ANALYTICS CARDS -->
    <div class="analytics-grid">

        <div class="card">
            <h3>Total Revenue</h3>
            <p>£<?= number_format($revenue,2); ?></p>
        </div>

        <div class="card">
            <h3>Total Orders</h3>
            <p><?= $totalOrders; ?></p>
        </div>

        <div class="card">
            <h3>Pending Orders</h3>
            <p><?= $pendingOrders; ?></p>
        </div>

        <div class="card">
            <h3>Total Customers</h3>
            <p><?= $totalCustomers; ?></p>
        </div>

        <div class="card warning">
            <h3>Low Stock Products</h3>
            <p><?= $lowStock; ?></p>
        </div>

    </div>

    <!-- CHART -->
    <div class="chart-container">
        <canvas id="ordersChart"></canvas>
    </div>

    <!-- RECENT ORDERS -->
    <h2 style="margin:20px 0;">Recent Orders</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Update</th>
        </tr>

        <?php while($order = $recentOrders->fetch_assoc()): ?>
        <tr>
            <td><?= $order['ID']; ?></td>
            <td><?= $order['Full_Name']; ?></td>
            <td>£<?= $order['Total']; ?></td>
            <td>
                <span class="badge <?= strtolower($order['Status']); ?>">
                    <?= $order['Status']; ?>
                </span>
            </td>
            <td>
                <form method="POST" action="update_order_status.php">
                    <input type="hidden" name="order_id" value="<?= $order['ID']; ?>">
                    <select name="status">
                        <option>Pending</option>
                        <option>Processing</option>
                        <option>Shipped</option>
                        <option>Completed</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- REVIEWS -->
    <h2 style="margin:30px 0 15px;">Recent Reviews</h2>

    <table>
        <tr>
            <th>User</th>
            <th>Product</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Action</th>
        </tr>

        <?php while($review = $reviews->fetch_assoc()): ?>
        <tr>
            <td><?= $review['Full_Name']; ?></td>
            <td><?= $review['product_name']; ?></td>
            <td><?= $review['rating']; ?>/5</td>
            <td><?= $review['comment']; ?></td>
            <td>
                <form method="POST" action="delete_review.php">
                    <input type="hidden" name="review_id" value="<?= $review['id']; ?>">
                    <button class="danger">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>

<!-- CHART SCRIPT -->
<script>
const ctx = document.getElementById('ordersChart').getContext('2d');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Pending','Processing','Shipped','Completed'],
        datasets: [{
            label: 'Orders',
            data: [
                <?= $orderStatusData['Pending'] ?? 0 ?>,
                <?= $orderStatusData['Processing'] ?? 0 ?>,
                <?= $orderStatusData['Shipped'] ?? 0 ?>,
                <?= $orderStatusData['Completed'] ?? 0 ?>
            ]
        }]
    }
});
</script>

</body>
</html>