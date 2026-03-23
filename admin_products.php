<?php
require 'config.php';
require 'AuthenticationSec/adminlogincheck.php';

// Fetch Products
$products = $conn->query("SELECT * FROM product");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Management</title>
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
    <h1>Product Management</h1>
    <br> 

    <h2>Add New Product</h2>

    <form method="POST" action="add_product.php">
        <input type="text" name="name" placeholder="Product Name" required>
        <input type="number" name="price" step="0.01" placeholder="Price" required>
        <input type="number" name="stock" placeholder="Stock" required>
        <input type="text" name="image" placeholder="Image URL">
        <button type="submit">Add Product</button>
    </form>

    <h2 style="margin-top:30px;">All Products</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Stock</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>

        <?php while($row = $products->fetch_assoc()): ?>
        <tr>
            <td><?= $row['ID']; ?></td>
            <td><?= $row['Product_Name']; ?></td>
            <td><?= $row['Stock']; ?></td>
            <td>
                <?php if($row['Image']): ?>
                    <img src="<?= $row['Image']; ?>" width="50">
                <?php endif; ?>
            </td>
            <td>
                <a href="edit_product.php?id=<?= $row['ID']; ?>">Edit</a> |
                <a href="delete_product.php?id=<?= $row['ID']; ?>" 
                onclick="return confirm('Are you sure you want to delete this product?');">
                Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>