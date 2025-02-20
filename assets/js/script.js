document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("login-form");
    const registerForm = document.getElementById("register-form");
    const formTitle = document.getElementById("form-title");
    const showRegister = document.getElementById("show-register");
    const showLogin = document.getElementById("show-login");
    const messageBox = document.getElementById("message-box");

    // Toggle between login and register forms
    showRegister.addEventListener("click", function () {
        loginForm.style.display = "none";
        registerForm.style.display = "block";
        formTitle.textContent = "Register";
    });

    showLogin.addEventListener("click", function () {
        registerForm.style.display = "none";
        loginForm.style.display = "block";
        formTitle.textContent = "Login";
    });

    // Display session messages from PHP
    fetch("auth.php")
        .then(response => response.text())
        .then(data => {
            if (data.trim() !== "") {
                messageBox.innerHTML = data;
                setTimeout(() => messageBox.innerHTML = "", 5000); // Clear message after 5s
            }
        })
        .catch(error => console.error("Error fetching messages:", error));
    });