// ===============================
// RENDER ORDER SUMMARY FROM CART
// ===============================
function renderOrderSummary() {
    const container = document.getElementById("summary-items");
    const cart = JSON.parse(localStorage.getItem("cart") || "[]");

    if (!cart.length) {
        container.innerHTML = '<p class="summary-empty">Your cart is empty.</p>';
        document.getElementById("summary-subtotal").textContent = "£0.00";
        document.getElementById("summary-total").textContent = "£0.00";
        document.getElementById("submit-btn").disabled = true;
        return;
    }

    let subtotal = 0;
    container.innerHTML = cart.map(item => {
        const lineTotal = item.price * item.quantity;
        subtotal += lineTotal;
        return `
            <div class="summary-item">
                <img class="summary-item-img"
                     src="${item.image || 'images/placeholder.png'}"
                     alt="${item.name}">
                <div class="summary-item-info">
                    <div class="summary-item-name">${item.name}</div>
                    <div class="summary-item-qty">Qty: ${item.quantity}</div>
                </div>
                <span class="summary-item-price">£${lineTotal.toFixed(2)}</span>
            </div>`;
    }).join("");

    document.getElementById("summary-subtotal").textContent = "£" + subtotal.toFixed(2);
    document.getElementById("summary-total").textContent = "£" + subtotal.toFixed(2);
}

// ===============================
// INPUT FORMATTING
// ===============================
function setupFormatting() {
    const cardInput = document.getElementById("card-number");
    const expiryInput = document.getElementById("expiry");
    const cvcInput = document.getElementById("cvc");

    // Card number: spaces every 4 digits + card type detection
    cardInput.addEventListener("input", function () {
        let v = this.value.replace(/\D/g, "").substring(0, 16);
        this.value = v.replace(/(.{4})/g, "$1 ").trim();

        const icon = document.getElementById("card-icon");
        if (!icon) return;
        if (/^4/.test(v)) icon.textContent = "💳 Visa";
        else if (/^5[1-5]/.test(v)) icon.textContent = "💳 MC";
        else if (/^3[47]/.test(v)) icon.textContent = "💳 Amex";
        else icon.textContent = "💳";
    });

    // Expiry: auto-insert slash
    expiryInput.addEventListener("input", function () {
        let v = this.value.replace(/\D/g, "").substring(0, 4);
        if (v.length >= 3) v = v.slice(0, 2) + "/" + v.slice(2);
        this.value = v;
    });

    // CVC: digits only
    cvcInput.addEventListener("input", function () {
        this.value = this.value.replace(/\D/g, "").substring(0, 4);
    });
}

// ===============================
// INLINE FIELD VALIDATION HELPER
// ===============================
function showFieldError(input, errId, message) {
    const errEl = document.getElementById(errId);
    input.classList.remove("valid");
    input.classList.add("invalid");
    if (errEl) errEl.textContent = message;
}

function showFieldValid(input, errId) {
    const errEl = document.getElementById(errId);
    input.classList.remove("invalid");
    input.classList.add("valid");
    if (errEl) errEl.textContent = "";
}

function clearFieldState(input, errId) {
    const errEl = document.getElementById(errId);
    input.classList.remove("invalid", "valid");
    if (errEl) errEl.textContent = "";
}

// ===============================
// VALIDATE SHIPPING INPUTS
// ===============================
function validateUserInputs() {
    const full_name = document.getElementById("full-name");
    const address = document.getElementById("address");
    const city = document.getElementById("city");
    const zip = document.getElementById("zip");

    let valid = true;

    if (!full_name.value.trim() || full_name.value.trim().length < 2) {
        showFieldError(full_name, "err-name", "Enter your full name");
        valid = false;
    } else {
        showFieldValid(full_name, "err-name");
    }

    if (!address.value.trim() || address.value.trim().length < 5) {
        showFieldError(address, "err-address", "Enter a valid address");
        valid = false;
    } else {
        showFieldValid(address, "err-address");
    }

    if (!city.value.trim() || city.value.trim().length < 2) {
        showFieldError(city, "err-city", "Enter a city");
        valid = false;
    } else {
        showFieldValid(city, "err-city");
    }

    if (!/^[A-Za-z0-9\s\-]{3,10}$/.test(zip.value.trim())) {
        showFieldError(zip, "err-zip", "Enter a valid post code");
        valid = false;
    } else {
        showFieldValid(zip, "err-zip");
    }

    return valid;
}

// ===============================
// VALIDATE PAYMENT INPUTS
// ===============================
function luhnCheck(num) {
    if (!/^\d{13,19}$/.test(num)) return false;
    let sum = 0, alt = false;
    for (let i = num.length - 1; i >= 0; i--) {
        let n = parseInt(num[i], 10);
        if (alt) { n *= 2; if (n > 9) n -= 9; }
        sum += n;
        alt = !alt;
    }
    return sum % 10 === 0;
}

