<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Products - School Inventory</title>
    <link rel="stylesheet" href="Styles/Home.css">
    <link rel="stylesheet" href="Styles/style.css" />
</head>
<body>

<!-- header -->
<header class="header">
    <img src="Assets/Home/Logo.png" alt="logo" class="logo" />
    <?php require_once __DIR__ . '/topbar.php';?>
</header>
<!-- Category Bar -->
<div class="header2">
    <nav class="nav2">
        
    </nav>
</div>

  <!-- search -->
  <div class="search-container">
    <input id="searchInput" type="search" placeholder="Search products..." />
  </div>

  <!-- categories -->
  <div class="category-buttons">
    <button class="cat-btn" onclick="filterCategory('all')">All</button>
    <button class="cat-btn" onclick="filterCategory('classroom')">Classroom Technology</button>
    <button class="cat-btn" onclick="filterCategory('computing')">Computing Equipment</button>
    <button class="cat-btn" onclick="filterCategory('networking')">Networking & Connectivity</button>
    <button class="cat-btn" onclick="filterCategory('audiovisual')">Audio-Visual & Presentation</button>
    <button class="cat-btn" onclick="filterCategory('office')">Office & Administrative</button>
  </div>

  <!-- products -->
  <main>
    <div id="productsContainer" class="product-grid"></div>
  </main>

  <!-- footer -->
  <footer>
    <p>© 2025 NovaTech inventory. All rights reserved.</p>
  </footer>

  <!-- product modal -->
  <div id="productModal" class="modal" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="modalName">
      <button class="close-btn" aria-label="Close" onclick="closeModal()">×</button>

      <div class="modal-preview">
        <img id="modalImage" src="" alt="Product image" />
      </div>

      <div class="modal-info">
        <h2 id="modalName"></h2>
        <div class="price" id="modalPrice"></div>
        <p id="modalDesc"></p>

        <label for="modalQty">Quantity</label>
        <input id="modalQty" type="number" min="1" value="1" />

        <button class="add-cart-btn" onclick="addToCart()">Add to cart</button>
      </div>
    </div>
  </div>

  
  <script src="javascript/product.js"></script>
</body>
</html>
