<?php
require 'config.php';

$inventory = $conn->query("SELECT * FROM product");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory Management</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="sidebar">
    <h2>NovaTech Admin</h2>
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
            <td><?= $item['Name']; ?></td>
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