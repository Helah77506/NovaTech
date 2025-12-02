//js for checkout
//listener 
function listen_Sumbission(){
    const form = document.querySelector("form")
       
    form.addEventListener("submit",function(e){
            const ok = validateUserInputs();   
            if (!ok) e.preventDefault(); 
        }
    )
}

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
    return true 
    


}

//function to validate card inputs 
function validatePaymentInputs(){
    
}

//listens for sumbission 
document.addEventListener("DOMContentLoaded", listen_Sumbission);