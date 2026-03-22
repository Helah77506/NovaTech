<?php
require 'config.php';

// ===============================
// HANDLE DELETE (AJAX)
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents("php://input"), true);

    if (($data['action'] ?? '') === 'delete') {
        $review_id = (int)$data['review_id'];
        $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->bind_param("i", $review_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Review deleted."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete."]);
        }
        exit;
    }
}

// ===============================
// FETCH REVIEWS
// ===============================
$filterRating  = $_GET['rating']  ?? 'all';
$filterProduct = $_GET['product'] ?? 'all';
$search        = $_GET['search']  ?? '';

$sql = "
    SELECT rv.id, rv.product_id, rv.user_id, rv.rating, rv.comment, rv.created_at,
           p.Product_Name, p.Image,
           u.Full_Name AS user_name
    FROM reviews rv
    JOIN product p ON rv.product_id = p.ID
    JOIN users u ON rv.user_id = u.ID
";

$conditions = [];
$params = [];
$types = "";

if ($filterRating !== 'all') {
    $conditions[] = "rv.rating = ?";
    $params[] = (int)$filterRating;
    $types .= "i";
}

if ($filterProduct !== 'all') {
    $conditions[] = "rv.product_id = ?";
    $params[] = (int)$filterProduct;
    $types .= "i";
}

if ($search) {
    $conditions[] = "(p.Product_Name LIKE ? OR u.Full_Name LIKE ? OR rv.comment LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "sss";
}

if ($conditions) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY rv.created_at DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Product list for filter dropdown
$products = $conn->query("SELECT ID, Product_Name FROM product ORDER BY Product_Name")->fetch_all(MYSQLI_ASSOC);

// Stats
$stats = $conn->query("
    SELECT
        COUNT(*) as total,
        ROUND(AVG(rating), 1) as avg_rating,
        SUM(rating >= 4) as positive,
        SUM(rating <= 2) as negative
    FROM reviews
")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Reviews</title>
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
        <li><a href="Admin_returns.php">Returns</a></li>
        <li><a href="Admin_reviews.php" style="color:white;">Reviews</a></li>
        <li><a href="AuthenticationSec/adminchangepw.php">Change Password</a></li>
    </ul>
</div>

<!-- MAIN -->
<div class="main-content">

    <div class="header">
        <h1>Customer Reviews</h1>
    </div>

    <!-- STATS CARDS -->
    <div class="analytics-grid">
        <div class="card">
            <h3>Total Reviews</h3>
            <p id="stat-total"><?= $stats['total'] ?? 0 ?></p>
        </div>
        <div class="card" style="border-left: 5px solid #3b82f6;">
            <h3>Average Rating</h3>
            <p><?= $stats['avg_rating'] ?? '0.0' ?> ★</p>
        </div>
        <div class="card" style="border-left: 5px solid #10b981;">
            <h3>Positive (4-5★)</h3>
            <p><?= $stats['positive'] ?? 0 ?></p>
        </div>
        <div class="card" style="border-left: 5px solid #dc2626;">
            <h3>Negative (1-2★)</h3>
            <p><?= $stats['negative'] ?? 0 ?></p>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="search-bar" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:20px;">
        <form method="GET" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <input type="text" name="search" placeholder="Search reviews..."
                   value="<?= htmlspecialchars($search) ?>" style="width:220px;">

            <select name="rating" style="padding:8px; border-radius:6px; border:1px solid #ccc;">
                <option value="all" <?= $filterRating === 'all' ? 'selected' : '' ?>>All Ratings</option>
                <option value="5" <?= $filterRating === '5' ? 'selected' : '' ?>>5 Stars</option>
                <option value="4" <?= $filterRating === '4' ? 'selected' : '' ?>>4 Stars</option>
                <option value="3" <?= $filterRating === '3' ? 'selected' : '' ?>>3 Stars</option>
                <option value="2" <?= $filterRating === '2' ? 'selected' : '' ?>>2 Stars</option>
                <option value="1" <?= $filterRating === '1' ? 'selected' : '' ?>>1 Star</option>
            </select>

            <select name="product" style="padding:8px; border-radius:6px; border:1px solid #ccc;">
                <option value="all">All Products</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['ID'] ?>" <?= $filterProduct == $p['ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['Product_Name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Filter</button>
            <a href="Admin_reviews.php"><button type="button" class="secondary">Clear</button></a>
        </form>
    </div>

    <!-- REVIEWS TABLE -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Customer</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reviews)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding:30px; color:#6b7280;">
                        No reviews found.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($reviews as $rv): ?>
                <tr id="review-row-<?= $rv['id'] ?>">
                    <td>#<?= $rv['id'] ?></td>
                    <td style="display:flex; align-items:center; gap:10px;">
                        <?php if ($rv['Image']): ?>
                            <img src="<?= htmlspecialchars($rv['Image']) ?>" class="product-img" alt="">
                        <?php endif; ?>
                        <strong><?= htmlspecialchars($rv['Product_Name']) ?></strong>
                    </td>
                    <td><?= htmlspecialchars($rv['user_name']) ?></td>
                    <td>
                        <span class="star-display">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span style="color: <?= $i <= $rv['rating'] ? '#f59e0b' : '#d1d5db' ?>; font-size:16px;">★</span>
                            <?php endfor; ?>
                        </span>
                        <br><small style="color:#6b7280;"><?= $rv['rating'] ?>/5</small>
                    </td>
                    <td style="max-width:250px;">
                        <?php if ($rv['comment']): ?>
                            <span><?= htmlspecialchars(mb_strimwidth($rv['comment'], 0, 80, '...')) ?></span>
                            <?php if (strlen($rv['comment']) > 80): ?>
                                <button class="secondary" style="font-size:11px; padding:2px 8px; margin-top:4px;"
                                        onclick="showComment(<?= $rv['id'] ?>)">
                                    View Full
                                </button>
                                <input type="hidden" id="comment-<?= $rv['id'] ?>" value="<?= htmlspecialchars($rv['comment']) ?>">
                            <?php endif; ?>
                        <?php else: ?>
                            <small style="color:#6b7280;">No comment</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <small><?= date('d M Y', strtotime($rv['created_at'])) ?></small>
                        <br><small style="color:#6b7280;"><?= date('H:i', strtotime($rv['created_at'])) ?></small>
                    </td>
                    <td>
                        <button class="danger" onclick="deleteReview(<?= $rv['id'] ?>)">
                            Delete
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<script src="javascript/admin_reviews.js"></script>
</body>
</html>