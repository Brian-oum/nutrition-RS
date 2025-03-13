<?php
session_start();
include('../config/db.php');

$parent_username = $_SESSION["username"];
$child_query = "SELECT * FROM children WHERE parent_username = '$parent_username' ORDER BY id DESC LIMIT 1";
$child_result = mysqli_query($conn, $child_query);
$child = mysqli_fetch_assoc($child_result);

if (!$child) {
    echo "<script>alert('No child details found! Please add your child’s details first.'); window.location.href='details.php';</script>";
    exit();
}

$dob = new DateTime($child['dob']);
$today = new DateTime();
$age_interval = $dob->diff($today);
$age_in_months = ($age_interval->y * 12) + $age_interval->m;
$weight = $child['weight'];
$gender = $child['gender'];

if ($age_in_months <= 6) {
    $age_group = "0-6 months";
} elseif ($age_in_months <= 12) {
    $age_group = "6-12 months";
} elseif ($age_in_months <= 24) {
    $age_group = "1-2 years";
} elseif ($age_in_months <= 36) {
    $age_group = "2-3 years";
} else {
    $age_group = "4-5 years";
}

$meal_query = "SELECT * FROM meal_plans WHERE age_group = '$age_group' 
               AND min_weight <= '$weight' AND max_weight >= '$weight'
               ORDER BY FIELD(meal_time, 'Breakfast', 'Mid-Morning Snack', 'Lunch', 'Afternoon Snack', 'Dinner')";
$meal_result = mysqli_query($conn, $meal_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Meal Plans</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="meal-container">
        <h2>Meal Plan for <?php echo htmlspecialchars($child['child_name']); ?> (<?php echo $gender; ?>)</h2>
        <p><strong>Age:</strong> <?php echo $age_interval->y; ?> years, <?php echo $age_interval->m; ?> months</p>
        <p><strong>Weight:</strong> <?php echo $weight; ?> kg</p>

        <ul>
            <?php if (mysqli_num_rows($meal_result) > 0): ?>
                <?php while ($meal = mysqli_fetch_assoc($meal_result)): ?>
                    <li>
                        <strong><?php echo $meal['meal_time']; ?>:</strong> <?php echo $meal['meal_name']; ?>
                        <p><?php echo $meal['description']; ?></p>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li><p>No meal plan found for this weight range. Consult a nutritionist.</p></li>
            <?php endif; ?>
        </ul>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>