<?php
require 'config.php';
require 'AuthenticationSec/adminlogincheck.php';

$orders = $conn->query("
    SELECT orders.ID, users.Full_Name, orders.Total, orders.Status
    FROM orders
    INNER JOIN users ON orders.User_ID = users.ID
    ORDER BY orders.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Management</title>
    <link rel="stylesheet" href="Styles/admin.css">
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

<div class="main-content">
    <h1>Orders</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Update</th>
            <th>Details</th>
        </tr>

        <?php while($order = $orders->fetch_assoc()): ?>
        <tr>
            <td><?= $order['ID']; ?></td>
            <td><?= $order['Full_Name']; ?></td>
            <td>£<?= $order['Total']; ?></td>
            <td><?= $order['Status']; ?></td>
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
            <td>
                <a href="admin_order_details.php?id=<?= $order['ID']; ?>" 
                style="padding:6px 10px; background:#0d6bcb; color:white; border-radius:6px; text-decoration:none;">
                View
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>