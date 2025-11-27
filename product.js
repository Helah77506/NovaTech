// ===============================
// PRODUCT LIST (25 ITEMS)
// ===============================
const products = [
    // Classroom Technology
    { id: 1, name: "Epson EB-X49 Classroom Projector", price: 599,
      description: "Bright 3,600-lumen projector designed for classrooms.",
      category: "classroom", image: "images/epson-projector.jpg" },

    { id: 2, name: "Promethean ActivPanel 65\"", price: 2499,
      description: "65-inch interactive classroom display.",
      category: "classroom", image: "images/activpanel-65.jpg" },

    { id: 3, name: "IPEVO V4K Document Camera", price: 119,
      description: "Ultra-HD document camera for lessons.",
      category: "classroom", image: "images/ipevo-v4k.jpg" },

    { id: 4, name: "Logitech R400 Wireless Presenter", price: 39,
      description: "Wireless clicker with 15m range.",
      category: "classroom", image: "images/logitech-r400.jpg" },

    { id: 5, name: "FrontRow Juno Audio System", price: 799,
      description: "Classroom speaker + teacher mic system.",
      category: "classroom", image: "images/frontrow-juno.jpg" },

    // Computing Equipment
    { id: 6, name: "Dell OptiPlex 7010 Desktop", price: 799,
      description: "Fast i5 desktop for school use.",
      category: "computing", image: "images/optiplex-7010.jpg" },

    { id: 7, name: "Lenovo ThinkPad E15", price: 899,
      description: "Durable teacher laptop.",
      category: "computing", image: "images/thinkpad-e15.jpg" },

    { id: 8, name: "Acer Chromebook Spin 311", price: 249,
      description: "Student touchscreen Chromebook.",
      category: "computing", image: "images/chromebook-spin311.jpg" },

    { id: 9, name: "Apple iPad 9th Gen", price: 329,
      description: "Portable tablet for education.",
      category: "computing", image: "images/ipad-9.jpg" },

    { id: 10, name: "Synology DS220+ NAS Server", price: 319,
      description: "2-bay storage server for backups.",
      category: "computing", image: "images/synology-ds220.jpg" },

    // Networking & Connectivity
    { id: 11, name: "Ubiquiti UniFi U6-Lite", price: 99,
      description: "Wi-Fi 6 school access point.",
      category: "networking", image: "images/unifi-u6lite.jpg" },

    { id: 12, name: "TP-Link 24-Port PoE Switch", price: 249,
      description: "Managed PoE switch for APs and cameras.",
      category: "networking", image: "images/tplink-24port.jpg" },

    { id: 13, name: "Logitech C920 HD Webcam", price: 59,
      description: "HD webcam for meetings and streaming.",
      category: "networking", image: "images/logitech-c920.jpg" },

    { id: 14, name: "Poly Studio Video Bar", price: 799,
      description: "All-in-one conferencing bar.",
      category: "networking", image: "images/poly-studio.jpg" },

    { id: 15, name: "Netgear Nighthawk AX5400", price: 179,
      description: "Wi-Fi 6 router for offices.",
      category: "networking", image: "images/nighthawk-ax5400.jpg" },

    // Audio-Visual & Presentation
    { id: 16, name: "JBL EON712 Speaker", price: 499,
      description: "Portable PA speaker.",
      category: "audiovisual", image: "images/jbl-eon712.jpg" },

    { id: 17, name: "Shure BLX14 Lavalier Mic", price: 299,
      description: "Wireless lapel mic system.",
      category: "audiovisual", image: "images/shure-blx14.jpg" },

    { id: 18, name: "Elite Screens 100\" Electric Screen", price: 269,
      description: "Motorized projector screen.",
      category: "audiovisual", image: "images/elite-100in.jpg" },

    { id: 19, name: "Samsung 75\" LED Display", price: 1399,
      description: "Large commercial-grade display.",
      category: "audiovisual", image: "images/samsung-75.jpg" },

    { id: 20, name: "AVer CAM520 Pro2 Camera", price: 799,
      description: "PTZ camera for classrooms.",
      category: "audiovisual", image: "images/aver-cam520.jpg" },

    // Office & Administrative
    { id: 21, name: "HP LaserJet Pro M428fdw", price: 459,
      description: "Multifunction school printer.",
      category: "office", image: "images/hp-m428.jpg" },

    { id: 22, name: "Fellowes 99Ci Shredder", price: 229,
      description: "Heavy-duty office shredder.",
      category: "office", image: "images/fellowes-99ci.jpg" },

    { id: 23, name: "Zebra ZD220 Label Printer", price: 159,
      description: "Compact barcode label printer.",
      category: "office", image: "images/zebra-zd220.jpg" },

    { id: 24, name: "AccuTouch RFID Terminal", price: 199,
      description: "RFID attendance system.",
      category: "office", image: "images/rfid-terminal.jpg" },

    { id: 25, name: "GBC Fusion 3000L Laminator", price: 89,
      description: "Fast laminator for schools.",
      category: "office", image: "images/gbc-3000l.jpg" }
];

// Save globally (used by modal + cart + other pages)
localStorage.setItem("productsData", JSON.stringify(products));


// ===============================
// PRODUCT DISPLAY
// ===============================
const container = document.getElementById("productsContainer");
let activeCategory = "all";

function displayProducts(list) {
    container.innerHTML = "";

    if (list.length === 0) {
        container.innerHTML = "<p>No products found.</p>";
        return;
    }

    list.forEach(p => {
        container.innerHTML += `
            <div class="product-card">
                <img src="${p.image}" alt="${p.name}">
                <h3>${p.name}</h3>
                <p class="price">₦${p.price.toLocaleString()}</p>
                <button class="details-btn" onclick="openModal(${p.id})">View Details</button>
            </div>
        `;
    });
}

// Initial load
displayProducts(products);


// ===============================
// CATEGORY FILTERING
// ===============================
function filterCategory(cat) {
    activeCategory = cat;
    applyFilters();
}


// ===============================
// SEARCH + CATEGORY FILTER
// ===============================
function applyFilters() {
    const searchTerm = document.getElementById("searchInput").value.toLowerCase();

    let filtered = products.filter(p =>
        p.name.toLowerCase().includes(searchTerm)
    );

    if (activeCategory !== "all") {
        filtered = filtered.filter(p => p.category === activeCategory);
    }

    displayProducts(filtered);
}

document.getElementById("searchInput").addEventListener("input", applyFilters);


// ===============================
// MODAL (PRODUCT DETAILS POPUP)
// ===============================
function openModal(id) {
    const product = products.find(p => p.id === id);

    document.getElementById("modalImage").src = product.image;
    document.getElementById("modalName").textContent = product.name;
    document.getElementById("modalPrice").textContent = "₦" + product.price.toLocaleString();
    document.getElementById("modalDesc").textContent = product.description;

    document.getElementById("modalQty").value = 1;

    document.getElementById("productModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("productModal").style.display = "none";
}

// Close when clicking outside
window.onclick = function(e) {
    const modal = document.getElementById("productModal");
    if (e.target === modal) closeModal();
};


// ===============================
// ADD TO CART (placeholder)
// ===============================
function addToCart() {
    alert("Item added to cart! (Cart system integrates next)");
}
