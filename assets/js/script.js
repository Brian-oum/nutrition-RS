document.addEventListener("DOMContentLoaded", function () {
    // Show Login Form
    function showLogin() {
        document.getElementById('register-form').style.display = 'none';
        document.getElementById('login-form').style.display = 'block';
    }

    // Show Register Form
    function showRegister() {
        document.getElementById('login-form').style.display = 'none';
        document.getElementById('register-form').style.display = 'block';
    }

    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    // Email Verification
    const urlParams = new URLSearchParams(window.location.search);
    const email = urlParams.get("email");
    const userEmailInput = document.getElementById("userEmail");

    if (userEmailInput) {
        userEmailInput.value = email ? email : "";
    }

    // OTP Sending
    const sendOTPButton = document.getElementById("sendOTP");
    if (sendOTPButton) {
        sendOTPButton.addEventListener("click", function () {
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
                    window.location.href = "otp_verification.html";
                }, 2000);
            }).catch(function () {
                showMessage("Error sending OTP!", "error");
            });
        });
    }

    // OTP Verification
    const verifyOTPButton = document.getElementById("verifyOTP");
    if (verifyOTPButton) {
        document.getElementById("emailMessage").textContent = `Please enter the OTP sent to "${sessionStorage.getItem("userEmail")}"`;

        startOTPTimer();

        verifyOTPButton.addEventListener("click", function () {
            const enteredOTP = document.getElementById("otpInput").value;
            const storedOTP = sessionStorage.getItem("otp");
            const responseMessage = document.getElementById("responseMessage");

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

                        verifyOTPButton.textContent = "Redirecting...";
                        verifyOTPButton.disabled = true;

                        setTimeout(() => {
                            window.location.href = "../index.php";
                        }, 2000);
                    } else {
                        responseMessage.textContent = data.message;
                        responseMessage.className = "error";
                    }
                })
                .catch(() => {
                    responseMessage.textContent = "An error occurred. Please try again.";
                    responseMessage.className = "error";
                });
            } else {
                responseMessage.textContent = "Invalid OTP!";
                responseMessage.className = "error";
            }
        });

        document.getElementById("resendOTP").addEventListener("click", resendOTP);
    }

    // OTP Timer
    function startOTPTimer() {
        let timeLeft = 60;
        const expireSpan = document.getElementById("expire");
        const otpInput = document.getElementById("otpInput");
        const verifyButton = document.getElementById("verifyOTP");
        const resendButton = document.getElementById("resendOTP");
        const responseMessage = document.getElementById("responseMessage");

        resendButton.style.display = "none";
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
                resendButton.style.display = "block";
            }
        }, 1000);
    }

    function resendOTP() {
        const email = sessionStorage.getItem("userEmail");
        const responseMessage = document.getElementById("responseMessage");
        const resendButton = document.getElementById("resendOTP");

        resendButton.disabled = true;
        resendButton.textContent = "Resending...";

        setTimeout(() => {
            const newOTP = Math.floor(1000 + Math.random() * 9000);
            sessionStorage.setItem("otp", newOTP);

            emailjs.init("L5PzYj2MAUuZ4E9f4");
            emailjs.send("service_07esa9d", "template_h2q7mxr", {
                to_email: email,
                otp_code: newOTP
            }).then(() => {
                responseMessage.textContent = "New OTP sent successfully!";
                responseMessage.className = "success";
                resendButton.style.display = "none";
                startOTPTimer();
            }).catch(() => {
                responseMessage.textContent = "Error sending OTP. Please try again.";
                responseMessage.className = "error";
                resendButton.disabled = false;
                resendButton.textContent = "Resend OTP";
            });
        }, 5000);
    }
});


// Dynamic Page Loading  
document.addEventListener("DOMContentLoaded", function () {
    function loadContent(page, addToHistory = true) {
        event.preventDefault();

        let contentDiv = document.getElementById("dynamic-content");
        contentDiv.innerHTML = "<p>Loading...</p>";

        fetch(page)
            .then(response => {
                if (!response.ok) throw new Error("Failed to load content.");
                return response.text();
            })
            .then(data => {
                contentDiv.innerHTML = data;

                // Store current page in sessionStorage
                sessionStorage.setItem("lastPage", page);

                if (addToHistory) {
                    history.pushState({ page: page }, "", page);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                contentDiv.innerHTML = "<p>Failed to load content.</p>";
            });
    }

    // Handle browser back/forward buttons
    window.onpopstate = function (event) {
        if (event.state && event.state.page) {
            loadContent(event.state.page, false);
        }
    };

    // Load last page on refresh
    const lastPage = sessionStorage.getItem("lastPage");
    if (lastPage) {
        loadContent(lastPage, false);
    }

    // Attach event listeners to sidebar links
    document.querySelectorAll(".sidebar a").forEach(link => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const page = this.getAttribute("href");
            if (page) loadContent(page);
        });
    });
});


// 🔍 SEARCH FUNCTION (Now globally accessible)
window.searchChild = function () {
    let query = document.getElementById("searchChild").value;
    let searchResults = document.getElementById("search-results");

    if (query.length < 1) {
        searchResults.innerHTML = "";
        return;
    }

    fetch(`./search_child.php?query=${query}`)
        .then(response => response.json())
        .then(data => {
            searchResults.innerHTML = "";
            data.forEach(child => {
                let div = document.createElement("div");
                div.classList.add("search-item");
                div.textContent = child.child_name;
                div.onclick = function () {
                    window.loadMealPlan(child.id);  // ✅ FIX: Make sure loadMealPlan is global
                };
                searchResults.appendChild(div);
            });
        })
        .catch(error => console.error("Error:", error));
};

// ✅ MAKE `loadMealPlan` GLOBAL
window.loadMealPlan = function (childId) {
    let mealPlanContainer = document.getElementById("meal-plan-container");

    fetch(`./load_meal_plan.php?id=${childId}`)
        .then(response => response.text())
        .then(data => {
            mealPlanContainer.innerHTML = data;
        })
        .catch(error => console.error("Error loading meal plan:", error));
};

