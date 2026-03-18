<?php
require 'config.php';

// ===============================
// HANDLE ORDER SUBMISSION (API)
// ===============================
$data = json_decode(file_get_contents("php://input"), true);

if ($data) {

    $cart = $data['cart'];
    $user_id = 1; // TODO: replace with session later

    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    $conn->begin_transaction();

    try {

        // INSERT ORDER
        $stmt = $conn->prepare("
            INSERT INTO orders (User_ID, Total, Status)
            VALUES (?, ?, 'Pending')
        ");
        $stmt->bind_param("id", $user_id, $total);
        $stmt->execute();

        $order_id = $stmt->insert_id;

        // LOOP ITEMS
        foreach ($cart as $item) {

            $product_id = $item['id'];
            $quantity = $item['quantity'];
            $price = $item['price'];

            // CHECK STOCK
            $res = $conn->query("SELECT Stock FROM product WHERE ID = $product_id");
            $row = $res->fetch_assoc();

            if (!$row || $row['Stock'] < $quantity) {
                throw new Exception("Not enough stock for product ID $product_id");
            }

            // INSERT ORDER ITEM
            $stmt = $conn->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            $stmt->execute();

            // UPDATE STOCK
            $stmt = $conn->prepare("
                UPDATE product SET Stock = Stock - ? WHERE ID = ?
            ");
            $stmt->bind_param("ii", $quantity, $product_id);
            $stmt->execute();
        }

        $conn->commit();
        echo "success";

    } catch (Exception $e) {
        $conn->rollback();
        echo "error";
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>

    <link rel="stylesheet" href="Styles/style.css">
    <script src="javascript/checkout.js" defer></script>
</head>

<body>

<div class="checkout-container">

    <h1>Checkout</h1>

    <form id="checkout-form">

        <!-- SHIPPING -->
        <section>
            <h2>Shipping Details</h2>

            <input id="full-name" type="text" placeholder="Full Name">
            <input id="address" type="text" placeholder="Address">

            <div class="checkout-row">
                <input id="city" type="text" placeholder="City">
                <input id="zip" type="text" placeholder="Post Code">
            </div>

            <label id="infolabel" hidden></label>
        </section>

        <!-- PAYMENT -->
        <section>
            <h2>Payment</h2>

            <input id="card-number" type="text" placeholder="Card Number">

            <div class="checkout-row">
                <input id="expiry" type="text" placeholder="MM/YY">
                <input id="cvc" type="text" placeholder="CVC">
            </div>

            <label id="infolabel2" hidden></label>
        </section>

        <button type="submit">Place Order</button>

    </form>

</div>

</body>
</html>