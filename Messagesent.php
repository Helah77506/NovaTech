<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You – NovaTech</title>
    <link rel="stylesheet" href="Styles/ContactUs.css">
    <style>
        .faq-section {
            padding: 50px 10%;
            background-color: #f4f7f9;
            text-align: left;
        }
        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .faq-item {
            margin-bottom: 25px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .faq-item h3 {
            color: #0d6bcb;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        .faq-item p {
            color: #555;
            line-height: 1.6;
            margin: 0;
        }
    </style>
</head>

<body>

<header class="header">
    <img src="Assets/Home/Logo.png" alt="logo" class="logo" />
    <nav class="nav">
        <a href="Home.php">Home</a>
        <a href="ContactUs.php" class="active">Contact</a>
        <a href="about.php">About Us</a>
        <a href="product.php">Products</a>

        <div class="nav-icons">
            <a href="Login.php">Log in</a>
            <img src="Assets/Home/user.png" alt="Login" />
            <a href="cart.php">Cart</a>
            <img src="Assets/Home/cart .png" alt="Cart" />
        </div>
    </nav>
</header>

<div class="header2"></div>

<section class="contact-form-wrapper" style="padding-bottom: 20px;">
    <div class="contact-form" style="text-align: center;">
        <h2 style="font-size: 2.5rem; margin-bottom: 20px; color: #0d6bcb;">Thank You!</h2>

        <p style="font-size: 1.2rem; margin-bottom: 30px;">
            Your message has been successfully sent.<br>
            Our support team will get back to you shortly.
        </p>

        <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
            <a href="Home.php">
                <button style="width: 250px; padding: 14px 40px; background-color: #0d6bcb; color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer;">
                    Return to Home
                </button>
            </a>

            <a href="MessageHistory.php">
                <button style="width: 250px; padding: 14px 40px; background-color: #333; color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer;">
                    Go to Message History
                </button>
            </a>
        </div>
    </div>
</section>

<section class="faq-section">
    <div class="faq-container">
        <h2 style="text-align: center; margin-bottom: 40px; color: #333;">Frequently Asked Questions</h2>
        
        <div class="faq-item">
            <h3>How long does it take to receive a reply?</h3>
            <p>Our team is dedicated to providing quick support. You can expect a reply to your contact form within 24 hours during business days.</p>
        </div>

        <div class="faq-item">
            <h3>How can I find my order number?</h3>
            <p>Your order number is sent to your email immediately after purchase. You can also find it in the <strong>Order Summary</strong> section of your NovaTech account dashboard.</p>
        </div>

        <div class="faq-item">
            <h3>Do you offer technical support?</h3>
            <p>Yes! If you are having trouble with a product, please include your serial number in your message so our technicians can assist you faster.</p>
        </div>

        <div class="faq-item">
            <h3>Can I track my support ticket?</h3>
            <p>Currently, you can view your message logs in the <strong>Message History</strong> section. Once a representative replies, you will receive an update directly to your email.</p>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="col">
        <h4>Store Location</h4>
        <p>Aston University, Birmingham</p>
        <p>novatech2025nt@gmail.com</p>
    </div>
    <div class="col">
        <h4>Support</h4>
        <a href="ContactUs.php">Contact Us</a>
        <a href="about.php">About Us</a>
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