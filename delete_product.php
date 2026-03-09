<?php
require 'config.php';

// Check if ID exists
if(!isset($_GET['id'])){
    echo "No product selected";
    exit;
}

$id = $_GET['id'];

// Delete product
$conn->query("DELETE FROM product WHERE ID = $id");

// Redirect back to products page
header("Location: admin_products.php");
exit;
?>