<?php

require 'config.php';

// Read JSON from JS
$data = json_decode(file_get_contents("php://input"), true);

if ($data) {

    $cart = $data['cart'];
    $user_id = 1; // replace with session later

    $total = 0;
    foreach($cart as $item){
        $total += $item['price'] * $item['quantity'];
    }

    // Insert order
    $conn->query("
        INSERT INTO orders (User_ID, Total, Status)
        VALUES ('$user_id', '$total', 'Pending')
    ");

    $order_id = $conn->insert_id;

    // Insert items + update stock
    foreach($cart as $item){

        $product_id = $item['id'];
        $quantity = $item['quantity'];
        $price = $item['price'];

        // Save item
        $conn->query("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES ('$order_id', '$product_id', '$quantity', '$price')
        ");

        // Update stock
        $conn->query("
            UPDATE product
            SET Stock = Stock - $quantity
            WHERE ID = $product_id
        ");
    }

    echo "success";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>

    <link rel="stylesheet" href="Styles/style.css">
    <script src="javascript/checkout.js" defer></script>
</head>

<body>

<div class="checkout-container">


 <button type="button" onclick="window.location.href='order.php'">Place Order</button>

    <h1>Checkout</h1>

    <!-- ONLY ONE FORM -->
    <form id="checkout-form">

        <h2>Shipping Details</h2>

        <input id="full-name" type="text" placeholder="Full Name">
        <input id="address" type="text" placeholder="Address">
        <input id="city" type="text" placeholder="City">
        <input id="zip" type="text" placeholder="Post Code">

        <h2>Payment</h2>

        <input id="card-number" type="text" placeholder="Card Number">
        <input id="expiry" type="text" placeholder="MM/YY">
        <input id="cvc" type="text" placeholder="CVC">

        <label id="infolabel" hidden></label>
        <label id="infolabel2" hidden></label>

        <br><br>
        <button type="submit">Place Order</button>

    </form>

</div>

</body>
</html>