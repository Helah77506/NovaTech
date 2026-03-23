<?php
session_start();
require 'Config.php';

// redirect if not logged in
require 'AuthenticationSec/loggedincheck.php';

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// ===============================
// HANDLE RETURN SUBMISSION
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_return'])) {
    $order_id   = intval($_POST['order_id'] ?? 0);
    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity   = intval($_POST['quantity'] ?? 1);
    $reason     = trim($_POST['reason'] ?? '');

    if ($order_id === 0 || $product_id === 0 || $reason === '') {
        $error = 'Please fill in all fields.';
    } elseif ($quantity < 1) {
        $error = 'Quantity must be at least 1.';
    } else {
        // check the user actually owns this order and it contains this product
        $check = $conn->prepare("
            SELECT oi.quantity AS max_qty
            FROM orders o
            INNER JOIN order_items oi ON o.ID = oi.order_id
            WHERE o.ID = ? AND o.User_ID = ? AND oi.product_id = ?
            LIMIT 1
        ");
        $check->bind_param("iii", $order_id, $user_id, $product_id);
        $check->execute();
        $check_result = $check->get_result();
        $row = $check_result->fetch_assoc();
        $check->close();

        if (!$row) {
            $error = 'Order or product not found in your history.';
        } elseif ($quantity > $row['max_qty']) {
            $error = 'You cannot return more than ' . $row['max_qty'] . ' units.';
        } else {
            // check if return already exists for this order + product
            $dup = $conn->prepare("SELECT id FROM returns WHERE order_id = ? AND product_id = ? AND user_id = ?");
            $dup->bind_param("iii", $order_id, $product_id, $user_id);
            $dup->execute();
            $dup->store_result();

            if ($dup->num_rows > 0) {
                $error = 'You have already submitted a return for this item.';
            } else {
                $stmt = $conn->prepare("INSERT INTO returns (order_id, product_id, user_id, quantity, reason) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iiiis", $order_id, $product_id, $user_id, $quantity, $reason);
                if ($stmt->execute()) {
                    $success = 'Return request submitted. We will review it shortly.';
                } else {
                    $error = 'Failed to submit return. Please try again.';
                }
                $stmt->close();
            }
            $dup->close();
        }
    }
}

