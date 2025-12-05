// order.js - Updated to work with PHP backend

function renderOrderSummary() {
    const container = document.getElementById('orderContainer');
    const totalBox = document.getElementById('orderTotal');
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');

    if (!cart.length) {
        container.innerHTML = "<p>Your cart is empty.</p>";
        totalBox.innerHTML = "";
        return;
    }

    let total = 0;
    container.innerHTML = "";

    cart.forEach(item => {
        const row = document.createElement('div');
        row.className = "cart-row";

        const subtotal = item.quantity * item.price;
        total += subtotal;

        row.innerHTML = `
            <div class="cart-img"><img src="${item.image}" alt="${item.name}" /></div>
            <div class="cart-name">
                <strong>${item.name}</strong>
                <p>${item.description || ""}</p>
            </div>
            <div class="cart-unit">£${item.price.toLocaleString()}</div>
            <div class="cart-qty"><span>${item.quantity}</span></div>
            <div class="cart-sub">£${subtotal.toLocaleString()}</div>
        `;

        container.appendChild(row);
    });

    totalBox.innerHTML = `Total: £${total.toLocaleString()}`;
}

async function placeOrder() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    
    if (!cart.length) {
        alert("Your cart is empty.");
        return;
    }

    // Disable the button to prevent double submission
    const orderBtn = document.querySelector('.order-btn');
    if (orderBtn) {
        orderBtn.disabled = true;
        orderBtn.textContent = 'Processing...';
    }

    try {
        // Send order to PHP backend
        const response = await fetch('place_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cart: cart,
                user_id: getUserId() // Get user ID from session or localStorage
            })
        });

        const result = await response.json();

        if (result.success) {
            alert(`✅ Order placed successfully!\nOrder ID: ${result.order_id}\nTotal: £${result.total.toLocaleString()}\n\nYou'll receive a confirmation email shortly.`);
            
            // Clear the cart
            localStorage.removeItem('cart');
            
            // Redirect to home page or order confirmation page
            window.location.href = "Home.html";
        } else {
            throw new Error(result.message || 'Failed to place order');
        }

    } catch (error) {
        console.error('Error placing order:', error);
        alert(`❌ Error placing order: ${error.message}\nPlease try again.`);
        
        // Re-enable the button
        if (orderBtn) {
            orderBtn.disabled = false;
            orderBtn.textContent = 'Place Order';
        }
    }
}

// Helper function to get user ID
// Modify this based on how you store user information
function getUserId() {
    // Option 1: Get from localStorage
    const userId = localStorage.getItem('userId');
    if (userId) return parseInt(userId);
    
    // Option 2: Get from session (you'll need to create a PHP endpoint for this)
    // Option 3: Default to 1 for testing
    return 1;
}

// Initialize when page loads
document.addEventListener("DOMContentLoaded", renderOrderSummary);
