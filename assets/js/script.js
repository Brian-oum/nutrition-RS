let progressChart; // Declare progressChart globally

document.addEventListener("DOMContentLoaded", function () {
    // Function to set the max date for the date picker
    function setMaxDateForDatePicker() {
        const datePicker = document.querySelector("#dob");
        if (datePicker) {
            const today = new Date().toISOString().split('T')[0];
            datePicker.setAttribute('max', today);
        }
    }

    setMaxDateForDatePicker();

    const sidebar = document.querySelector(".sidebar");
    const content = document.querySelector(".content");
    const toggleBtn = document.getElementById("toggle-btn");

    // Toggle sidebar and content visibility
    if (toggleBtn) {
        toggleBtn.addEventListener("click", function () {
            sidebar.classList.toggle("expanded");
            content.classList.toggle("expanded");
        });
    }

    // Initialize search functionality
    initSearchFunctionality();

    // Initialize Track Progress functionality
    initTrackProgress(); // Ensure track progress loads dynamically
});

// 🔎 Search Functionality for Child Details
function initSearchFunctionality() {
    const searchInput = document.getElementById("search-child");
    const searchResults = document.getElementById("search-results");
    const nameField = document.getElementById("name");

    if (!searchInput) return; // Exit if search input doesn't exist (prevents errors)

    searchInput.addEventListener("input", function () {
        let query = searchInput.value.trim();
        if (query.length === 0) {
            searchResults.innerHTML = "";
            return;
        }

        fetch(`./search_child.php?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                console.log("Search Results:", data);
                searchResults.innerHTML = "";

                if (data.length > 0) {
                    data.forEach(child => {
                        let div = document.createElement("div");
                        div.classList.add("search-result");
                        div.textContent = child.child_name;
                        div.addEventListener("click", function () {
                            nameField.value = child.child_name;
                            searchResults.innerHTML = "";
                        });
                        searchResults.appendChild(div);
                    });
                } else {
                    searchResults.innerHTML = "<div class='search-result'>No results found</div>";
                }
            })
            .catch(error => console.error("Error fetching child data:", error));
    });

    // Close search results when clicking outside
    document.addEventListener("click", function (event) {
        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
            searchResults.innerHTML = "";
        }
    });
}

// 📊 Track Progress Functionality
function initTrackProgress() {
    const childDropdown = document.getElementById("child_id");
    if (!childDropdown) return; // Exit if Track Progress is not loaded

    childDropdown.addEventListener("change", function () {
        let childId = this.value;
        if (childId) {
            fetch(`fetch_progress.php?child_id=${childId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    updateGraph(data.child_name, data.progress);  // Update chart data
                })
                .catch(error => console.error("Error fetching progress data:", error));
        } else {
            document.getElementById("progress-details").innerHTML = "<p>Select a child to view progress.</p>";
        }
    });
}

// Initialize the chart (Ensure this runs once)
function initializeChart() {
    const ctx = document.getElementById('progressChart').getContext('2d');
    progressChart = new Chart(ctx, {
        type: 'bar', // Bar chart
        data: {
            labels: [], // X-axis (Schedules)
            datasets: [{
                label: 'Weight (kg)',
                data: [], // Y-axis (Weight)
                backgroundColor: 'rgba(54, 162, 235, 0.6)', // Light blue color
                borderColor: 'rgba(54, 162, 235, 1)', // Darker blue for borders
                borderWidth: 1,
                barThickness: 30, // Adjust bar thickness
                hoverBackgroundColor: 'rgba(54, 162, 235, 1)', // Bright color when hovering
                hoverBorderColor: 'rgba(54, 162, 235, 1)', // Bright border on hover
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'x', // Switch axes: X is for schedule, Y is for weight
            scales: {
                x: {
                    title: { display: true, text: 'Schedule' },
                    ticks: {
                        autoSkip: false, // Ensure all schedules appear
                        reverse: true, // Start from 2-Weeks at the left
                        font: {
                            size: 14,
                            weight: 'bold',
                            family: 'Arial, sans-serif',
                            color: '#333'
                        }
                    }
                },
                y: {
                    title: { display: true, text: 'Weight (kg)' },
                    ticks: {
                        font: {
                            size: 14,
                            weight: 'bold',
                            family: 'Arial, sans-serif',
                            color: '#333'
                        },
                        beginAtZero: true,
                    }
                }
            }
        }
    });
}

// Update the graph with new data
function updateGraph(childName, progressData) {
    let weights = progressData.map(p => p.weight);
    let schedules = progressData.map(p => p.schedule);

    // Ensure that progress data is sorted correctly by schedule
    let schedule_order = [
        '2-Weeks', '6-Weeks', '10-Weeks', '14-Weeks', '1-Year', '1.25-Years', '1.5-Years',
        '2-Years', '2.5-Years', '3-Years', '3.5-Years', '4-Years', '4.5-Years', '5-Years'
    ];

    progressData.sort((a, b) => schedule_order.indexOf(a.schedule) - schedule_order.indexOf(b.schedule));

    // Log data to ensure it is correct
    console.log("Updated Progress Data: ", progressData);

    progressChart.data.labels = schedules; // X-axis (Schedule)
    progressChart.data.datasets[0].data = weights; // Y-axis (Weight)
    progressChart.options.plugins = { title: { display: true, text: `Weight Progress for ${childName}` } };
    progressChart.update();
}

// Initialize the chart once the page is ready
initializeChart();
