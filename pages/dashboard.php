<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../index.php");
    exit();
}
$username = $_SESSION["username"] ?? "Guest";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Nutrition System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="dashboard-wrapper">
    <!-- Sidebar -->
    <nav class="sidebar">
        <h2 class="sidebar-title">Nutrition System</h2>
        <ul>
            <li><a href="details.php" class="load-page"><i class="fas fa-child"></i> Add Child Details</a></li>
            <li><a href="view_meal_plans.php" class="load-page"><i class="fas fa-utensils"></i> View Meal Plans</a></li>
            <li><a href="track_progress.php" class="load-page"><i class="fas fa-chart-line"></i> Track Progress</a></li>
            <li><a href="change_username.php" class="load-page"><i class="fas fa-user-edit"></i> Change Username</a></li>
            <li><a href="change_password.php" class="load-page"><i class="fas fa-lock"></i> Change Password</a></li>
            <li><a href="logout.php" id="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="content">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        <p>Manage your child's nutrition with personalized recommendations.</p>
        <div id="dynamic-content">
            <p>Select an option from the sidebar.</p>
        </div>
    </main>
</div>
<script src="../assets/js/script.js"></script>
</body>
</html>
