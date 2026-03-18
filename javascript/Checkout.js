// ===============================
// VALIDATE SHIPPING INPUTS
// ===============================
function validateUserInputs(){
    const full_name = document.getElementById("full-name");
    const address = document.getElementById("address");
    const city = document.getElementById("city");
    const zip = document.getElementById("zip");
    const infolabel = document.getElementById("infolabel");

    const full_nameV = full_name.value.trim();
    const addressV = address.value.trim();
    const cityV = city.value.trim();
    const zipV = zip.value.trim();

    if (full_nameV === "" || addressV === "" || cityV === "" || zipV === "") {
        infolabel.hidden = false;
        infolabel.textContent = "Please ensure all shipping fields are entered";
        return false;
    }

    infolabel.hidden = true;
    return true;
}

// ===============================
// VALIDATE PAYMENT INPUTS
// ===============================
function validatePaymentInputs(){
    const card_number = document.getElementById("card-number");
    const expiry = document.getElementById("expiry");
    const cvc = document.getElementById("cvc");
    const infolabel2 = document.getElementById("infolabel2");

    const card_numberV = card_number.value.trim();
    const expiryV = expiry.value.trim();
    const cvcV = cvc.value.trim();

    const expiryRegex = /^(0[1-9]|1[0-2])\/\d{2}$/;

    if(card_numberV.length !== 16){
        infolabel2.textContent = "Enter a valid 16-digit card number";
        infolabel2.hidden = false;
        return false;
    }
    else if(!expiryRegex.test(expiryV)){
        infolabel2.textContent = "Use MM/YY format for expiry";
        infolabel2.hidden = false;
        return false;
    }
    else if (cvcV.length < 3 || cvcV.length > 4){
        infolabel2.textContent = "Enter a valid CVC";
        infolabel2.hidden = false;
        return false;
    }

    infolabel2.hidden = true;
    return true;
}

// ===============================
// CHECK STOCK (FRONTEND SAFETY)
// ===============================
function validateStock(cart, products){
    for (let item of cart){
        const product = products.find(p => p.id == item.id);

        if (!product || product.stock < item.quantity){
            alert(`Insufficient stock for ${item.name}`);
            return false;
        }
    }
    return true;
}

// ===============================
// MAIN SUBMISSION HANDLER
// ===============================
function listen_Submission(){
    const form = document.querySelector("form");

    form.addEventListener("submit", function(e){
        e.preventDefault();

        const shippingOK = validateUserInputs();
        const paymentOK = validatePaymentInputs();

        if (!shippingOK || !paymentOK) return;

        const cart = JSON.parse(localStorage.getItem("cart") || "[]");
        const products = JSON.parse(localStorage.getItem("productsData") || "[]");

        if (cart.length === 0){
            alert("Your cart is empty.");
            return;
        }

        // Frontend stock check
        if (!validateStock(cart, products)) return;

        // ===============================
        // SEND DATA TO PHP BACKEND
        // ===============================
        fetch("checkout.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                cart: cart,
                full_name: document.getElementById("full-name").value,
                address: document.getElementById("address").value,
                city: document.getElementById("city").value,
                zip: document.getElementById("zip").value
            })
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);

            alert("Order placed successfully!");

            // Clear cart AFTER successful order
            localStorage.removeItem("cart");

            // Redirect to order page
            window.location.href = "order.php";
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Something went wrong. Please try again.");
        });

    });
}

// ===============================
// INIT
// ===============================
document.addEventListener("DOMContentLoaded", listen_Submission);