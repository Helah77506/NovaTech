
// PRODUCT LIST 

const products = [
  { id: 1, name: "Epson EB-X49 Classroom Projector", price: 599,
    description: "Bright 3,600-lumen projector designed for classrooms.",
    category: "classroom", image: "Assets/product/epson-projector.jpg" },

  { id: 2, name: "Promethean ActivPanel 65\"", price: 2499,
    description: "65-inch interactive classroom display.",
    category: "classroom", image: "Assets/product/activpanel-65.jpg" },

  { id: 3, name: "IPEVO V4K Document Camera", price: 119,
    description: "Ultra-HD document camera for lessons.",
    category: "classroom", image: "Assets/product/ipevo-v4k.jpg" },

  { id: 4, name: "Logitech R400 Wireless Presenter", price: 39,
    description: "Wireless clicker with 15m range.",
    category: "classroom", image: "Assets/product/logitech-r400.jpg" },

  { id: 5, name: "FrontRow Juno Audio System", price: 799,
    description: "Classroom speaker + teacher mic system.",
    category: "classroom", image: "Assets/product/frontrow-juno.jpg" },

  { id: 6, name: "Dell OptiPlex 7010 Desktop", price: 799,
    description: "Fast i5 desktop for school use.",
    category: "computing", image: "Assets/product/optiplex-7010.jpg" },

  { id: 7, name: "Lenovo ThinkPad E15", price: 899,
    description: "Durable teacher laptop.",
    category: "computing", image: "Assets/product/thinkpad-e15.jpg" },

  { id: 8, name: "Acer Chromebook Spin 311", price: 249,
    description: "Student touchscreen Chromebook.",
    category: "computing", image: "Assets/product/chromebook-spin311.jpg" },

  { id: 9, name: "Apple iPad 9th Gen", price: 329,
    description: "Portable tablet for education.",
    category: "computing", image: "Assets/product/ipad-9.jpg" },

  { id: 10, name: "Synology DS220+ NAS Server", price: 319,
    description: "2-bay storage server for backups.",
    category: "computing", image: "Assets/product/synology-ds220.jpg" },

  { id: 11, name: "Ubiquiti UniFi U6-Lite", price: 99,
    description: "Wi-Fi 6 school access point.",
    category: "networking", image: "Assets/product/unifi-u6lite.jpg" },

  { id: 12, name: "TP-Link 24-Port PoE Switch", price: 249,
    description: "Managed PoE switch for APs and cameras.",
    category: "networking", image: "Assets/product/tplink-24port.jpg" },

  { id: 13, name: "Logitech C920 HD Webcam", price: 59,
    description: "HD webcam for meetings and streaming.",
    category: "networking", image: "Assets/product/logitech-c920.jpg" },

  { id: 14, name: "Poly Studio Video Bar", price: 799,
    description: "All-in-one conferencing bar.",
    category: "networking", image: "Assets/product/poly-studio.jpg" },

  { id: 15, name: "Netgear Nighthawk AX5400", price: 179,
    description: "Wi-Fi 6 router for offices.",
    category: "networking", image: "Assets/product/nighthawk-ax5400.jpg" },

  { id: 16, name: "JBL EON712 Speaker", price: 499,
    description: "Portable PA speaker.",
    category: "audiovisual", image: "Assets/product/jbl-eon712.jpg" },

  { id: 17, name: "Shure BLX14 Lavalier Mic", price: 299,
    description: "Wireless lapel mic system.",
    category: "audiovisual", image: "Assets/product/shure-blx14.jpg" },

  { id: 18, name: "Elite Screens 100\" Electric Screen", price: 269,
    description: "Motorized projector screen.",
    category: "audiovisual", image: "Assets/product/elite-100in.jpg" },

  { id: 19, name: "Samsung 75\" LED Display", price: 1399,
    description: "Large commercial-grade display.",
    category: "audiovisual", image: "Assets/product/samsung-75.jpg" },

  { id: 20, name: "AVer CAM520 Pro2 Camera", price: 799,
    description: "PTZ camera for classrooms.",
    category: "audiovisual", image: "Assets/product/aver-cam520.jpg" },

  { id: 21, name: "HP LaserJet Pro M428fdw", price: 459,
    description: "Multifunction school printer.",
    category: "office", image: "Assets/product/hp-m428.jpg" },

  { id: 22, name: "Fellowes 99Ci Shredder", price: 229,
    description: "Heavy-duty office shredder.",
    category: "office", image: "Assets/product/fellowes-99ci.jpg" },

  { id: 23, name: "Zebra ZD220 Label Printer", price: 159,
    description: "Compact barcode label printer.",
    category: "office", image: "Assets/product/zebra-zd220.jpg" },

  { id: 24, name: "AccuTouch RFID Terminal", price: 199,
    description: "RFID attendance system.",
    category: "office", image: "Assets/product/rfid-terminal.jpg" },

  { id: 25, name: "GBC Fusion 3000L Laminator", price: 89,
    description: "Fast laminator for schools.",
    category: "office", image: "Assets/product/gbc-3000l.jpg" },
];

