<?php
include('../config/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p class='error'>Invalid or no child selected!</p>";
    exit();
}

$child_id = intval($_GET['id']);

// Fetch child details securely
$stmt = $conn->prepare("SELECT * FROM children WHERE id = ?");
$stmt->bind_param("i", $child_id);
$stmt->execute();
$child_result = $stmt->get_result();

if ($child_result->num_rows === 0) {
    echo "<p class='error'>Child not found!</p>";
    exit();
}

$child = $child_result->fetch_assoc();
$stmt->close();

// Calculate age in months
$dob = new DateTime($child['dob']);
$today = new DateTime();
$age_interval = $dob->diff($today);
$age_in_months = ($age_interval->y * 12) + $age_interval->m;

// Assign age group
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

$weight = $child['weight'];
$gender = $child['gender'];

// Fetch meal plans securely
$stmt = $conn->prepare(
    "SELECT * FROM meal_plans 
     WHERE age_group = ? AND min_weight <= ? AND max_weight >= ? 
     ORDER BY FIELD(meal_time, 'Breakfast', 'Mid-Morning Snack', 'Lunch', 'Afternoon Snack', 'Dinner')"
);
$stmt->bind_param("sdd", $age_group, $weight, $weight);
$stmt->execute();
$meal_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Plan for <?php echo htmlspecialchars($child['child_name']); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<style>
/* 🎯 Heading Styling */
.load-container h2 {
    font-size: 24px;
    color: #333;
    margin-bottom: 10px;
}

/* 📌 Basic Text Styling */
.load-container p {
    font-size: 16px;
    color: #666;
    margin: 5px 0;
}

/* 🍽️ Meal Plan Table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: white;
    border-radius: 8px;
    overflow: hidden;
}

/* 🏷️ Table Headers */
table th {
    background: #228B22;
    color: white;
    padding: 12px;
    text-align: left;
    font-size: 16px;
}

table td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    font-size: 15px;
}

/* ✨ Hover effect for table rows */
/* ✨ Hover effect for table rows */
table tr:hover {
    transform: scale(1.02); /* Slightly enlarge the row */
    transition: transform 0.3s ease-in-out; /* Smooth transition */
}



.premium {
    background: #228B22;
    font-weight: bold;
    color: white;
}

.affordable {
    background: white;
    color: #228B22;
}

/* ❌ No Meal Message */
.no-meal {
    font-size: 16px;
    color: #ff0000;
    margin-top: 20px;
    font-weight: bold;
}

/* 📱 Responsive Design */
@media (max-width: 600px) {
    .load-container {
        padding: 15px;
        width: 90%;
    }
    
    table th, table td {
        font-size: 14px;
        padding: 8px;
    }
}

</style>
<body>

    <div class="load-container">
        <h2>Meal Plan for <?php echo htmlspecialchars($child['child_name']); ?> (<?php echo htmlspecialchars($gender); ?>)</h2>
        <p><strong>Age:</strong> <?php echo $age_interval->y; ?> year(s), <?php echo $age_interval->m; ?> month(s)</p>
        <p><strong>Weight:</strong> <?php echo htmlspecialchars($weight); ?> kg</p>

        <?php if ($meal_result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Meal Time</th>
                        <th>Meal Name</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($meal = $meal_result->fetch_assoc()): ?>
                        <tr class="<?php echo ($meal['meal_type'] === 'Premium') ? 'premium' : 'affordable'; ?>">
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
    </div>

</body>
</html>