// ===============================
// FETCH PAST ORDERS WITH ITEMS
// ===============================
$orders_stmt = $conn->prepare("
    SELECT o.ID AS order_id, o.Status AS order_status, o.created_at,
           oi.product_id, oi.quantity, oi.price,
           p.Product_Name, p.Image
    FROM orders o
    INNER JOIN order_items oi ON o.ID = oi.order_id
    INNER JOIN product p ON oi.product_id = p.ID
    WHERE o.User_ID = ?
    ORDER BY o.created_at DESC
");
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();

$orders = [];
while ($row = $orders_result->fetch_assoc()) {
    $oid = $row['order_id'];
    if (!isset($orders[$oid])) {
        $orders[$oid] = [
            'id'     => $oid,
            'status' => $row['order_status'],
            'date'   => $row['created_at'],
            'items'  => []
        ];
    }
    $orders[$oid]['items'][] = $row;
}
$orders_stmt->close();

// ===============================
// FETCH EXISTING RETURNS FOR THIS USER
// ===============================
$my_returns = [];
$ret_stmt = $conn->prepare("
    SELECT r.id, r.order_id, r.product_id, r.quantity, r.reason, r.status, r.created_at,
           p.Product_Name, p.Image, p.Price
    FROM returns r
    LEFT JOIN product p ON r.product_id = p.ID
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$ret_stmt->bind_param("i", $user_id);
$ret_stmt->execute();
$ret_result = $ret_stmt->get_result();
while ($r = $ret_result->fetch_assoc()) {
    $my_returns[] = $r;
}
$ret_stmt->close();

// build a lookup so we know which items already have a return
$returned_items = [];
foreach ($my_returns as $r) {
    $returned_items[$r['order_id'] . '-' . $r['product_id']] = $r['status'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns | NovaTech</title>
    <link rel="stylesheet" href="Styles/Home.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        .returns-wrapper {
            max-width: 960px;
            margin: 40px auto;
            padding: 0 5%;
        }

        /* alerts */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 15px;
        }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error   { background: #f8d7da; color: #721c24; }

        /* tabs */
        .tabs {
            display: flex;
            gap: 0;
            margin-bottom: 24px;
            border-bottom: 2px solid #eee;
        }
        .tab-btn {
            padding: 12px 24px;
            background: none;
            border: none;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            color: #666;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
        }
        .tab-btn.active {
            color: #0d6bcb;
            border-bottom-color: #0d6bcb;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* order blocks */
        .order-block {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        }
        .order-block h3 {
            margin-bottom: 6px;
            font-size: 18px;
        }
        .order-meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 16px;
        }

        /* item rows */
        .item-row {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 0;
            border-top: 1px solid #f0f0f0;
        }
        .item-row img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            background: #f5f5f5;
        }
        .item-info { flex: 1; }
        .item-info strong { font-size: 15px; }
        .item-info p { font-size: 13px; color: #666; margin: 2px 0 0; }

        /* return form per item */
        .return-form {
            display: flex;
            gap: 8px;
            align-items: flex-end;
            flex-wrap: wrap;
            margin-top: 8px;
        }
        .return-form select,
        .return-form input,
        .return-form textarea {
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        .return-form textarea {
            width: 100%;
            min-height: 60px;
            resize: vertical;
        }
        .return-form .btn-submit {
            padding: 8px 18px;
            background: #dc3545;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }
        .return-form .btn-submit:hover { background: #b02a37; }

        /* status badges */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-pending  { background: #fff3cd; color: #856404; }
        .badge-approved { background: #d4edda; color: #155724; }
        .badge-rejected { background: #f8d7da; color: #721c24; }

        .already-returned {
            font-size: 13px;
            padding: 6px 0;
        }

        /* my returns table */
        .returns-list .return-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #fff;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .return-card img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            background: #f5f5f5;
        }
        .return-card .return-info { flex: 1; }
        .return-card .return-info strong { font-size: 15px; }
        .return-card .return-info p { font-size: 13px; color: #666; margin: 2px 0 0; }
        .return-card .return-meta {
            text-align: right;
            font-size: 13px;
        }
        .return-card .return-meta .date { color: #999; }

        .empty-msg {
            text-align: center;
            color: #999;
            padding: 40px 0;
            font-size: 16px;
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="header">
    <img src="Assets/Home/Logo.png" alt="logo" class="logo" />
    <?php require_once __DIR__ . '/topbar.php'; ?>
</header>
<div class="header2"><nav class="nav2"></nav></div>

<div class="returns-wrapper">

    <h1 style="margin-bottom: 24px;">Returns</h1>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- TABS -->
    <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('request')">Request a Return</button>
        <button class="tab-btn" onclick="switchTab('history')">My Returns (<?= count($my_returns) ?>)</button>
    </div>

    <!-- TAB 1: REQUEST A RETURN -->
    <div id="tab-request" class="tab-content active">

        <?php if (empty($orders)): ?>
            <p class="empty-msg">You have no orders yet.</p>
        <?php else: ?>

            <?php foreach ($orders as $order): ?>
                <div class="order-block">
                    <h3>Order #<?= $order['id'] ?></h3>
                    <div class="order-meta">
                        <?= date('d M Y, H:i', strtotime($order['date'])) ?>
                        &nbsp;|&nbsp; Status: <strong><?= htmlspecialchars($order['status']) ?></strong>
                    </div>

                    <?php foreach ($order['items'] as $item): ?>
                        <?php $key = $order['id'] . '-' . $item['product_id']; ?>
                        <div class="item-row">
                            <img src="<?= htmlspecialchars($item['Image']) ?>" alt="<?= htmlspecialchars($item['Product_Name']) ?>">
                            <div class="item-info">
                                <strong><?= htmlspecialchars($item['Product_Name']) ?></strong>
                                <p>Qty: <?= $item['quantity'] ?> &nbsp;|&nbsp; £<?= number_format($item['price'], 2) ?> each</p>

                                <?php if (isset($returned_items[$key])): ?>
                                    <p class="already-returned">
                                        Return status:
                                        <?php
                                            $st = $returned_items[$key];
                                            $cls = match($st) {
                                                'Requested' => 'badge-pending',
                                                'Pending'   => 'badge-pending',
                                                'Approved'  => 'badge-approved',
                                                'Rejected'  => 'badge-rejected',
                                                default     => 'badge-pending'
                                            };
                                        ?>
                                        <span class="badge <?= $cls ?>"><?= htmlspecialchars($st) ?></span>
                                    </p>
                                <?php else: ?>
                                    <!-- Return form for this item -->
                                    <form method="POST" class="return-form">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">

                                        <div>
                                            <label style="font-size:13px;">Qty to return</label>
                                            <input type="number" name="quantity" value="1" min="1" max="<?= $item['quantity'] ?>" style="width:70px;">
                                        </div>

                                        <div style="flex:1; min-width:200px;">
                                            <label style="font-size:13px;">Reason for return</label>
                                            <textarea name="reason" placeholder="Why are you returning this item?" required></textarea>
                                        </div>

                                        <button type="submit" name="submit_return" class="btn-submit">Request Return</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>

    <!-- TAB 2: MY RETURN HISTORY -->
    <div id="tab-history" class="tab-content">

        <?php if (empty($my_returns)): ?>
            <p class="empty-msg">You haven't made any return requests yet.</p>
        <?php else: ?>

            <div class="returns-list">
                <?php foreach ($my_returns as $r): ?>
                    <div class="return-card">
                        <img src="<?= htmlspecialchars($r['Image'] ?? '') ?>" alt="">
                        <div class="return-info">
                            <strong><?= htmlspecialchars($r['Product_Name'] ?? 'Unknown') ?></strong>
                            <p>Order #<?= $r['order_id'] ?> &nbsp;|&nbsp; Qty: <?= $r['quantity'] ?></p>
                            <p>Reason: <?= htmlspecialchars($r['reason']) ?></p>
                        </div>
                        <div class="return-meta">
                            <?php
                                $cls = match($r['status']) {
                                    'Requested' => 'badge-pending',
                                    'Pending'   => 'badge-pending',
                                    'Approved'  => 'badge-approved',
                                    'Rejected'  => 'badge-rejected',
                                    default     => 'badge-pending'
                                };
                            ?>
                            <span class="badge <?= $cls ?>"><?= htmlspecialchars($r['status']) ?></span>
                            <div class="date" style="margin-top:6px;"><?= date('d M Y', strtotime($r['created_at'])) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>

</div>

<!-- Footer -->
<footer class="footer">
    <div class="col">
        <h4>Store Location</h4>
        <p>Aston University<br>Birmingham</p>
        <p>NovaTech@gmail.com<br>07378867181</p>
    </div>
    <div class="col">
        <h4>Shop</h4>
        <a href="productpage.php">Shop All</a>
    </div>
    <div class="col">
        <h4>Support</h4>
        <a href="ContactUs.php">Contact Us</a>
        <a href="aboutpage.php">About Us</a>
    </div>
</footer>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

    document.getElementById('tab-' + tab).classList.add('active');
    event.target.classList.add('active');
}
</script>
<script src="javascript/cartCount.js"></script>
</body>
</html>