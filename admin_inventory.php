<?php
require 'config.php';


// HANDLE STOCK UPDATE (SAME PAGE)
// ===============================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {

    $product_id = $_POST['product_id'];
    $stock = $_POST['stock'];

    $stmt = $conn->prepare("UPDATE product SET Stock=? WHERE ID=?");
    $stmt->bind_param("ii", $stock, $product_id);

    if ($stmt->execute()) {
        $success = "Stock updated successfully!";
    } else {
        $error = "Failed to update stock.";
    }
}


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
        <li><a href="admin_customers.php">Customers</a></li>
        <li><a href="#">Reviews</a></li>
        <li><a href="#">Returns</a></li>
        <li><a href="AuthenticationSec/adminchangepw.php">Change Password</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>Inventory Management</h1>

    <!-- SUCCESS / ERROR MESSAGE -->
    <?php if (!empty($success)): ?>
        <div style="background:#d4edda; padding:10px; border-radius:6px; color:#155724; margin-bottom:15px;">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div style="background:#f8d7da; padding:10px; border-radius:6px; color:#721c24; margin-bottom:15px;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Stock</th>
            <th>Update Stock</th>
        </tr>

        <?php while($item = $inventory->fetch_assoc()): ?>
        <tr class="
            <?php 
            if ($item['Stock'] == 0) echo 'out-stock';
            elseif ($item['Stock'] < 501) echo 'low-stock';
            ?>
            ">
            <td><?= $item['ID']; ?></td>
            <td><?= $item['Product_Name']; ?></td>
            <td style="color: <?= $item['Stock'] < 501 ? 'orange' : 'green'; ?>">
                <?= $item['Stock']; ?>
            </td>
            <td>
                <form method="POST">
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