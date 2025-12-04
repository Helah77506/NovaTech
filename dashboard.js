// dashboard.js

function loadPastOrders() {
    const orders = JSON.parse(localStorage.getItem("pastOrders") || "[]");
    const container = document.getElementById("ordersContainer");
    const noMsg = document.getElementById("noOrdersMessage");

    container.innerHTML = "";

    if (!orders.length) {
        noMsg.style.display = "block";
        return;
    }

    noMsg.style.display = "none";

    orders.reverse().forEach((order, index) => {
        const card = document.createElement("div");
        card.className = "order-card";

        const date = new Date(order.timestamp).toLocaleString();

        card.innerHTML = `<h4>Order #${orders.length - index} - <span style="font-weight:normal">${date}</span></h4>`;

        const itemsContainer = document.createElement("div");
        itemsContainer.className = "order-items";

        order.items.forEach(item => {
            const itemDiv = document.createElement("div");
            itemDiv.className = "order-item";
            itemDiv.innerHTML = `
                <img src="${item.image}" alt="${item.name}" />
                <div>
                    <strong>${item.name}</strong><br>
                    Qty: ${item.quantity} — £${(item.price * item.quantity).toLocaleString()}
                </div>
            `;
            itemsContainer.appendChild(itemDiv);
        });

        card.appendChild(itemsContainer);
        container.appendChild(card);
    });
}

document.addEventListener("DOMContentLoaded", loadPastOrders);

