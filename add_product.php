<?php
require 'config.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_POST['image'];

    $sql = "INSERT INTO product (Product_Name, Price, Stock, Image)
            VALUES ('$name', '$price', '$stock', '$image')";

    if($conn->query($sql) === TRUE){
        header("Location: admin_products.php");
        exit;
    } else {
        echo "Error adding product: " . $conn->error;
    }
}
?>