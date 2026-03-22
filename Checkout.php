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
        echo json_encode(["status" => "success", "order_id" => $order_id]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>

    <link rel="stylesheet" href="Styles/style.css">
    <link rel="stylesheet" href="Styles/checkout.css">
    <script src="javascript/checkout.js" defer></script>
</head>

<body>

<div class="checkout-wrapper">

    <a href="index.php" class="back-home-btn">
        <span class="back-arrow-circle"><img src="Assets/Home/arrow.png" alt="Back" class="back-arrow-icon"></span>
        Back to Home
    </a>

    <!-- SECURE BADGE -->
    <div class="secure-badge">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
        </svg>
        Secure Checkout
    </div>

    <h1 class="checkout-title">Checkout</h1>

    <!-- PROGRESS BAR -->
    <div class="progress-bar">
        <div class="progress-step active" id="step-shipping">
            <div class="progress-circle">1</div>
            <span>Shipping</span>
        </div>
        <div class="progress-line" id="line-1"></div>
        <div class="progress-step" id="step-payment">
            <div class="progress-circle">2</div>
            <span>Payment</span>
        </div>
        <div class="progress-line" id="line-2"></div>
        <div class="progress-step" id="step-confirm">
            <div class="progress-circle">3</div>
            <span>Confirm</span>
        </div>
    </div>

    <div class="checkout-layout">

        <!-- ====== LEFT: FORM ====== -->
        <div class="checkout-form-col">
            <form id="checkout-form" novalidate>

                <!-- SHIPPING -->
                <section class="checkout-section">
                    <div class="section-header">
                        <span class="section-step">1</span>
                        <h2>Shipping Details</h2>
                    </div>

                    <div class="field">
                        <label for="full-name">Full Name</label>
                        <input id="full-name" type="text" placeholder="John Smith" autocomplete="name">
                        <span class="field-error" id="err-name"></span>
                    </div>

                    <div class="field">
                        <label for="address">Address</label>
                        <input id="address" type="text" placeholder="123 High Street" autocomplete="street-address">
                        <span class="field-error" id="err-address"></span>
                    </div>

                    <div class="field-row">
                        <div class="field">
                            <label for="city">City</label>
                            <input id="city" type="text" placeholder="London" autocomplete="address-level2">
                            <span class="field-error" id="err-city"></span>
                        </div>
                        <div class="field">
                            <label for="zip">Post Code</label>
                            <input id="zip" type="text" placeholder="SW1A 1AA" autocomplete="postal-code">
                            <span class="field-error" id="err-zip"></span>
                        </div>
                    </div>
                </section>

                <!-- PAYMENT -->
                <section class="checkout-section">
                    <div class="section-header">
                        <span class="section-step">2</span>
                        <h2>Payment</h2>
                    </div>

                    <div class="field">
                        <label for="card-number">Card Number</label>
                        <div class="input-icon-wrap">
                            <input id="card-number" type="text" placeholder="1234 5678 9012 3456"
                                   inputmode="numeric" maxlength="19" autocomplete="cc-number">
                            <span class="card-icon" id="card-icon">💳</span>
                        </div>
                        <span class="field-error" id="err-card"></span>
                    </div>

                    <div class="field-row">
                        <div class="field">
                            <label for="expiry">Expiry</label>
                            <input id="expiry" type="text" placeholder="MM/YY"
                                   inputmode="numeric" maxlength="5" autocomplete="cc-exp">
                            <span class="field-error" id="err-expiry"></span>
                        </div>
                        <div class="field">
                            <label for="cvc">CVC</label>
                            <input id="cvc" type="text" placeholder="123"
                                   inputmode="numeric" maxlength="4" autocomplete="cc-csc">
                            <span class="field-error" id="err-cvc"></span>
                        </div>
                    </div>
                </section>

                <button type="submit" id="submit-btn">
                    <span class="btn-text">Place Order</span>
                    <span class="btn-spinner" hidden></span>
                </button>

                <div class="payment-methods">
                   
                </div>

            </form>
        </div>

        <!-- ====== RIGHT: ORDER SUMMARY ====== -->
        <aside class="checkout-summary-col">
            <section class="checkout-section summary-section">
                <h2>Order Summary</h2>

                <div id="summary-items">
                    <!-- JS fills this -->
                </div>

                <div class="summary-divider"></div>

                <div class="summary-line">
                    <span>Subtotal</span>
                    <span id="summary-subtotal">£0.00</span>
                </div>
                <div class="summary-line">
                    <span>Shipping</span>
                    <span id="summary-shipping">Free</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-line summary-total">
                    <span>Total</span>
                    <span id="summary-total">£0.00</span>
                </div>
            </section>
        </aside>

    </div>
</div>

</body>
</html>