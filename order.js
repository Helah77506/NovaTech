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
      <div class="cart-img">
        <img src="${item.image}" alt="${item.name}" />
      </div>
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

function placeOrder() {
  const cart = JSON.parse(localStorage.getItem('cart') || '[]');
  if (!cart.length) return alert("Your cart is empty.");

  alert("✅ Order placed successfully!\nYou’ll receive a confirmation email shortly.");
  localStorage.removeItem('cart');
  window.location.href = "Home.html";
}

document.addEventListener("DOMContentLoaded", renderOrderSummary);
