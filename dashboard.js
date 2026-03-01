// dashboard.js

function loadSavedItems() {
    const saved = JSON.parse(localStorage.getItem("savedItems") || "[]");
    const container = document.getElementById("savedContainer");
    const noMsg = document.getElementById("noSavedMessage");

    container.innerHTML = "";

    if (saved.length === 0) {
        noMsg.style.display = "block";
        return;
    }

    noMsg.style.display = "none";

    saved.forEach(item => {
        const card = document.createElement("div");
        card.className = "product-card";
        card.innerHTML = `
            <img src="${item.image}" alt="${item.name}">
            <h3>${item.name}</h3>
            <div class="price">£${item.price.toLocaleString()}</div>
            <button class="details-btn" onclick="window.location.href='product.html'">View Product</button>
        `;
        container.appendChild(card);
    });
}

document.addEventListener("DOMContentLoaded", loadSavedItems);
