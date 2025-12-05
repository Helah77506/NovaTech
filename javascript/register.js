//javascript logic for registration page

//listens for the submission
function listen_Submission() {
    const form = document.querySelector("form");

    form.addEventListener("submit", function (e) {
        const ok = validateInputs();
        if (!ok) e.preventDefault(); // stop form if something is wrong
    });
}

//validate inputs function
function validateInputs() {
    const username = document.getElementById("username");
    const password = document.getElementById("password");
    const email = document.getElementById("email");
    const label = document.getElementById("infolabel");

    //these values should be used
    const pw = password.value.trim();
    const em = email.value.trim();
    const user = username.value.trim();

    //ensure there are no empty fields
    if (user === "" || pw === "" || em === "") {
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
        label.style.display = "grid";
        label.style.whiteSpace = "pre-line"; // Add this line
        label.textContent =
            "Please ensure the password meets the requirements:\n" +
            "- 8 or more characters\n" +
            "- at least one uppercase character\n" +
            "- at least one lowercase character\n" +
            "- at least one number";
        return false;
    }

    //ensure email is in a correct format (simple @ check)
    else if (!/@/.test(em)) {
        label.hidden = false;
        label.style.display = "block";
        label.textContent = "Please enter a valid email";
        return false;
    }

    //everything is fine
    else {
        label.hidden = true;
        label.style.display = "none";
        return true;
    }
}

//function to handle backend responses
function handleBackend() {
    //if registration is sucessful extract and store token and redirect user

    //if fails display sutible error message
}

//listens for submission
document.addEventListener("DOMContentLoaded", listen_Submission);