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
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const email = urlParams.get("email");
    document.getElementById("userEmail").value = email;

    document.getElementById("sendOTP").addEventListener("click", function() {
        const otp = Math.floor(1000 + Math.random() * 9000);
        sessionStorage.setItem("otp", otp); // Store OTP in sessionStorage

        emailjs.init("n1mynshmvV-ncZptA"); // Replace with your actual EmailJS public key
        emailjs.send("service_07esa9d", "template_h2q7mxr", {
            to_email: email,
            otp_code: otp
        }).then(function(response) {
            alert("OTP sent successfully!");
            window.location.href = "otp_verification.html";
        }).catch(function(error) {
            alert("Error sending OTP!");
        });
    });
});


// otp verification
document.getElementById("verifyOTP").addEventListener("click", function() {
    const enteredOTP = document.getElementById("otpInput").value;
    const storedOTP = sessionStorage.getItem("otp"); // Get OTP from sessionStorage

    if (enteredOTP === storedOTP) {
        // OTP is correct, proceed to complete registration in PHP
        fetch('./auth.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `verify_otp=1`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("Registration successful!");
                window.location.href = "../index.php"; // Redirect to login
            } else {
                document.getElementById("responseMessage").innerText = data.message;
            }
        })
        .catch(error => console.error('Error:', error));
    } else {
        document.getElementById("responseMessage").innerText = "Invalid OTP!";
    }
});


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