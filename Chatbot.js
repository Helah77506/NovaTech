alert("NEW CHATBOT JS FILE LOADED");
const chatToggle = document.getElementById("chatToggle");
const chatbot = document.getElementById("chatbot");
const closeChat = document.getElementById("closeChat");
const sendBtn = document.getElementById("sendBtn");
const userInput = document.getElementById("userInput");
const chatBody = document.getElementById("chatBody");

if (chatToggle && chatbot && closeChat && sendBtn && userInput && chatBody) {
    chatToggle.addEventListener("click", () => {
        chatbot.style.display = "flex";
    });

    closeChat.addEventListener("click", () => {
        chatbot.style.display = "none";
    });

    sendBtn.addEventListener("click", sendMessage);

    userInput.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            sendMessage();
        }
    });

    addQuickReplies();
}

function sendMessage() {
    const userText = userInput.value.trim();
    if (userText === "") return;

    addMessage(userText, "user-message");
    userInput.value = "";

    setTimeout(() => {
        const botReply = getBotResponse(userText);
        addMessage(botReply, "bot-message");
    }, 450);
}

function addMessage(text, className) {
    const messageDiv = document.createElement("div");
    messageDiv.classList.add("message", className);
    messageDiv.textContent = text;
    chatBody.appendChild(messageDiv);
    chatBody.scrollTop = chatBody.scrollHeight;
}

function addBotMessages(messages) {
    messages.forEach((msg, index) => {
        setTimeout(() => {
            addMessage(msg, "bot-message");
        }, index * 350);
    });
}

function addQuickReplies() {
    const quickWrap = document.createElement("div");
    quickWrap.classList.add("message", "bot-message");
    quickWrap.style.display = "flex";
    quickWrap.style.flexWrap = "wrap";
    quickWrap.style.gap = "8px";

    const replies = [
        "Track order",
        "Delivery info",
        "Returns policy",
        "Show products"
    ];

    replies.forEach(reply => {
        const btn = document.createElement("button");
        btn.textContent = reply;
        btn.style.border = "none";
        btn.style.padding = "8px 10px";
        btn.style.borderRadius = "8px";
        btn.style.cursor = "pointer";
        btn.style.background = "#222";
        btn.style.color = "#fff";
        btn.style.fontSize = "12px";

        btn.addEventListener("click", () => {
            addMessage(reply, "user-message");

            setTimeout(() => {
                const botReply = getBotResponse(reply);
                addMessage(botReply, "bot-message");
            }, 350);
        });

        quickWrap.appendChild(btn);
    });

    chatBody.appendChild(quickWrap);
}

