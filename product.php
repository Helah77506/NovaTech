<?php
// Return JSON
header('Content-Type: application/json');

// DB connection
require 'Config.php';

// Get all products
$sql = "SELECT id, name, description, price, category, image FROM product";
$result = $conn->query($sql);

$products = [];

// Convert rows → PHP array
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'price' => (float)$row['price'],
            'category' => $row['category'],
            'image' => $row['image']
        ];
    }
}

// Output as JSON
echo json_encode($products);

$conn->close();
