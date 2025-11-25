//javascript logic for registration page

//listens for the submission 
function listen_Sumbission(){
    const form = document.querySelector("form")

    form.addEventListener("submit",function(e){
        validateInputs(); 
        e.preventDefault();    
    })

}

//validate inputs function 
function validateInputs(){ 
    const username = document.getElementById("username")
    const password = document.getElementById("password")
    const email = document.getElementById("email")
    const label = document.getElementById("infolabel")
    //these values should be used 
    const pw = password.value.trim()
    const em = email.value.trim()
    //ensure there are no empty fields
    if(username.value.trim()  ==""||pw == ""||em==""){
        //console.log("validate input test run ")
        label.hidden = false
        label.textContent = "Please ensure all fields are filled out"
        return false
    } 
    //ensure password is more then 8 characters and contains 1 upercase and lowercase and a number
    else if(pw.length<8||!/[a-z]/.test(pw)||!/[A-Z]/.test(pw)||!/[0-9]/.test(pw)){
        label.hidden = false
        label.textContent = "Please ensure the password meets the requirements\n-8 or more characters"+
        " \n-at least one uppercase character \n-at least one lowercase character\n-at least one number "
        return false

    }
    //ensure email is in a correct format 
    else if(!/[@]/.test(em)){
        label.hidden = false
        label.textContent = "Please enter a valid email" 

    }
    else{
       label.hidden = true
       return true 
    }   
}

//submit to backend 
function sumbitreg(){

}

//function to handle backend responses 
function handleBackend(){
    //if registration is sucessful extract and store token and redirect user
    
    //if fails display sutible error message

}

//listens for sumbission 
document.addEventListener("DOMContentLoaded", listen_Sumbission);