function getBotResponse(input) {
    const text = input.toLowerCase();

    // greetings
    if (
        text.includes("hello") ||
        text.includes("hi") ||
        text.includes("hey") ||
        text.includes("good morning") ||
        text.includes("good afternoon")
    ) {
        return "Hello and welcome to NovaTech. I can help with products, prices, delivery, returns, payments, and orders.";
    }

    // thanks
    if (
        text.includes("thank you") ||
        text.includes("thanks") ||
        text.includes("cheers")
    ) {
        return "You're welcome. Let me know if you need help with anything else.";
    }

    
    if (
        text.includes("help") ||
        text.includes("what can you do") ||
        text.includes("how can you help")
    ) {
        return "I can help you find products, explain delivery and returns, guide you to checkout, answer price questions, and point you to support pages.";
    }

    if (
        text.includes("product") ||
        text.includes("item") ||
        text.includes("show products") ||
        text.includes("shop")
    ) {
        return "You can browse all products on the products page. We offer classroom technology, computing equipment, networking products, and audio-visual equipment.";
    }

    
    if (text.includes("classroom")) {
        return "Our classroom technology range includes projectors, smart boards, classroom audio equipment, and presentation tools.";
    }

    if (text.includes("computing")) {
        return "Our computing range includes desktops, laptops, Chromebooks, tablets, and storage devices.";
    }

    if (text.includes("network") || text.includes("networking") || text.includes("wifi")) {
        return "Our networking products include access points, switches, webcams, conferencing gear, and routers.";
    }

    if (text.includes("audio") || text.includes("visual") || text.includes("speaker")) {
        return "Our audio-visual range includes speakers, microphones, video bars, and presentation equipment.";
    }

    
    if (
        text.includes("delivery") ||
        text.includes("shipping") ||
        text.includes("postage")
    ) {
        return "We offer delivery options during checkout. Orders are usually processed before dispatch, and delivery details are shown before payment is completed.";
    }

    
    if (
        text.includes("return") ||
        text.includes("refund") ||
        text.includes("exchange")
    ) {
        return "If you need to return an item, please contact support or use the Contact Us page. Make sure you have your order details ready so the team can help faster.";
    }

    
    if (
        text.includes("track") ||
        text.includes("where is my order") ||
        text.includes("order status") ||
        text.includes("track order")
    ) {
        return "You can track your order from your account or dashboard once it has been placed. If you have an issue, contact support with your order details.";
    }

    if (text.includes("order")) {
        return "If you have already placed an order, you can review your account or dashboard for updates. If you are about to order, add items to your cart and continue to checkout.";
    }

    if (
        text.includes("checkout") ||
        text.includes("pay now") ||
        text.includes("buy now")
    ) {
        return "To complete a purchase, add items to your cart and continue to checkout. There you can review your order, enter delivery details, and choose payment.";
    }

    if (
        text.includes("payment") ||
        text.includes("pay") ||
        text.includes("card") ||
        text.includes("paypal")
    ) {
        return "We support secure payment methods during checkout. Available options are shown before you place the order.";
    }

  
    if (
        text.includes("discount") ||
        text.includes("offer") ||
        text.includes("sale") ||
        text.includes("deal") ||
        text.includes("bulk")
    ) {
        return "NovaTech focuses on educational technology and bulk deals. Check the homepage and products page for current offers and larger order options.";
    }

    if (
        text.includes("contact") ||
        text.includes("support") ||
        text.includes("email") ||
        text.includes("phone")
    ) {
        return "You can contact the NovaTech team through the Contact Us page for direct support with orders, returns, or general enquiries.";
    }

    if (
        text.includes("login") ||
        text.includes("log in") ||
        text.includes("account") ||
        text.includes("register") ||
        text.includes("sign up")
    ) {
        return "You can log in or create an account from the top navigation. An account helps you manage orders and access your dashboard.";
    }


    if (
        text.includes("cart") ||
        text.includes("basket")
    ) {
        return "You can review your selected items in the cart page, update quantities, remove products, and continue to checkout when ready.";
    }

  
    if (
        text.includes("review") ||
        text.includes("rating")
    ) {
        return "You can view product reviews on the product review page and leave feedback after purchasing.";
    }


    if (typeof products !== "undefined" && Array.isArray(products)) {
        const matchedProduct = products.find(product =>
            text.includes(product.name.toLowerCase()) ||
            product.name.toLowerCase().includes(text)
        );

        if (matchedProduct) {
            return `${matchedProduct.name} costs £${matchedProduct.price}. ${matchedProduct.description}`;
        }

        const matchedCategory = products.filter(product =>
            text.includes(product.category.toLowerCase())
        );

        if (matchedCategory.length > 0) {
            const names = matchedCategory.slice(0, 4).map(p => p.name).join(", ");
            return `Here are some ${matchedCategory[0].category} products: ${names}. You can view more on the products page.`;
        }
    }

    if (typeof products !== "undefined" && Array.isArray(products)) {
        const quantityMatch = text.match(/(\d+)/);

        if (quantityMatch) {
            const qty = parseInt(quantityMatch[1], 10);

            const matchedProduct = products.find(product =>
                text.includes(product.name.toLowerCase())
            );

            if (matchedProduct && qty > 0) {
                const total = matchedProduct.price * qty;
                return `${qty} x ${matchedProduct.name} would cost £${total.toLocaleString()}.`;
            }
        }
    }

    if (
        text.includes("recommend") ||
        text.includes("best product") ||
        text.includes("what do you suggest")
    ) {
        return "If you tell me what type of equipment you need, such as classroom displays, laptops, networking, or audio equipment, I can guide you better.";
    }

    return "Sorry, I did not fully understand that. You can ask me about products, prices, delivery, returns, checkout, support, or your order.";
}