function validExpiry(v) {
    const parts = v.split("/");
    if (parts.length !== 2) return false;
    const month = parseInt(parts[0], 10);
    const year = parseInt("20" + parts[1], 10);
    if (month < 1 || month > 12) return false;
    const now = new Date();
    const exp = new Date(year, month); // first day of next month
    return exp > now;
}

function validatePaymentInputs() {
    const card = document.getElementById("card-number");
    const expiry = document.getElementById("expiry");
    const cvc = document.getElementById("cvc");

    let valid = true;

    // Card: strip spaces then Luhn check
    const rawCard = card.value.replace(/\s/g, "");
    if (!luhnCheck(rawCard)) {
        showFieldError(card, "err-card", "Enter a valid card number");
        valid = false;
    } else {
        showFieldValid(card, "err-card");
    }

    if (!validExpiry(expiry.value.trim())) {
        showFieldError(expiry, "err-expiry", "Enter a valid expiry (MM/YY)");
        valid = false;
    } else {
        showFieldValid(expiry, "err-expiry");
    }

    if (!/^\d{3,4}$/.test(cvc.value.trim())) {
        showFieldError(cvc, "err-cvc", "Enter a 3 or 4 digit CVC");
        valid = false;
    } else {
        showFieldValid(cvc, "err-cvc");
    }

    return valid;
}

// ===============================
// CHECK STOCK (FRONTEND SAFETY)
// ===============================
function validateStock(cart, products) {
    for (let item of cart) {
        const product = products.find(p => p.id == item.id);

        if (!product || product.stock < item.quantity) {
            alert(`Insufficient stock for ${item.name}`);
            return false;
        }
    }
    return true;
}

// ===============================
// REAL-TIME BLUR VALIDATION
// ===============================
function setupBlurValidation() {
    const fieldMap = [
        { id: "full-name", errId: "err-name",    test: v => v.trim().length >= 2,                      msg: "Enter your full name" },
        { id: "address",   errId: "err-address",  test: v => v.trim().length >= 5,                      msg: "Enter a valid address" },
        { id: "city",      errId: "err-city",     test: v => v.trim().length >= 2,                      msg: "Enter a city" },
        { id: "zip",       errId: "err-zip",      test: v => /^[A-Za-z0-9\s\-]{3,10}$/.test(v.trim()), msg: "Enter a valid post code" },
        { id: "card-number", errId: "err-card",   test: v => luhnCheck(v.replace(/\s/g, "")),           msg: "Enter a valid card number" },
        { id: "expiry",   errId: "err-expiry",    test: v => validExpiry(v),                            msg: "Enter a valid expiry (MM/YY)" },
        { id: "cvc",      errId: "err-cvc",       test: v => /^\d{3,4}$/.test(v),                       msg: "Enter a 3 or 4 digit CVC" },
    ];

    fieldMap.forEach(({ id, errId, test, msg }) => {
        const input = document.getElementById(id);
        if (!input) return;

        // Show error on blur if invalid
        input.addEventListener("blur", () => {
            if (input.value.length === 0) return; // don't nag empty untouched fields
            if (!test(input.value)) showFieldError(input, errId, msg);
            else showFieldValid(input, errId);
        });

        // Clear error while typing once it passes
        input.addEventListener("input", () => {
            if (input.classList.contains("invalid") && test(input.value)) {
                showFieldValid(input, errId);
            }
        });
    });
}

// ===============================
// LOADING STATE HELPERS
// ===============================
function setLoading(on) {
    const btn = document.getElementById("submit-btn");
    const btnText = btn.querySelector(".btn-text");
    const btnSpin = btn.querySelector(".btn-spinner");

    btn.disabled = on;
    btnText.textContent = on ? "Processing…" : "Place Order";
    btnSpin.hidden = !on;
}

