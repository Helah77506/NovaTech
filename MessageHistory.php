<?php
// 1. Connect to the database
$conn = new mysqli("localhost", "root", "", "novatech");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Get all messages (Newest first)
$sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message History – NovaTech</title>
    <link rel="stylesheet" href="Styles/ContactUs.css">
    <style>
        /* Extra styling for the table area */
        .history-section {
            padding: 60px 5%;
            background-color: #f9f9f9;
            min-height: 60vh;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-top: 20px;
        }
        .history-table th {
            background-color: #0d6bcb;
            color: white;
            padding: 15px;
            text-align: left;
        }
        .history-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 0.95rem;
            color: #444;
        }
        .history-table tr:hover {
            background-color: #f1f7ff;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #888;
        }
    </style>
</head>
<body>

<header class="header">
    <img src="Assets/Home/Logo.png" alt="logo" class="logo" />
    <nav class="nav">
        <a href="Homepage.php">Home</a>
        <a href="ContactUs.php">Contact</a>
        <a href="aboutpage.php">About Us</a>
        <a href="productpage.php">Products</a>

        <div class="nav-icons">
            <a href="Login.php">Log in</a>
            <img src="Assets/Home/user.png" alt="Login" />
            <a href="cart.php">Cart</a>
            <img src="Assets/Home/cart .png" alt="Cart" />
        </div>
    </nav>
</header>

<div class="header2"></div>

<section class="history-section">
    <h1 style="color: #0d6bcb; margin-bottom: 10px;">Message History</h1>
    <p>Review all messages sent to NovaTech</p>

    <table class="history-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Name</th>
                <th>Email</th>
                <th>Subject</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . date('d M Y, H:i', strtotime($row['created_at'])) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['subject']) . "</td>";
                    echo "<td>" . nl2br(htmlspecialchars($row['message'])) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='no-data'>No messages found in history.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</section>

<footer class="footer">
    <div class="col">
        <h4>Store Location</h4>
        <p>Aston University, Birmingham</p>
        <p>novatech2025nt@gmail.com<br>07378867181</p>
    </div>

    <div class="col">
        <h4>Shop</h4>
        <a href="productpage.php">Shop All</a>
        <a href="productpage.php">Computers</a>
        <a href="productpage.php">Projectors</a>
    </div>

    <div class="col">
        <h4>Support</h4>
        <a href="ContactUs.php">Contact Us</a>
        <a href="aboutpage.php">About Us</a>
    </div>
</footer>

<section class="payment-options">
    <h4>We accept the following payment methods:</h4>
    <div class="payment-icons">
        <img src="Assets/Home/visa.svg">
        <img src="Assets/Home/mastercard.svg">
        <img src="Assets/Home/paypal.svg">
        <img src="Assets/Home/applepay.svg">
    </div>
</section>

</body>
</html>