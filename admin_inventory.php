<?php
require 'config.php';

$inventory = $conn->query("SELECT * FROM product");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory Management</title>
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
        <li><a href="#">Customers</a></li>
        <li><a href="#">Reviews</a></li>
        <li><a href="#">Returns</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>Inventory Management</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Stock</th>
            <th>Update Stock</th>
        </tr>

        <?php while($item = $inventory->fetch_assoc()): ?>
        <tr>
            <td><?= $item['ID']; ?></td>
            <td><?= $item['Product_Name']; ?></td>
            <td style="color: <?= $item['Stock'] < 5 ? 'red' : 'green'; ?>">
                <?= $item['Stock']; ?>
            </td>
            <td>
                <form method="POST" action="update_stock.php">
                    <input type="hidden" name="product_id" value="<?= $item['ID']; ?>">
                    <input type="number" name="stock" required>
                    <button>Update</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>