// cart.js
const cartContainer = document.getElementById('cartContainer');
const cartSummary = document.getElementById('cartSummary');
const clearCartBtn = document.getElementById('clearCartBtn');
const checkoutBtn = document.getElementById('checkoutBtn');

function loadCart() {
  return JSON.parse(localStorage.getItem('cart') || '[]');
}

function saveCart(cart) {
  localStorage.setItem('cart', JSON.stringify(cart));
  // update global cart count )
  try { window.parent && window.parent.updateCartCount && window.parent.updateCartCount(); } catch(e){}
}

function renderCart() {
  const cart = loadCart();
  cartContainer.innerHTML = '';
  if (!cart.length) {
    cartContainer.innerHTML = '<p style="color:#666">Your cart is empty.</p>';
    cartSummary.innerHTML = '';
    return;
  }

  // header row
const list = document.createElement('div');
list.className = "cart-header";

list.innerHTML = `
    <div class="cart-img-head"></div>
    <div class="cart-name-head">Item</div>
    <div class="cart-unit-head">Unit Price</div>
    <div class="cart-qty-head">Quantity</div>
    <div class="cart-sub-head">Subtotal</div>
    <div class="cart-remove-head"></div>
`;

cartContainer.appendChild(list);
  ;

  let total = 0;

cart.forEach(item => {
  const row = document.createElement('div');
  row.className = "cart-row";

  row.innerHTML = `
    <div class="cart-img"><img src="${item.image}" alt="${item.name}"></div>

    <div class="cart-name">
        <strong>${item.name}</strong>
        <p>${item.description || ""}</p>
    </div>

    <div class="cart-unit">£${item.price.toLocaleString()}</div>

    <div class="cart-qty">
        <button class="dec-btn" data-id="${item.id}">-</button>
        <input type="number" min="1" value="${item.quantity}" data-id="${item.id}" class="qty-input">
        <button class="inc-btn" data-id="${item.id}">+</button>
    </div>

    <div class="cart-sub">£${(item.price * item.quantity).toLocaleString()}</div>

    <div class="cart-remove">
        <button class="remove-btn" data-id="${item.id}">Remove</button>
    </div>
  `;

  cartContainer.appendChild(row);
  total += item.price * item.quantity;
});

cartSummary.innerHTML = `
  <div style="font-weight:700;font-size:1.15rem">
    Total: £${total.toLocaleString()}
  </div>
`;

attachCartListeners();

}

function attachCartListeners() {
  document.querySelectorAll('.inc-btn').forEach(b => b.addEventListener('click', (e) => {
    const id = parseInt(e.currentTarget.dataset.id,10);
    changeQty(id, 1);
  }));
  document.querySelectorAll('.dec-btn').forEach(b => b.addEventListener('click', (e) => {
    const id = parseInt(e.currentTarget.dataset.id,10);
    changeQty(id, -1);
  }));
  document.querySelectorAll('.qty-input').forEach(inp => inp.addEventListener('change', (e) => {
    const id = parseInt(e.currentTarget.dataset.id,10);
    let val = parseInt(e.currentTarget.value,10) || 1;
    if (val < 1) val = 1;
    setQty(id, val);
  }));
  document.querySelectorAll('.remove-btn').forEach(b => b.addEventListener('click', (e) => {
    const id = parseInt(e.currentTarget.dataset.id,10);
    removeItem(id);
  }));
}

function changeQty(id, delta) {
  const cart = loadCart();
  const idx = cart.findIndex(i => i.id === id);
  if (idx === -1) return;
  cart[idx].quantity = Math.max(1, (cart[idx].quantity || 1) + delta);
  saveCart(cart);
  renderCart();
  updateHeaderCartCount();
}

function setQty(id, qty) {
  const cart = loadCart();
  const idx = cart.findIndex(i => i.id === id);
  if (idx === -1) return;
  cart[idx].quantity = qty;
  saveCart(cart);
  renderCart();
  updateHeaderCartCount();
}

function removeItem(id) {
  let cart = loadCart();
  cart = cart.filter(i => i.id !== id);
  saveCart(cart);
  renderCart();
  updateHeaderCartCount();
}

function clearCart() {
  if (!confirm('Clear the cart?')) return;
  localStorage.removeItem('cart');
  renderCart();
  updateHeaderCartCount();
}

function checkout() {
  // placeholder action - replace with real checkout
  const cart = loadCart();
  if (!cart.length) { alert('Cart is empty.'); return; }
  window.location.href='Checkout.html';
}

// header cart count update
function updateHeaderCartCount() {
  const cart = loadCart();
  const total = cart.reduce((s,i)=> s + (i.quantity || 0), 0);
  const el = document.getElementById('cartCount');
  if (el) el.textContent = total;
  saveCart(cart);
}

clearCartBtn.addEventListener('click', clearCart);
checkoutBtn.addEventListener('click', checkout);

// initial render
renderCart();
updateHeaderCartCount();
