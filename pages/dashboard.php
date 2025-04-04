<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../index.php");
    exit();
}
$username = $_SESSION["username"] ?? "Guest";

date_default_timezone_set("Africa/Nairobi");

$hour = date("H");
$minutes = date("i");
$current_time = date("H:i");
if($hour >= 5 && $hour < 12) {
    $greeting = "Good Morning";
} elseif ($hour >= 12 && $hour < 17) {
    $greeting = "Good Afternoon";
} elseif ($hour >= 17 && $hour < 23) {
    $greeting = "Good Evening";
} else {
    $greeting = "Good Night";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Nutrition System</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<style>
    .user-manual {
        background-color: #f9f9f9;
        padding: 20px;
        max-width: 800px;
        margin: 0 auto;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .user-manual h3 {
        text-align: center;
        font-size: 24px;
        margin-bottom: 20px;
        color: #333;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        font-size: 18px;
        color: #333;
        font-weight: bold;
    }

    .form-group p {
        font-size: 16px;
        color: #555;
        margin-top: 5px;
    }
</style>
<body>

    <!-- Header -->
    <div class="dashboard-header">
        <button id="toggle-btn"><i class="fas fa-bars"></i></button>
        <h2 id="greeting"><?php echo $greeting . ", " . htmlspecialchars($username) . "!"; ?></h2>
        <h3 id="current-time"><?php echo " $current_time HRS"; ?></h3>
        <a href="#" class="faq-icon" title="Frequently Asked Questions" id="faq-btn">
            <i class="fas fa-comments"></i>
        </a>
    </div>

    <nav class="sidebar">
        <ul>
            <li><a href="details.php" class="load-page" data-title="Add Child Details"><i class="fas fa-child"></i><span>Add Child Details</span></a></li>
            <li><a href="update-details.php" class="load-page" data-title="Update Child Details"><i class="fas fa-user-edit"></i><span>Update Child Details</span></a></li>
            <li><a href="meal_plans.php" class="load-page" data-title="View Meal Plans"><i class="fas fa-utensils"></i><span>View Meal Plans</span></a></li>
            <li><a href="track_progress.php" class="load-page" data-title="Track Progress"><i class="fas fa-chart-line"></i><span>Track Progress</span></a></li>
            <li><a href="change_username.php" class="load-page" data-title="Change Username"><i class="fas fa-user-edit"></i><span>Change Username</span></a></li>
            <li><a href="change_password.php" class="load-page" data-title="Change Password"><i class="fas fa-lock"></i><span>Change Password</span></a></li>
            <li><a href="logout.php" id="logout" data-title="Logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
        </ul>
    </nav>

    <!-- FAQ Modal -->
    <div id="faq-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <
        </div>
    </div>

    <!-- Main Content -->
    <main class="content">
        <div id="dynamic-content">
            <h2>Nutrition Recommender System</h2>
            <p>Select an option from the sidebar || Manage your child's nutrition with personalized recommendations.</p>
            <div class="user-manual">
    <h3>How to Use the System</h3>
    <form>
        <div class="form-group">
            <label for="add-child">Add Child Details:</label>
            <p id="add-child">Click on "Add Child Details" and fill in the required information. <strong>Note:</strong> Insert relevant information for the child.</p>
        </div>
        <div class="form-group">
            <label for="update-child">Update Child Details:</label>
            <p id="update-child">Go to "Update Child Details" to modify any information.</p>
        </div>
        <div class="form-group">
            <label for="view-meal">View Meal Plans:</label>
            <p id="view-meal">Check meal plans recommended based on the child's details.</p>
        </div>
        <div class="form-group">
            <label for="track-progress">Track Progress:</label>
            <p id="track-progress">Monitor the child’s growth and nutrition status.</p>
        </div>
        <div class="form-group">
            <label for="change-credentials">Change Username/Password:</label>
            <p id="change-credentials">Update your login credentials for security.</p>
        </div>
        <div class="form-group">
            <label for="logout">Logout:</label>
            <p id="logout">Click "Logout" to exit the system.</p>
        </div>
    </form>
</div>
        </div>
        <div id="progress-details"></div>
    </main>


    <script src="../assets/js/script.js"></script>
    <script>
    </script>
</body>
</html>
