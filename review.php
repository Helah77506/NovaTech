<?php
include "Config.php";
session_start();
// ===============================
// GET PRODUCT ID
// ===============================
if (!isset($_GET['id'])) {
    echo "No product selected.";
    exit();
}

$product_id = intval($_GET['id']);

// ===============================
// FETCH PRODUCT
// ===============================
$product_stmt = $conn->prepare("SELECT * FROM product WHERE ID = ?");
$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();

if ($product_result->num_rows === 0) {
    echo "Product not found.";
    exit();
}

$product = $product_result->fetch_assoc();

// ===============================
// HANDLE REVIEW SUBMISSION
// ===============================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // if (!isset($_SESSION['user_id'])) {
    //     header("Location: login.php");
    //     exit();
    // }

    $user_id = $_SESSION['user_id'];
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if ($rating >= 1 && $rating <= 5 && !empty($comment)) {

        $stmt = $conn->prepare("
            INSERT INTO reviews (user_id, product_id, rating, comment, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("iiis", $user_id, $product_id, $rating, $comment);
        $stmt->execute();

        $success = "Review submitted successfully";
    }
}



$review_stmt = $conn->prepare("
    SELECT reviews.*, users.Full_name 
    FROM reviews 
    JOIN users ON reviews.user_id = users.id
    WHERE product_id = ?
    ORDER BY created_at DESC
");
$review_stmt->bind_param("i", $product_id);
$review_stmt->execute();
$reviews = $review_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Reviews</title>

    <link rel="stylesheet" href="Styles/Home.css">
    <link rel="stylesheet" href="styles/review.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body>

<header class="header">
    <img src="Assets/Home/Logo.png" alt="logo" class="logo">
    <?php require_once __DIR__ . '/topbar.php'; ?>
</header>

<div class="container">

    <!-- PRODUCT INFO -->
    <div class="product-box">
        <img src="<?= htmlspecialchars($product['Image']); ?>" alt="Product">

        <div class="product-info">
            <h2><?= htmlspecialchars($product['Product_Name']); ?></h2>
            <p class="price">£<?= htmlspecialchars($product['Price']); ?></p>
            <p><?= htmlspecialchars($product['Product_description']); ?></p>
        </div>
    </div>

    <!-- REVIEW FORM -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <form method="POST" class="review-form">
            <h3>Rate this product</h3>

            <div class="stars">
                <input type="radio" name="rating" id="star5" value="5"><label for="star5">★</label>
                <input type="radio" name="rating" id="star4" value="4"><label for="star4">★</label>
                <input type="radio" name="rating" id="star3" value="3"><label for="star3">★</label>
                <input type="radio" name="rating" id="star2" value="2"><label for="star2">★</label>
                <input type="radio" name="rating" id="star1" value="1"><label for="star1">★</label>
            </div>

            <textarea name="comment" placeholder="Write your review..." required></textarea>

            <button type="submit" class="submit-btn">Submit Review</button>
        </form>
    <?php else: ?>
        <p class="login-msg">Please log in to leave a review.</p>
    <?php endif; ?>

    <!-- REVIEWS -->
    <h3 class="section-title">Customer Reviews</h3>

    <?php if ($reviews->num_rows === 0): ?>
        <p class="no-reviews">No reviews yet.</p>
    <?php endif; ?>

    <?php while($row = $reviews->fetch_assoc()): ?>
        <div class="review-box">
            <strong><?= htmlspecialchars($row['Full_name']); ?></strong>

            <div class="stars-display">
                <?= str_repeat("★", $row['rating']) . str_repeat("☆", 5 - $row['rating']); ?>
            </div>

            <p><?= htmlspecialchars($row['comment']); ?></p>
            <small><?= $row['created_at']; ?></small>
        </div>
    <?php endwhile; ?>

</div>
<script src="javascript/cartCount.js"></script>
</body>
</html>