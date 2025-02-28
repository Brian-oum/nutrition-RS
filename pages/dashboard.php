<?php
session_start();

if(!isset($_SESSION["user_id"])){
    header("location: ../index.php");
}

$username = isset($_SESSION["username"]) ? $_SESSION["username"] : "Guest"
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Nutrition System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dash-body">
    <header class="nutrition-heading">
        <h2>Nutrition Recommender System</h2>
    </header>

    <div class="dashboard-container">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        <p>Manage your child's nutrition with personalized recommendations.</p>

        <nav>
            <ul class="nav-grid">
                <li><a href="./details.html">Add Child Details</a></li>
                <li><a href="#">View Meal Plans</a></li>
                <li><a href="#">Track Progress</a></li>
                <li><a href="#">Change Username</a></li>
                <li><a href="#">Change Password</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Nutrition Recommender System. All Rights Reserved.</p>
    </footer>
    </div>
</body>
</html>
