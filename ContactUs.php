<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovaTech – Contact</title>
    <link rel="stylesheet" href="Styles/ContactUs.css">
</head>

<body>


<header class="header">
    <img src="Assets/Home/Logo.png" alt="logo" class="logo" />
    <?php require_once __DIR__ . '/topbar.php';?>
</header>


<div class="header2">
    <nav class="nav2">

    </nav>
</div>


<section class="contact-hero">
    <img src="Assets/Home/Hero.png" alt="Contact Hero Image">

    <div class="contact-hero-text">
        <h1>Get in Touch With Us</h1>
        <p>We're here to help with all your technology needs</p>
        
    </div>
</section>

<section id="contact-form" class="contact-form-wrapper">
    <div class="contact-form">
        <h2>Send us a message</h2>

        <form action="send_email.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="subject" placeholder="Subject" required>
            <textarea name="message" rows="6" placeholder="Message" required></textarea>
            
            <button type="submit">Send Message</button>
        </form>
    </div>
</section>

<footer class="footer">
    <div class="col">
        <h4>Store Location</h4>
        <p>Aston University<br>Birmingham</p>
        <p>novatech2025nt@gmail.com<br>07378867181</p>
    </div>

    <div class="col">
        <h4>Shop</h4>
        <a href="product.php">Shop All</a>
        <a href="product.php">Computers</a>
        <a href="product.php">Projectors</a>
        <a href="product.php">Smart Boards</a>
        <a href="product.php">Classroom Audio</a>
        <a href="product.php">Wireless Clickers</a>
        <a href="product.php">Chromebooks</a>
        <a href="product.php">Tablets</a>
    </div>

    <div class="col">
        <h4>Support</h4>
        <a href="ContactUs.php">Contact Us</a>
        <a href="about.php">About Us</a>
    </div>
</footer>

<section class="payment-options">
    <h4>We accept the following paying methods:</h4>
   
    <div class="payment-icons">
        <img src="Assets/Home/visa.svg">
        <img src="Assets/Home/mastercard.svg">
        <img src="Assets/Home/paypal.svg">
        <img src="Assets/Home/amex.svg">
        <img src="Assets/Home/jcb.svg">
        <img src="Assets/Home/unionpay.svg">
        <img src="Assets/Home/googlepay.svg">
        <img src="Assets/Home/applepay.svg">
    </div>
</section>

</body>
</html>