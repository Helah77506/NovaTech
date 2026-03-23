<?php
require 'config.php';
require 'AuthenticationSec/adminlogincheck.php';
session_start();

// SECURITY
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("unauthorized");
}

$order_id = $_GET['id'];

// GET ORDER INFO
$order = $conn->query("
    SELECT orders.*, users.Full_Name
    FROM orders
    JOIN users ON orders.User_ID = users.ID
    WHERE orders.ID = $order_id
")->fetch_assoc();

// GET ORDER ITEMS
$items = $conn->query("
    SELECT order_items.*, product.Product_Name
    FROM order_items
    JOIN product ON order_items.product_id = product.ID
    WHERE order_items.order_id = $order_id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
    <link rel="stylesheet" href="Styles/admin.css">
</head>
<body>

<h1>Order #<?= $order['ID']; ?></h1>

<p><strong>Customer:</strong> <?= $order['Full_Name']; ?></p>
<p><strong>Status:</strong> <?= $order['Status']; ?></p>
<p><strong>Total:</strong> £<?= $order['Total']; ?></p>

<h2>Items</h2>

<table>
<tr>
    <th>Product</th>
    <th>Quantity</th>
    <th>Price</th>
</tr>

<?php while($item = $items->fetch_assoc()): ?>
<tr>
    <td><?= $item['Product_Name']; ?></td>
    <td><?= $item['quantity']; ?></td>
    <td>£<?= $item['price']; ?></td>
</tr>
<?php endwhile; ?>

</table>

<a href="admin_orders.php">⬅ Back</a>

</body>
</html>