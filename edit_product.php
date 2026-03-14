<?php
require 'config.php';

// Get product ID
if(!isset($_GET['id'])){
    echo "No product selected";
    exit;
}

$id = $_GET['id'];

// Fetch product
$product_query = $conn->query("SELECT * FROM product WHERE ID = $id");
$product = $product_query->fetch_assoc();

if(!$product){
    echo "Product not found";
    exit;
}

// Update product
if(isset($_POST['update_product'])){

    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_POST['image'];

    $conn->query("
        UPDATE product 
        SET 
        Product_Name = '$name',
        Price = '$price',
        Stock = '$stock',
        Image = '$image'
        WHERE ID = $id
    ");

    header("Location: admin_products.php");
}
?>

<!DOCTYPE html>
<html>

<head>
<title>Edit Product</title>
<link rel="stylesheet" href="Styles/admin.css">
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>NovaTech Admin</h2>
    <ul>
        <li><a href="admin.php">Dashboard</a></li>
        <li><a href="admin_products.php">Products</a></li>
        <li><a href="admin_inventory.php">Inventory</a></li>
        <li><a href="admin_orders.php">Orders</a></li>
        <li><a href="#">Customers</a></li>
        <li><a href="#">Reviews</a></li>
        <li><a href="#">Returns</a></li>
    </ul>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

<h1>Edit Product</h1>

<form method="POST">

    <label>Product Name</label>
    <input type="text" name="name" value="<?= $product['Product_Name']; ?>" required>

    <label>Price</label>
    <input type="number" step="0.01" name="price" value="<?= $product['Price']; ?>" required>

    <label>Stock</label>
    <input type="number" name="stock" value="<?= $product['Stock']; ?>" required>

    <label>Image URL</label>
    <input type="text" name="image" value="<?= $product['Image']; ?>">

    <br><br>

    <button type="submit" name="update_product">Update Product</button>

</form>

</div>

</body>
</html>