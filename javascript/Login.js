//this file is for the javascript logic which will take place in the login page

//this function will listen for submission on the login page
function listen_Submission() {
    const form = document.querySelector("form");

    form.addEventListener("submit", function (e) {
        const ok = validateInputs();
        if (!ok) e.preventDefault(); // stop submit if password/user is wrong format
    });
}

//function to validate inputs
function validateInputs() {
    const username = document.getElementById("username");
    const password = document.getElementById("password");
    const label = document.getElementById("infolabel");

    const user = username.value.trim();
    const pw = password.value.trim();

    //ensure there are no empty fields
    if (user === "" || pw === "") {
        //console.log("validate input test run ")
        label.hidden = false;
        label.style.display = "block";
        label.textContent = "Please ensure all fields are filled out";
        return false;
    }

    //ensure password is more than 8 characters and contains 1 uppercase, 1 lowercase and a number
    else if (
        pw.length < 8 ||
        !/[a-z]/.test(pw) ||   // no lowercase
        !/[A-Z]/.test(pw) ||   // no uppercase
        !/[0-9]/.test(pw)      // no number
    ) {
        label.hidden = false;
        label.style.display = "block";
        label.textContent =
            "Please ensure the password meets the requirements:\n" +
            "- 8 or more characters\n" +
            "- at least one uppercase character\n" +
            "- at least one lowercase character\n" +
            "- at least one number";
        return false;
    }

    //everything is fine -> allow form to go to login.php
    else {
        label.hidden = true;
        label.style.display = "none";
        return true;
    }
}

//function to handle backend responses
function handleBackend() {
    //if login is sucessful extract and store token and redirect user

    //if fails display sutible error message
}

//Ui Improvement
//function to toggle password visibility
//not vital
function showPassword() {

}

//function to show loading button when its processing
function showLoading() {

}

//start the listener when page loads
document.addEventListener("DOMContentLoaded", listen_Submission);
