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
    <style>
        .meal-container {
            width: 90%;
            max-width: 800px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            color: #333;
            text-transform: uppercase;
        }

        p {
            font-size: 16px;
            color: #555;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #28a745;
            color: white;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .premium {
            background-color: #f8d7da; /* Light red for premium meals */
            color: #333;
        }

        .affordable {
            background-color: #d4edda; /* Light green for affordable meals */
           
        }

        .no-meal {
            background: #ffe5e5;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 15px;
        }

        @media (max-width: 600px) {
            th, td {
                padding: 8px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="meal-container">
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
    </div>
</body>
</html>
