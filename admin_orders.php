<?php
require 'config.php';

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
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="sidebar">
    <h2>NovaTech Admin</h2>
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
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>