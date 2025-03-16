function showLogin() {
    document.getElementById('register-form').style.display = 'none';
    document.getElementById('login-form').style.display = 'block';
}
function showRegister() {
    document.getElementById('login-form').style.display = 'none';
    document.getElementById('register-form').style.display = 'block';
}

// Prevent form resubmission on page refresh
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

// email verification
document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const email = urlParams.get("email");
    document.getElementById("userEmail").value = email;

    document.getElementById("sendOTP").addEventListener("click", function () {
        const sendOTPButton = document.getElementById("sendOTP");
        const otp = Math.floor(1000 + Math.random() * 9000);
        sessionStorage.setItem("otp", otp); // Store OTP in sessionStorage

        emailjs.init("L5PzYj2MAUuZ4E9f4"); // Replace with your actual EmailJS public key
        emailjs.send("service_07esa9d", "template_h2q7mxr", {
            to_email: email,
            otp_code: otp
        }).then(function (response) {
            showMessage("OTP sent successfully!", "success");
            sendOTPButton.textContent = "Redirecting..."; // Change button text
            sendOTPButton.disabled = true; // Disable button to prevent multiple clicks

            setTimeout(() => {
                window.location.href = "otp_verification.html";
            }, 2000); // Redirect after 2 seconds
        }).catch(function (error) {
            showMessage("Error sending OTP!", "error");
        });
    });

    function showMessage(message, type) {
        const messageBox = document.getElementById("message-box");
        messageBox.textContent = message;
        messageBox.className = "message-box " + type; // Add class based on type
    }
});



// otp verification
document.addEventListener("DOMContentLoaded", function () {
    const email = sessionStorage.getItem("userEmail"); // Get stored email
    document.getElementById("emailMessage").textContent = `Please enter the OTP sent to "${email}"`;

    startOTPTimer();

    document.getElementById("verifyOTP").addEventListener("click", verifyOTP);
    document.getElementById("resendOTP").addEventListener("click", resendOTP);
});

function startOTPTimer() {
    let timeLeft = 60;
    const expireSpan = document.getElementById("expire");
    const otpInput = document.getElementById("otpInput");
    const verifyButton = document.getElementById("verifyOTP");
    const resendButton = document.getElementById("resendOTP");
    const responseMessage = document.getElementById("responseMessage");

    resendButton.style.display = "none"; // Hide resend button at start
    otpInput.disabled = false;
    verifyButton.disabled = false;

    const countdown = setInterval(() => {
        timeLeft--;
        expireSpan.textContent = timeLeft;

        if (timeLeft <= 0) {
            clearInterval(countdown);
            responseMessage.textContent = "OTP expired! Please request a new one.";
            responseMessage.className = "error";
            otpInput.disabled = true;
            verifyButton.disabled = true;
            resendButton.style.display = "block"; // Show resend button
        }
    }, 1000);
}

function verifyOTP() {
    const enteredOTP = document.getElementById("otpInput").value;
    const storedOTP = sessionStorage.getItem("otp");
    const responseMessage = document.getElementById("responseMessage");
    const verifyButton = document.getElementById("verifyOTP");

    if (enteredOTP === storedOTP) {
        fetch('./auth.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `verify_otp=1`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                responseMessage.textContent = "Registration successful!";
                responseMessage.className = "success";

                verifyButton.textContent = "Redirecting...";
                verifyButton.disabled = true;

                setTimeout(() => {
                    window.location.href = "../index.php";
                }, 2000);
            } else {
                responseMessage.textContent = data.message;
                responseMessage.className = "error";
            }
        })
        .catch(error => {
            console.error('Error:', error);
            responseMessage.textContent = "An error occurred. Please try again.";
            responseMessage.className = "error";
        });
    } else {
        responseMessage.textContent = "Invalid OTP!";
        responseMessage.className = "error";
    }
}

function resendOTP() {
    const email = sessionStorage.getItem("userEmail");
    const responseMessage = document.getElementById("responseMessage");
    const resendButton = document.getElementById("resendOTP");

    // Disable resend button temporarily (prevent spam clicking)
    resendButton.disabled = true;
    resendButton.textContent = "Resending...";

    setTimeout(() => {
        const newOTP = Math.floor(1000 + Math.random() * 9000);
        sessionStorage.setItem("otp", newOTP);

        emailjs.init("L5PzYj2MAUuZ4E9f4"); // Replace with your actual EmailJS public key
        emailjs.send("service_07esa9d", "template_h2q7mxr", {
            to_email: email,
            otp_code: newOTP
        }).then(function(response) {
            responseMessage.textContent = "New OTP sent successfully!";
            responseMessage.className = "success";
            resendButton.style.display = "none";
            startOTPTimer(); // Restart OTP expiration timer
        }).catch(function(error) {
            responseMessage.textContent = "Error sending OTP. Please try again.";
            responseMessage.className = "error";
            resendButton.disabled = false;
            resendButton.textContent = "Resend OTP";
        });
    }, 5000); // 5-second cooldown before resending OTP
}



// dashboard dynamic-content
function loadContent(page) {
    event.preventDefault();

    let contentDiv = document.getElementById("dynamic-content");

    
    contentDiv.innerHTML = "<p>Loading...</p>";

    fetch(page) 
        .then(response => response.text())
        .then(data => {
            contentDiv.innerHTML = data;
        })
        .catch(error => {
            console.error("Error loading content:", error);
            contentDiv.innerHTML = "<p>Failed to load content.</p>";
        });
}