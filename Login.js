//this file is for the javascript logic which will take place in the login page

//this function will listen for submission on the login page 
function listen_Sumbission(){
    const form = document.querySelector("form")

     form.addEventListener("submit", function(e){
        const ok = validateInputs();   
        if (!ok) e.preventDefault();  
    })
}

//function to validate inputs
function validateInputs(){ 
    const username = document.getElementById("username")
    const password = document.getElementById("password")
    const label = document.getElementById("infolabel")
    const pw = password.value.trim()
    //ensure there are no empty fields
    if(username.value.trim()  ==""||pw == ""){
        //console.log("validate input test run ")
        label.hidden = false
        label.textContent = "Please ensure all fields are filled out"
        return false
    } 
    //ensure password is more then 8 characters 
    else if(pw.length<8||!/[a-z]/.test(pw)||!/[A-Z]/.test(pw)||!/[0-9]/.test(pw)){
        label.hidden = false
        label.textContent = "Please ensure the password meets the requirements\n-8 or more characters"+
        " \n-at least one uppercase character \n-at least one lowercase character\n-at least one number "
        console.log("pw error")
        return false

    }
    else{
       label.hidden = true
       return true 
    }   
}

//function to handle backend responses 
function handleBackend(){
    //if login is sucessful extract and store token and redirect user
    
    //if fails display sutible error message

}

//Ui Improvement
//function to toggle password visibility 
//not vital
function showPassword(){

}

//function to show loading button when its processing 
function showLoading(){

}

document.addEventListener("DOMContentLoaded", listen_Sumbission);
