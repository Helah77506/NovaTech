<?php
require 'config.php';

// ===============================
// HANDLE ACTIONS (AJAX)
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents("php://input"), true);
    $action = $data['action'] ?? '';

    // --- APPROVE RETURN ---
    if ($action === 'approve') {
        $return_id = (int)$data['return_id'];

        $conn->begin_transaction();
        try {
            // Get return details
            $stmt = $conn->prepare("SELECT product_id, quantity FROM returns WHERE id = ?");
            $stmt->bind_param("i", $return_id);
            $stmt->execute();
            $ret = $stmt->get_result()->fetch_assoc();

            if (!$ret) throw new Exception("Return not found");

            // Update return status
            $stmt = $conn->prepare("UPDATE returns SET status = 'Approved', processed_at = NOW() WHERE id = ?");
            $stmt->bind_param("i", $return_id);
            $stmt->execute();

            // Restore stock
            $stmt = $conn->prepare("UPDATE product SET Stock = Stock + ? WHERE ID = ?");
            $stmt->bind_param("ii", $ret['quantity'], $ret['product_id']);
            $stmt->execute();

            $conn->commit();
            echo json_encode(["status" => "success", "message" => "Return approved. Stock restored by {$ret['quantity']} units."]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit;
    }

    // --- REJECT RETURN ---
    if ($action === 'reject') {
        $return_id = (int)$data['return_id'];
        $stmt = $conn->prepare("UPDATE returns SET status = 'Rejected', processed_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $return_id);
        $stmt->execute();
        echo json_encode(["status" => "success", "message" => "Return rejected."]);
        exit;
    }
}

// ===============================
// FETCH RETURNS
// ===============================
$filter = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

$sql = "
    SELECT r.id, r.order_id, r.product_id, r.user_id, r.quantity, r.reason, r.status, r.created_at, r.processed_at,
           p.Product_Name, p.Image, p.Price,
           u.Full_Name AS user_name
    FROM returns r
    LEFT JOIN product p ON r.product_id = p.ID
    LEFT JOIN users u ON r.user_id = u.ID
";

$conditions = [];
$params = [];
$types = "";

if ($filter !== 'all') {
    $conditions[] = "r.status = ?";
    $params[] = $filter;
    $types .= "s";
}

if ($search) {
    $conditions[] = "(p.Product_Name LIKE ? OR u.Full_Name LIKE ? OR r.order_id = ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = (int)$search;
    $types .= "ssi";
}

if ($conditions) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY r.created_at DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$returns = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Stats
$stats = $conn->query("
    SELECT
        COUNT(*) as total,
        SUM(status = 'Pending') as pending,
        SUM(status = 'Approved') as approved,
        SUM(status = 'Rejected') as rejected
    FROM returns
")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Returns</title>
    <link rel="stylesheet" href="Styles/admin.css">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>NovaTech Admin</h2>
    <ul>
        <li><a href="admin.php">Dashboard</a></li>
        <li><a href="admin_products.php">Products</a></li>
        <li><a href="admin_orders.php">Orders</a></li>
        <li><a href="#">Customers</a></li>
        <li><a href="Admin_returns.php" style="color:white;">Returns</a></li>
        <li><a href="Admin_reviews.php">Reviews</a></li>
        <li><a href="AuthenticationSec/adminchangepw.php">Change Password</a></li>
    </ul>
</div>

<!-- MAIN -->
<div class="main-content">

    <div class="header">
        <h1>Return Requests</h1>
    </div>

    <!-- STATS CARDS -->
    <div class="analytics-grid">
        <div class="card">
            <h3>Total Returns</h3>
            <p><?= $stats['total'] ?? 0 ?></p>
        </div>
        <div class="card warning">
            <h3>Pending</h3>
            <p><?= $stats['pending'] ?? 0 ?></p>
        </div>
        <div class="card" style="border-left: 5px solid #10b981;">
            <h3>Approved</h3>
            <p><?= $stats['approved'] ?? 0 ?></p>
        </div>
        <div class="card" style="border-left: 5px solid #dc2626;">
            <h3>Rejected</h3>
            <p><?= $stats['rejected'] ?? 0 ?></p>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="search-bar" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:20px;">
        <form method="GET" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <input type="text" name="search" placeholder="Search by product, user or order ID..."
                   value="<?= htmlspecialchars($search) ?>" style="width:280px;">

            <select name="status" style="padding:8px; border-radius:6px; border:1px solid #ccc;">
                <option value="all"      <?= $filter === 'all'      ? 'selected' : '' ?>>All Status</option>
                <option value="Pending"   <?= $filter === 'Pending'  ? 'selected' : '' ?>>Pending</option>
                <option value="Approved"  <?= $filter === 'Approved' ? 'selected' : '' ?>>Approved</option>
                <option value="Rejected"  <?= $filter === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>

            <button type="submit">Filter</button>
            <a href="Admin_returns.php"><button type="button" class="secondary">Clear</button></a>
        </form>
    </div>

    <!-- RETURNS TABLE -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Customer</th>
                <th>Order #</th>
                <th>Qty</th>
                <th>Reason</th>
                <th>Requested</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($returns)): ?>
                <tr>
                    <td colspan="9" style="text-align:center; padding:30px; color:#6b7280;">
                        No returns found.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($returns as $r): ?>
                <tr id="return-row-<?= $r['id'] ?>">
                    <td>#<?= $r['id'] ?></td>
                    <td style="display:flex; align-items:center; gap:10px;">
                        <?php if ($r['Image']): ?>
                            <img src="<?= htmlspecialchars($r['Image']) ?>" class="product-img" alt="">
                        <?php endif; ?>
                        <div>
                            <strong><?= htmlspecialchars($r['Product_Name'] ?? 'Unknown Product') ?></strong>
                            <?php if ($r['Price']): ?>
                                <br><small style="color:#6b7280;">£<?= number_format($r['Price'], 2) ?></small>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($r['user_name'] ?? 'Unknown') ?></td>
                    <td>#<?= $r['order_id'] ?></td>
                    <td><?= $r['quantity'] ?></td>
                    <td style="max-width:200px;">
                        <span><?= htmlspecialchars(mb_strimwidth($r['reason'], 0, 60, '...')) ?></span>
                        <?php if (strlen($r['reason']) > 60): ?>
                            <button class="secondary" style="font-size:11px; padding:2px 8px; margin-top:4px;"
                                    onclick="showReason(<?= $r['id'] ?>)">
                                View Full
                            </button>
                            <input type="hidden" id="reason-<?= $r['id'] ?>" value="<?= htmlspecialchars($r['reason']) ?>">
                        <?php endif; ?>
                    </td>
                    <td>
                        <small><?= date('d M Y', strtotime($r['created_at'])) ?></small>
                        <br><small style="color:#6b7280;"><?= date('H:i', strtotime($r['created_at'])) ?></small>
                    </td>
                    <td>
                        <?php
                            $badgeClass = match($r['status']) {
                                'Pending'  => 'pending',
                                'Approved' => 'completed',
                                'Rejected' => 'badge-rejected',
                                default    => 'pending'
                            };
                        ?>
                        <span class="badge <?= $badgeClass ?>" id="badge-<?= $r['id'] ?>"><?= $r['status'] ?></span>
                    </td>
                    <td id="actions-<?= $r['id'] ?>">
                        <?php if ($r['status'] === 'Pending'): ?>
                            <button onclick="processReturn(<?= $r['id'] ?>, 'approve')" style="margin-bottom:4px;">
                                Approve
                            </button>
                            <button class="danger" onclick="processReturn(<?= $r['id'] ?>, 'reject')">
                                Reject
                            </button>
                        <?php else: ?>
                            <small style="color:#6b7280;">
                                <?= $r['processed_at'] ? date('d M Y', strtotime($r['processed_at'])) : '—' ?>
                            </small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<script src="javascript/admin_returns.js"></script>
</body>
</html>