<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="./styles/styles.css">
    <script type="text/javascript" src="./javascript/darkmode.js" defer></script>
    <script src ='./javascript/Checkout.js' ></script>
    <script src="javascript/Checkout.js" defer></script>
    <link rel="stylesheet" href="Styles/style.css" />
</head>

<body>

<form id="checkout-form" action="order.html">

<div class="checkout-container">

 <button type="button" onclick="window.location.href='order.html'">Place Order</button>
    <h1>Checkout</h1>

    <form id="checkoutForm">
        <!-- Shipping Details -->
        <div class="checkout-section">
            <h2>Shipping Details</h2>
            
            <div class="form-group">
                <label for="full-name">Full Name:</label>
                <input type="text" id="full-name" name="full-name" >
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" >
            </div>

            <div class="form-group">
                <label for="city">City:</label>
                <input type="text" id="city" name="city">
            </div>

            <div class="form-group">
                <label for="post-code">Post Code:</label>
                <input type="text" id="post-code" name="post-code" >
            </div>

            <div class="form-group">
                <label for="country">Country:</label>
                <select id="country" name="country">
                    <option value="">Select Country</option>
                    <option value="United States">United States</option>
                    <option value="United Kingdom">United Kingdom</option>
                    <option value="Canada">Canada</option>
                    <option value="Germany">Germany</option>
                    <option value="France">France</option>
                    <option value="Australia">Australia</option>
                </select>
            </div>
        </div>
    <!-- Shipping Info -->
    <section>
        <h2>Shipping Details</h2>

        <label>
            Full Name:
            <input id="full-name" type="text" name="full-name">
        </label>

        <label>
            Address:
            <input id="address" type="text" name="address">
        </label>

        <label>
            City:
            <input id="city" type="text" name="city">
        </label>

        <label>
            Post Code:
            <input id="zip" type="text" name="zip">
        </label>

        <label>
            Country:
            <select name="country">
                <option>United States</option>
                <option>Canada</option>
                <option>United Kingdom</option>
            </select>
        </label>

            <div class="form-group">
                <label for="card-number">Card Number:</label>
                <input type="text" id="card-number" name="card-number" placeholder="1234 5678 9012 3456" maxlength="19">
            </div>

            <div class="form-group">
                <label for="exp-date">Expiration Date:</label>
                <input type="text" id="exp-date" name="exp-date" placeholder="MM/YY" maxlength="5">
            </div>

            <div class="form-group">
                <label for="cvc">CVC:</label>
                <input type="text" id="cvc" name="cvc" placeholder="123" maxlength="4" >
            </div>
        </div>
        <label id="infolabel" hidden></label>
    </section>

    <!-- Payment Info -->
    <section>
        <h2>Payment</h2>

        <label>
            Card Number:
            <input id="card-number" type="text" name="card-number">
        </label>

        <label>
            Expiration Date:
            <input id="expiry" type="text" name="exp-date" placeholder="MM/YY">
        </label>

        <label>
            CVC:
            <input id="cvc" type="text" name="cvc">
        </label>

        <label id="infolabel2" hidden></label>
    </section>

    <button type="submit">Place Order</button>

</div>

</form>

</body>
</html>
