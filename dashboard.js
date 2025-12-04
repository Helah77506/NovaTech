function loadPastOrders() {
    fetch("get_orders.php")
        .then(res => {
            if (!res.ok) throw new Error("Not logged in or server error");
            return res.json();
        })
        .then(orders => {
            const container = document.getElementById("ordersContainer");
            const noMsg = document.getElementById("noOrdersMessage");

            container.innerHTML = "";
            if (!orders.length) {
                noMsg.style.display = "block";
                return;
            }

            noMsg.style.display = "none";

            orders.forEach((order, index) => {
                const card = document.createElement("div");
                card.className = "order-card";

                const date = new Date(order.created_at).toLocaleString();
                card.innerHTML = `<h4>Order #${order.order_id} - <span style="font-weight:normal">${date}</span></h4>`;

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
        })
        .catch(err => {
            document.getElementById("ordersContainer").innerHTML = `<p style="color:red;">${err.message}</p>`;
        });
}

document.addEventListener("DOMContentLoaded", loadPastOrders);
