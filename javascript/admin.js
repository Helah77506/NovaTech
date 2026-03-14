// function loadSection(section) {
//     const content = document.getElementById("admin-content");

//     switch(section) {

//         case "products":
//             content.innerHTML = `
//                 <h2>Manage Products</h2>
//                 <button onclick="addProduct()">Add Product</button>
//                 <div id="product-list"></div>
//             `;
//             loadProducts();
//             break;

//         case "inventory":
//             content.innerHTML = `
//                 <h2>Inventory Management</h2>
//                 <div id="inventory-list"></div>
//             `;
//             loadInventory();
//             break;

//         case "orders":
//             content.innerHTML = `
//                 <h2>Orders</h2>
//                 <div id="orders-list"></div>
//             `;
//             loadOrders();
//             break;

//         case "customers":
//             content.innerHTML = `
//                 <h2>Customer Management</h2>
//                 <div id="customers-list"></div>
//             `;
//             loadCustomers();
//             break;

//         case "reviews":
//             content.innerHTML = `
//                 <h2>Product Reviews</h2>
//                 <div id="reviews-list"></div>
//             `;
//             loadReviews();
//             break;

//         case "returns":
//             content.innerHTML = `
//                 <h2>Return Requests</h2>
//                 <div id="returns-list"></div>
//             `;
//             loadReturns();
//             break;
//     }
// }


document.addEventListener("DOMContentLoaded", () => {
    console.log("Admin dashboard loaded.");
});