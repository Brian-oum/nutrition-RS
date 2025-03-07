<?php
session_start();
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : "Guest";
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
            <li>
                <a href="#" onclick="loadContent('details.php')" data-tooltip="Add Child Details">
                    <i class="fas fa-child"></i>
                    <span class="link-text">Add Child Details</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="loadContent('view_meal_plans.php')" data-tooltip="View Meal Plans">
                    <i class="fas fa-utensils"></i>
                    <span class="link-text">View Meal Plans</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="loadContent('track_progress.php')" data-tooltip="Track Progress">
                    <i class="fas fa-chart-line"></i>
                    <span class="link-text">Track Progress</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="loadContent('change_username.php')" data-tooltip="Change Username">
                    <i class="fas fa-user-edit"></i>
                    <span class="link-text">Change Username</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="loadContent('change_password.php')" data-tooltip="Change Password">
                    <i class="fas fa-lock"></i>
                    <span class="link-text">Change Password</span>
                </a>
            </li>
            <li>
                <a href="logout.php" data-tooltip="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="link-text">Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content Area -->
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
