
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
    else if (cvcV.length < 3 || cvcV.length > 4) {
        infolabel2.textContent = "Please ensure a valid CVC is entered"
        infolabel2.hidden = false
        return false
    }
    else if(!expiryRegex.test(expiryV)){
        infolabel2.textContent = "Please ensure a valid expiry is entered in the format MM/YY"
        infolabel2.hidden = false
        
        return false
    }
    infolabel2.hidden = true
    infolabel2.style.display = "none"
    return true 
}

function listen_Sumbission(){
    const form = document.querySelector("form")
    
    form.addEventListener("submit", function(e){
        const shippingOK = validateUserInputs()
        const paymentOK = validatePaymentInputs()

        if (!shippingOK || !paymentOK) {
            e.preventDefault()
        }
    })
}

//listens for sumbission 
document.addEventListener("DOMContentLoaded", listen_Sumbission);