<?php
include('../config/db.php');

if (!isset($_GET['id'])) {
    echo "<p class='error'>No child selected!</p>";
    exit();
}

$child_id = intval($_GET['id']);
$child_query = "SELECT * FROM children WHERE id = '$child_id'";
$child_result = mysqli_query($conn, $child_query);

if (mysqli_num_rows($child_result) == 0) {
    echo "<p class='error'>Child not found!</p>";
    exit();
}

$child = mysqli_fetch_assoc($child_result);
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

<h2>Meal Plan for <?php echo htmlspecialchars($child['child_name']); ?> (<?php echo $gender; ?>)</h2>
<p><strong>Age:</strong> <?php echo $age_interval->y; ?> year(s), <?php echo $age_interval->m; ?> month(s)</p>
<p><strong>Weight:</strong> <?php echo $weight; ?> kg</p>

<?php if (mysqli_num_rows($meal_result) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Meal Time</th>
                <th>Meal Name</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($meal = mysqli_fetch_assoc($meal_result)): ?>
                <tr class="<?php echo ($meal['meal_type'] == 'premium') ? 'premium' : 'affordable'; ?>">
                    <td><?php echo htmlspecialchars($meal['meal_time']) . ' (' . ucfirst(htmlspecialchars($meal['meal_type'])) . ')'; ?></td>
                    <td><?php echo htmlspecialchars($meal['meal_name']); ?></td>
                    <td><?php echo htmlspecialchars($meal['description']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="no-meal">No meal plan found for this weight range. Consult a nutritionist.</p>
<?php endif; ?>