// ===============================
// BUILD CONFIRMATION SCREEN (NEW)
// ===============================
function showConfirmation(orderId, orderItems) {
    const orderTotal = orderItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    // Estimated delivery: 5–7 days from now
    const deliveryMin = new Date();
    deliveryMin.setDate(deliveryMin.getDate() + 5);
    const deliveryMax = new Date();
    deliveryMax.setDate(deliveryMax.getDate() + 7);
    const options = { weekday: 'short', day: 'numeric', month: 'short' };
    const deliveryRange = deliveryMin.toLocaleDateString('en-GB', options) + ' – ' + deliveryMax.toLocaleDateString('en-GB', options);

    // Build item rows
    const itemsHTML = orderItems.map(item => `
        <div class="confirm-item">
            <img src="${item.image || 'images/placeholder.png'}" alt="${item.name}" class="confirm-item-img">
            <div class="confirm-item-info">
                <span class="confirm-item-name">${item.name}</span>
                <span class="confirm-item-qty">Qty: ${item.quantity}</span>
            </div>
            <span class="confirm-item-price">£${(item.price * item.quantity).toFixed(2)}</span>
        </div>
    `).join("");

    // Grab shipping info before form is replaced
    const fullName = document.getElementById("full-name").value;
    const address  = document.getElementById("address").value;
    const city     = document.getElementById("city").value;
    const zip      = document.getElementById("zip").value;

    // Replace page content
    document.querySelector(".checkout-wrapper").innerHTML = `
        <div class="confirm-page">

            <div class="confirm-checkmark">
                <svg class="confirm-tick" viewBox="0 0 52 52">
                    <circle class="confirm-tick-circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="confirm-tick-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
            </div>

            <h1 class="confirm-title">Order Confirmed!</h1>
            <p class="confirm-subtitle">Thank you for your purchase</p>

            <div class="confirm-ref">
                Order #<strong>NT-${String(orderId).padStart(5, '0')}</strong>
            </div>

            <div class="confirm-card">
                <div class="confirm-card-header">
                    <h3>Order Summary</h3>
                </div>
                <div class="confirm-items">
                    ${itemsHTML}
                </div>
                <div class="confirm-totals">
                    <div class="confirm-total-line">
                        <span>Subtotal</span>
                        <span>£${orderTotal.toFixed(2)}</span>
                    </div>
                    <div class="confirm-total-line">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <div class="confirm-total-line confirm-grand-total">
                        <span>Total</span>
                        <span>£${orderTotal.toFixed(2)}</span>
                    </div>
                </div>
            </div>

            <div class="confirm-delivery">
                <span class="confirm-delivery-icon"></span>
                <div>
                    <strong>Estimated Delivery</strong>
                    <p>${deliveryRange}</p>
                </div>
            </div>

            <div class="confirm-address">
                <span class="confirm-address-icon"></span>
                <div>
                    <strong>Shipping To</strong>
                    <p>${fullName}</p>
                    <p>${address}, ${city}, ${zip}</p>
                </div>
            </div>

            <div class="confirm-actions">
                <a href="order.php" class="confirm-btn confirm-btn-primary">View My Orders</a>
                <a href="index.php" class="confirm-btn confirm-btn-secondary">Continue Shopping</a>
            </div>

        </div>`;
}

// ===============================
// MAIN SUBMISSION
// ===============================
function listen_Submission() {
    const form = document.getElementById("checkout-form");

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        if (!validateUserInputs() || !validatePaymentInputs()) {
            // Focus first invalid field
            const first = form.querySelector(".invalid");
            if (first) first.focus();
            return;
        }

        const cart = JSON.parse(localStorage.getItem("cart") || "[]");
        const products = JSON.parse(localStorage.getItem("productsData") || "[]");

        if (cart.length === 0) {
            alert("Your cart is empty.");
            return;
        }

        if (!validateStock(cart, products)) return;

        // Show loading
        setLoading(true);

        // Save cart before it gets cleared
        const orderItems = [...cart];

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
            .then(res => res.json())
            .then(data => {

                if (data.status === "success") {

                    // Clear cart
                    localStorage.removeItem("cart");

                    // Update cart count globally
                    if (window.updateCartCount) {
                        window.updateCartCount();
                    }

                    // Show rich confirmation screen
                    showConfirmation(data.order_id, orderItems);

                } else {
                    setLoading(false);
                    alert("Order failed: " + (data.message || "Try again."));
                }
            })
            .catch(err => {
                console.error(err);
                setLoading(false);
                alert("Something went wrong.");
            });
    });
}

// ===============================
// PROGRESS BAR TRACKING
// ===============================
function setupProgressBar() {
    const shippingFields = ['full-name', 'address', 'city', 'zip'];
    const paymentFields  = ['card-number', 'expiry', 'cvc'];

    function checkSection(fieldIds) {
        return fieldIds.every(id => {
            const el = document.getElementById(id);
            return el && el.value.trim().length > 0;
        });
    }

    function updateProgress() {
        const stepShipping = document.getElementById('step-shipping');
        const stepPayment  = document.getElementById('step-payment');
        const stepConfirm  = document.getElementById('step-confirm');
        const line1        = document.getElementById('line-1');
        const line2        = document.getElementById('line-2');

        const shippingDone = checkSection(shippingFields);
        const paymentDone  = checkSection(paymentFields);

        // Shipping step
        if (shippingDone) {
            stepShipping.className = 'progress-step done';
            line1.className = 'progress-line complete';
        } else {
            stepShipping.className = 'progress-step active';
            line1.className = 'progress-line';
        }

        // Payment step
        if (shippingDone && paymentDone) {
            stepPayment.className = 'progress-step done';
            line2.className = 'progress-line complete';
            stepConfirm.className = 'progress-step active';
        } else if (shippingDone) {
            stepPayment.className = 'progress-step active';
            line2.className = 'progress-line';
            stepConfirm.className = 'progress-step';
        } else {
            stepPayment.className = 'progress-step';
            line2.className = 'progress-line';
            stepConfirm.className = 'progress-step';
        }
    }

    // Listen to all fields
    [...shippingFields, ...paymentFields].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', updateProgress);
    });

    updateProgress();
}

// ===============================
// INIT
// ===============================
document.addEventListener("DOMContentLoaded", function () {
    renderOrderSummary();
    setupFormatting();
    setupBlurValidation();
    setupProgressBar();
    listen_Submission();
});