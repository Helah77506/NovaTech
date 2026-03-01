<?php
session_start();
include "Config.php";

if (!isset($_SESSION['product_id'])) {
    echo "No product selected.";
    exit();
}

$product_id = $_SESSION['product_id'];

$product_stmt = $conn->prepare("SELECT * FROM product WHERE ID = ?");
$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();
$product = $product_result->fetch_assoc();

/* ---------- Handle Review Submission ---------- */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);

    if (!empty($rating) && !empty($comment)) {

        $stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiis", $user_id, $product_id, $rating, $comment);
        $stmt->execute();

        $success = "Review submitted successfully"
    }
}

$productResult = $conn->query("SELECT * FROM products WHERE id = $product_id");
$product = $productResult->fetch_assoc();


$review_stmt = $conn->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC");
$review_stmt->bind_param("i", $product_id);
$review_stmt->execute();
$reviews = $review_stmt->get_result()
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rate & Review</title>
    <link rel="stylesheet" href="review.css" />
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>


 
<body>
<header class="header">
    <img src="Assets/Home/Logo.png" alt="logo" class="logo" />

    <nav class="navbar">
        <a href="Home.html">Home</a>
        <a href="ContactUs.html">Contact</a>
        <a href="about.html">About Us</a>
        <a href="product.html"> Products</a>
	    <a href="#"> Review</a>

        <div class="nav-icons">
            <img src="Assets/Home/user.png" alt="User" />
            <a href="Login.html" class="login">Login</a>

            <img src="Assets/Home/cart .png" alt="Cart" />
        </div>
    </nav>
</header>

    <div class="container">

        <div class="product-header">
            <img src="<?= $product['Image']; ?>" alt="Product">
            <div>
                <h2><?= $product['Product_Name']; ?></h2>
                <p class="price">£<?= $product['Price']; ?></p>
                <p><?= $product['Product_description']; ?></p>
            </div>
        </div>

    <?php if(isset($success)): ?>
        <div class="success"><?= $success; ?></div>
    <?php endif; ?>

    <?php if(isset($_SESSION['user_id'])): ?>
        <form method="POST" class="review-form"></form>
            <h3>Rate this product</h3>

            <div class="stars">
                <input type="radio" name="rating" id="star5"><label for="star5">★</label>
                <input type="radio" name="rating" id="star4"><label for="star4">★</label>
                <input type="radio" name="rating" id="star3"><label for="star3">★</label>
                <input type="radio" name="rating" id="star2"><label for="star2">★</label>
                <input type="radio" name="rating" id="star1"><label for="star1">★</label>
            </div>

            <textarea name = "comment" placeholder="Write your review..." required></textarea>
            <button class="submit-btn">Submit Review</button>
        </form>
    <?php else: ?>
        <p>Please log in to leave a review.</p>
    <?php endif; ?>  

        <h3 class="section-title">Customer Reviews</h3>
        <?php while($row = $reviews->fetch_assoc()): ?>
        <div class="review-box">
            <strong>Rating: <?= $row['rating']; ?>/5</strong>
            <p><?= htmlspecialchars($row['comment']); ?></p>
            <small><?= $row['created_at']; ?></small>
        </div>
    <?php endwhile; ?>
        

    </div>
</body>
</html>