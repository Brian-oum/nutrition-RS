// login and register page swapping 
function showLogin(){
    document.getElementById('register-form').style.display= 'none';
    document.getElementById('login-form').style.display = 'block';
}

function showRegister(){
    document.getElementById('login-form').style.display = 'none';
    document.getElementById('register-form').style.display = 'block';
}

document.addEventListener("DOMContentLoaded", function(){
    let activeForm = sessionStorage.getItem("activeForm") || "login";
    if (activeForm === "register"){
        showRegister();
    } else {
        showLogin();
    }
});

// OTP Sending
const sendOTPButton = document.getElementById("sendOTP");
if (sendOTPButton) {
    sendOTPButton.addEventListener("click", function () {
        const email = sessionStorage.getItem("userEmail"); // Get email from session storage
        if (!email) {
            showMessage("Invalid email!", "error");
            return;
        }

        const otp = Math.floor(1000 + Math.random() * 9000);
        sessionStorage.setItem("otp", otp);

        emailjs.init("L5PzYj2MAUuZ4E9f4");
        emailjs.send("service_07esa9d", "template_h2q7mxr", {
            to_email: email,
            otp_code: otp
        }).then(function () {
            showMessage("OTP sent successfully!", "success");
            sendOTPButton.textContent = "Redirecting...";
            sendOTPButton.disabled = true;

            setTimeout(() => {
                window.location.href = "./otp_verification.html";
            }, 2000);
        }).catch(function () {
            showMessage("Error sending OTP!", "error");
        });
    });
}
