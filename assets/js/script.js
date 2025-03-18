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

function toggleForm(form){
    sessionStorage.setItem("activeForm", form);
    if ( form === "register"){
        showRegister();
    } else {
        showLogin();
    }
}

// dynamic page loading
document.addEventListener("DOMContentLoaded", function () {
    // Sidebar Navigation without Reload
    document.querySelectorAll(".load-page").forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            let pageUrl = this.getAttribute("href");

            fetch(pageUrl)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("dynamic-content").innerHTML = data;
                    history.pushState({ page: pageUrl }, "", "?page=" + pageUrl);
                })
                .catch(error => console.error("Error loading page:", error));
        });
    });

    // Preserve State on Refresh
    window.addEventListener("popstate", function (event) {
        if (event.state && event.state.page) {
            fetch(event.state.page)
                .then(response => response.text())
                .then(data => document.getElementById("dynamic-content").innerHTML = data)
                .catch(error => console.error("Error restoring page:", error));
        }
    });

    // Logout Handling
    document.getElementById("logout").addEventListener("click", function (e) {
        e.preventDefault();
        fetch("logout.php")
            .then(() => window.location.href = "../index.php")
            .catch(error => console.error("Logout error:", error));
    });
});

// Search Child Function
window.searchChild = function () {
    let query = document.getElementById("searchChild").value;
    let searchResults = document.getElementById("search-results");

    if (query.length < 1) {
        searchResults.innerHTML = "";
        searchResults.style.display = "none";
        return;
    }

    fetch(`./search_child.php?query=${query}`)
        .then(response => response.json())
        .then(data => {
            searchResults.innerHTML = "";
            searchResults.style.display = data.length > 0 ? "block" : "none";

            data.forEach(child => {
                let div = document.createElement("div");
                div.classList.add("search-item");
                div.textContent = child.child_name;
                div.onclick = function () {
                    window.loadMealPlan(child.id);
                    setTimeout(() => searchResults.style.display = "none", 300);
                };
                searchResults.appendChild(div);
            });
        })
        .catch(error => {
            console.error("Error:", error);
            searchResults.style.display = "none";
        });
};

// Load Meal Plan Function
window.loadMealPlan = function (childId) {
    let mealPlanContainer = document.getElementById("meal-plan-container");

    fetch(`./load_meal_plan.php?id=${childId}`)
        .then(response => response.text())
        .then(data => mealPlanContainer.innerHTML = data)
        .catch(error => console.error("Error loading meal plan:", error));
};
