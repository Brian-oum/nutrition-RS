<?php
/*session_start();
include('../config/db.php');

// Ensure user is logged in
if (!isset($_SESSION["username"])) {
    echo "<script>alert('Please log in first.'); window.location.href='login.php';</script>";
    exit();
}

$parent_username = $_SESSION["username"];
$children_query = "SELECT * FROM children WHERE parent_username = '$parent_username' ORDER BY id DESC";
$children_result = mysqli_query($conn, $children_query);

if (mysqli_num_rows($children_result) == 0) {
    echo "<script>alert('No child details found! Please add your child’s details first.'); window.location.href='details.php';</script>";
    exit();
}*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>View Meal Plans</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="dash-container">
    <?php include '../includes/sidebar.php'; ?>

    <main class="content">
        <h2>View Meal Plans</h2>

        <div class="meal-wrapper">
            <form>
                <div class="child-selection">
                    <label for="childSelect"><i class="fas fa-child"></i> Select Child:</label>
                    <select id="childSelect" onchange="loadMealPlan(this.value)">
                        <option value="">=== Select a child ===</option>
                        <?php while ($child = mysqli_fetch_assoc($children_result)): ?>
                            <option value="<?php echo $child['id']; ?>"><?php echo htmlspecialchars($child['child_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </form>

            <div id="meal-plan-container" class="meal-plan-container">
                <p>Select a child to view their meal plan.</p>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>

<script>
function loadMealPlan(childId) {
    const mealPlanContainer = document.getElementById("meal-plan-container");
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

<script src="../assets/js/script.js"></script>
</body>
</html>