localStorage.setItem('productsData', JSON.stringify(products));


const container = document.getElementById('productsContainer');
const searchInput = document.getElementById('searchInput');
const cartCountEl = document.getElementById('cartCount');
let activeCategory = 'all';

// display products
function displayProducts(list) {
  container.innerHTML = '';
  if (!list || list.length === 0) {
    container.innerHTML = '<p style="grid-column:1/-1;text-align:center;color:#777">No products found.</p>';
    return;
  }

  list.forEach(p => {
    const card = document.createElement('div');
    card.className = 'product-card';
    card.innerHTML = `
      <img src="${p.image}" alt="${escapeHtml(p.name)}" />
      <h3>${escapeHtml(p.name)}</h3>
      <div class="price">£${p.price.toLocaleString()}</div>
      <button class="details-btn" data-id="${p.id}">View Details</button>
    `;
    container.appendChild(card);
  });

 
  document.querySelectorAll('.details-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = parseInt(btn.dataset.id, 10);
      openModal(id);
    });
  });
}


function escapeHtml(s){ return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }


displayProducts(products);

// category filter
function filterCategory(cat) {
  activeCategory = cat;
  applyFilters();
}


function applyFilters() {
  const q = (searchInput.value || '').trim().toLowerCase();
  let filtered = products.filter(p => p.name.toLowerCase().includes(q) || (p.description || '').toLowerCase().includes(q));
  if (activeCategory !== 'all') filtered = filtered.filter(p => p.category === activeCategory);
  displayProducts(filtered);
}
searchInput.addEventListener('input', applyFilters);

// modal  
const modal = document.getElementById('productModal');
const modalImage = document.getElementById('modalImage');
const modalName = document.getElementById('modalName');
const modalPrice = document.getElementById('modalPrice');
const modalDesc = document.getElementById('modalDesc');
const modalQty = document.getElementById('modalQty');

function openModal(id) {
  const product = products.find(p => p.id === id);
  if (!product) return;
  modalImage.src = product.image || '';
  modalName.textContent = product.name;
  modalPrice.textContent = '£' + product.price.toLocaleString();
  modalDesc.textContent = product.description;
  modalQty.value = 1;
  modal.dataset.productId = id;
  modal.classList.add('open');
  modal.setAttribute('aria-hidden','false');
  document.body.style.overflow = 'hidden';
}
function closeModal() {
  modal.classList.remove('open');
  modal.setAttribute('aria-hidden','true');
  document.body.style.overflow = '';
}
window.addEventListener('click', (e) => {
  if (e.target === modal) closeModal();
});
window.addEventListener('keydown', (e)=> {
  if (e.key === 'Escape') closeModal();
});

//  cart 
function updateCartCount() {
  const cart = JSON.parse(localStorage.getItem('cart') || '[]');
  const total = cart.reduce((s,i)=> s + (i.quantity || 0), 0);
  cartCountEl.textContent = total;
}
function addToCart() {
  const id = parseInt(modal.dataset.productId, 10);
  const qty = Math.max(1, parseInt(modalQty.value, 10) || 1);
  const product = products.find(p => p.id === id);
  if (!product) return alert('Product not found');

  let cart = JSON.parse(localStorage.getItem('cart') || '[]');
  const existing = cart.find(it => it.id === id);
  if (existing) {
    existing.quantity = (existing.quantity || 0) + qty;
  } else {
    cart.push({ id: product.id, name: product.name, price: product.price, image: product.image, quantity: qty });
  }
  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartCount();
  closeModal();
  // small confirmation
  setTimeout(()=> alert('Added to cart'), 50);
}

// unit cart count
updateCartCount();
