function listen_Sumbission(){
    const form = document.querySelector("form")

    form.addEventListener("submit",function(e){
        const ok = validateInputs();   
        if (!ok) e.preventDefault();   
    })
}
function validateInputs(){ 
    const name = document.querySelector("input[name='name']");
    const email = document.querySelector("input[name='email']");
    const subject = document.querySelector("input[name='subject']");
    const message = document.querySelector("textarea[name='message']");
}

const nm = name.value.trim()
const em = email.value.trim()
const sub = subject.value.trim()
const msg = message.value.trim()

if(nm ==""||em==""||sub==""||msg==""){
    alert("Please ensure all fields are filled out")
    return false
}
else if(!/[@]/.test(em)){
    alert("Please enter a valid email") 
    return false
}
else{
   return true 
}

document.addEventListener("DOMContentLoaded", listen_Sumbission);