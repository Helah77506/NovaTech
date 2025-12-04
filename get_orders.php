<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['uid'])) {
    http_response_code(403);
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$uid = $_SESSION['uid'];


$sql = "SELECT o.id AS order_id, o.created_at, oi.product_id, oi.quantity, p.name, p.image, p.price
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];

while ($row = $result->fetch_assoc()) {
    $orderId = $row['order_id'];

    if (!isset($orders[$orderId])) {
        $orders[$orderId] = [
            'order_id' => $orderId,
            'created_at' => $row['created_at'],
            'items' => []
        ];
    }

    $orders[$orderId]['items'][] = [
        'product_id' => $row['product_id'],
        'name'       => $row['name'],
        'image'      => $row['image'],
        'price'      => $row['price'],
        'quantity'   => $row['quantity']
    ];
}

$stmt->close();

echo json_encode(array_values($orders));
