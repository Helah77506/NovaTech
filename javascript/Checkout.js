// ===============================
// VALIDATE SHIPPING INPUTS
// ===============================
function validateUserInputs(){
    const full_name = document.getElementById("full-name");
    const address = document.getElementById("address");
    const city = document.getElementById("city");
    const zip = document.getElementById("zip");
    const infolabel = document.getElementById("infolabel");

    if (!full_name.value.trim() ||
        !address.value.trim() ||
        !city.value.trim() ||
        !zip.value.trim()) {

        infolabel.hidden = false;
        infolabel.textContent = "Please fill in all shipping fields";
        return false;
    }

    infolabel.hidden = true;
    return true;
}

// ===============================
// VALIDATE PAYMENT INPUTS
// ===============================
function validatePaymentInputs(){
    const card = document.getElementById("card-number");
    const expiry = document.getElementById("expiry");
    const cvc = document.getElementById("cvc");
    const infolabel2 = document.getElementById("infolabel2");

    const expiryRegex = /^(0[1-9]|1[0-2])\/\d{2}$/;

    if (card.value.trim().length !== 16){
        infolabel2.textContent = "Enter a valid 16-digit card number";
        infolabel2.hidden = false;
        return false;
    }

    if (!expiryRegex.test(expiry.value.trim())){
        infolabel2.textContent = "Use MM/YY format";
        infolabel2.hidden = false;
        return false;
    }

    if (cvc.value.trim().length < 3 || cvc.value.trim().length > 4){
        infolabel2.textContent = "Invalid CVC";
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
// MAIN SUBMISSION
// ===============================
function listen_Submission(){
    const form = document.getElementById("checkout-form");

    form.addEventListener("submit", function(e){
        e.preventDefault();

        if (!validateUserInputs() || !validatePaymentInputs()) return;

        const cart = JSON.parse(localStorage.getItem("cart") || "[]");
        const products = JSON.parse(localStorage.getItem("productsData") || "[]");

        if (cart.length === 0){
            alert("Your cart is empty.");
            return;
        }

        if (!validateStock(cart, products)) return;

        // SEND TO PHP
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
        .then(res => res.text())
        .then(data => {

            if (data === "success") {

                alert("Order placed successfully!");

                // clear cart
                localStorage.removeItem("cart");

                // update cart count globally
                if (window.updateCartCount) {
                    window.updateCartCount();
                }

                window.location.href = "order.php";

            } else {
                alert("Order failed. Try again.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Something went wrong.");
        });
    });
}

// ===============================
// INIT
// ===============================
document.addEventListener("DOMContentLoaded", listen_Submission);