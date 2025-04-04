<?php
session_start();
include('../config/db.php');

$parent_username = $_SESSION["username"];
$children_query = "SELECT * FROM children WHERE parent_username = '$parent_username' ORDER BY id DESC";
$children_result = mysqli_query($conn, $children_query);

if (mysqli_num_rows($children_result) == 0) {
    echo "<script>alert('No child details found! Please add your child’s details first.'); window.location.href='details.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Meal Plans</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<style>
    /* General Meal Container Styling */
    .meal-container {
        width: 80%;
        margin: 40px auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #f9f9f9;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Heading Styling */
    .meal-container h1 {
        font-size: 24px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
    }

    /* Dropdown Styling */
    #childSelect {
        width: 80%;
        padding: 10px;
        font-size: 16px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    #childSelect:focus {
        border-color: #4CAF50;
        box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
    }

    /* Meal Plan Content Styling */
    #meal-plan-container {
        margin-top: 20px;
        font-size: 18px;
        color: #666;
        display: none;
    }

    /* Loading Indicator Styling */
    .loading {
        font-size: 18px;
        color: #228B22;
        font-weight: bold;
    }

    /* Back Button Styling */
    .back-btn {
        display: inline-block;
        margin-top: 20px;
        color: #4CAF50;
        text-decoration: none;
        font-size: 16px;
        padding: 8px 16px;
        border: 2px solid #4CAF50;
        border-radius: 30px;
        transition: 0.3s;
        text-align: center;
    }

    .back-btn:hover {
        background-color: #4CAF50;
        color: #fff;
        text-decoration: none;
    }

    /* Meal Table Styling */
    .meal-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Table Header Styling */
    .meal-table th {
        background: #228B22;
        color: white;
        padding: 12px;
        text-align: left;
        font-size: 16px;
        text-transform: uppercase;
    }

    /* Table Row Styling */
    .meal-table td {
        padding: 12px;
        border-bottom: 1px solid #ddd;
        font-size: 15px;
    }

    /* Meal Row Hover Effect */
    .meal-table tr:hover {
        background-color: #f1f1f1;
        cursor: pointer;
    }

    /* Premium Meal Styling */
    .premium {
        background: #228B22;
        color: white;
        font-weight: bold;
        text-transform: uppercase;
    }

    /* Affordable Meal Styling */
    .affordable {
        background: #f0f0f0;
        color: #228B22;
    }

    /* Responsive Design */
    @media (max-width: 600px) {
        .meal-container {
            padding: 15px;
            width: 90%;
        }

        .meal-table th, .meal-table td {
            font-size: 14px;
            padding: 10px;
        }
    }
</style>

<body>
    <div class="meal-container">
        <h1>Select a Child to View Their Meal Plan</h1>

        <!-- Dropdown to select child -->
        <select id="childSelect" onchange="loadMealPlan(this.value)">
            <option value="">Select a child...</option>
            <?php while ($child = mysqli_fetch_assoc($children_result)): ?>
                <option value="<?php echo $child['id']; ?>"><?php echo htmlspecialchars($child['child_name']); ?></option>
            <?php endwhile; ?>
        </select>

        <!-- Meal plan content -->
        <div id="meal-plan-container">
            <div class="loading">Loading meal plan...</div>
        </div>

        <a href="../pages/dashboard.php" class="back-btn">Back To Dashboard</a>
    </div>

    <script>
        function loadMealPlan(childId) {
            const mealPlanContainer = document.getElementById("meal-plan-container");
            mealPlanContainer.style.display = "block";
            mealPlanContainer.innerHTML = "<div class='loading'>Loading meal plan...</div>";

            if (childId === "") {
                mealPlanContainer.innerHTML = "<p>Select a child to view their meal plan.</p>";
                return;
            }

            fetch('load_meal_plans.php?id=' + childId)
                .then(response => response.text())
                .then(data => {
                    mealPlanContainer.innerHTML = data;
                })
                .catch(error => {
                    console.error('Error loading meal plan:', error);
                    mealPlanContainer.innerHTML = "<p>Error loading meal plan. Please try again later.</p>";
                });
        }
    </script>
</body>
</html>
