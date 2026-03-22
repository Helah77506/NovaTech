function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];

    // ✅ count UNIQUE items only
    const total = cart.length;

    const el = document.getElementById('cartCount');
    if (el) el.textContent = total;
}

// run on every page load
document.addEventListener("DOMContentLoaded", updateCartCount);