
//function to validate user inputs 
function validateUserInputs(){
    //set up variables 
    const full_name = document.getElementById("full-name")
    const address = document.getElementById("address")
    const city = document.getElementById("city")
    const zip = document.getElementById("zip")
    const infolabel = document.getElementById("infolabel")

    const full_nameV = full_name.value.trim()
    const addressV = address.value.trim()
    const cityV = city.value.trim()
    const zipV = zip.value.trim()

    if (full_nameV == "" || addressV == "" || cityV == "" || zipV == "") {
       infolabel.hidden = false
       infolabel.textContent = "Please Ensure All Shipping Fields Are Entered"
       return false
    }
    
    infolabel.hidden = true
    infolabel.style.display = "none"
    return true 
    
}

//function to validate card inputs 
function validatePaymentInputs(){
    const card_number = document.getElementById("card-number")
    const expiry = document.getElementById("expiry")
    const cvc = document.getElementById("cvc")
    const infolabel2 = document.getElementById("infolabel2")

    const card_numberV = card_number.value.trim()
    const expiryV = expiry.value.trim()
    const cvcV = cvc.value.trim()

    const expiryRegex = /^(0[1-9]|1[0-2])\/\d{2}$/ //regex for the expiry 

    if(card_numberV.length!=16){
        infolabel2.textContent = "Please ensure a valid 16 digit card number is entered"
        infolabel2.hidden = false
        return false
    } 
    else if(!expiryRegex.test(expiryV)){
        infolabel2.textContent = "Please ensure a valid expiry is entered in the format MM/YY"
        infolabel2.hidden = false
        return false
    }
    else if (cvcV.length < 3 || cvcV.length > 4) {
        infolabel2.textContent = "Please ensure a valid CVC is entered"
        infolabel2.hidden = false
        return false
    }
    infolabel2.hidden = true
    infolabel2.style.display = "none"
    return true 
}
    

// CHECK STOCK BEFORE ORDER
// ======================================
function validateStock(cart, products) {
    for (let item of cart) {
        const product = products.find(p => p.id === item.id);
        if (!product || product.stock < item.quantity) {
            alert(`Insufficient stock for ${item.name}`);
            return false;
        }
    }
    return true;
}


// UPDATE STOCK AFTER ORDER
// ======================================
function updateStockFromCart(cart) {
    let products = JSON.parse(localStorage.getItem("productsData") || "[]");

    cart.forEach(item => {
        const product = products.find(p => p.id === item.id);
        if (product) {
            product.stock -= item.quantity;
            if (product.stock < 0) product.stock = 0;
        }
    });

    localStorage.setItem("productsData", JSON.stringify(products));
}


// SAVE ORDER
// ======================================
function saveOrder(cart) {
    const orders = JSON.parse(localStorage.getItem("orders") || "[]");

    orders.push({
        id: Date.now(),
        items: cart,
        date: new Date().toISOString(),
        status: "Pending"
    });

    localStorage.setItem("orders", JSON.stringify(orders));
}

// function listen_Sumbission(){
//     const form = document.querySelector("form")
    
//     form.addEventListener("submit", function(e){
//         const shippingOK = validateUserInputs()
//         const paymentOK = validatePaymentInputs()

//         if (!shippingOK || !paymentOK) {
//             e.preventDefault()
//         } 
//         else {
//             // redirect to another page
//             window.location.href = "order.html";}
//     })
// }
// this is the old funcion just incase

function listen_Submission(){
    const form = document.querySelector("form");

    form.addEventListener("submit", function(e){
        e.preventDefault();

        const shippingOK = validateUserInputs();
        const paymentOK = validatePaymentInputs();

        if (!shippingOK || !paymentOK) return;

        const cart = JSON.parse(localStorage.getItem("cart") || "[]");
        let products = JSON.parse(localStorage.getItem("productsData") || "[]");

        if (cart.length === 0) {
            alert("Your cart is empty.");
            return;
        }

        // Validate stock before reducing
        if (!validateStock(cart, products)) return;

        // Update stock
        updateStockFromCart(cart);

        // Save order
        saveOrder(cart);

        // Clear cart
        localStorage.removeItem("cart");

        // Redirect
        window.location.href = "order.html";
    });
}

document.addEventListener("DOMContentLoaded", listen_Submission);
