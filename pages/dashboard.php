<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: dashboard.php");
    exit();
}
$username = $_SESSION["username"] ?? "Guest";

date_default_timezone_set("Africa/Nairobi");
$hour = date("H");

if ($hour >= 5 && $hour < 12) {
    $greeting = "Good Morning";
} elseif ($hour >= 12 && $hour < 17) {
    $greeting = "Good Afternoon";
} elseif ($hour >= 17 && $hour < 23) {
    $greeting = "Good Evening";
} else {
    $greeting = "Good Night";
}

include '../config/db.php';

// Set MySQL session timezone to Africa/Nairobi to avoid mismatch
$conn->query("SET time_zone = 'Africa/Nairobi'");

// Get all subscriptions for the user, ordered by expiry date
$expiry_result = $conn->query("SELECT expiry_date FROM payments WHERE username='$username' ORDER BY expiry_date DESC");

$subscriptions_message = "";

if ($expiry_result && $expiry_result->num_rows > 0) {
    $subscriptions_message = "<h3>Your Subscriptions:</h3><ul>";
    
    while ($row = $expiry_result->fetch_assoc()) {
        $expiry = new DateTime($row['expiry_date']);
        $now = new DateTime();

        // Display subscription status for each subscription
        if ($expiry > $now) {
            $interval = $now->diff($expiry);
            $subscriptions_message .= "<li>⏳ Subscription active - <strong>{$interval->d} days, {$interval->h} hours, {$interval->i} minutes</strong> remaining (until {$expiry->format('d M Y H:i')})</li>";
        } else {
            $subscriptions_message .= "<li>❌ Subscription expired on <strong>{$expiry->format('d M Y H:i')}</strong></li>";
        }
    }

    $subscriptions_message .= "</ul>";
} else {
    $subscriptions_message = "⚠️ No subscriptions found.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Nutrition Dashboard</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <?php include '../includes/header.php'; ?>

<div class="dash-container">
    <?php include '../includes/sidebar.php'; ?>

    <main class="content">
      <?php
        if (isset($_GET['page'])) {
          $page = basename($_GET['page']);
          $filepath = "../pages/$page";

          if (file_exists($filepath)) {
              include $filepath;
          } else {
              echo "<h2>Page not found.</h2>";
          }
        } else {
          echo "
            <h2>Nutrition Recommender System</h2>
            <p style='margin-top: 1rem; font-weight: bold; color: #007bff;'>$subscriptions_message</p>
            <p>Select an option from the sidebar || Manage your child's nutrition with personalized recommendations.</p>
            <div class='user-manual'>
              <h3>How to Use the System</h3>
              <div class='form-group'>
                <label>Add Child Details:</label>
                <p>Click on \"Add Child Details\" and fill in the required information.</p>
              </div>
              <div class='form-group'>
                <label>Update Child Details:</label>
                <p>Modify the child's info from the update page.</p>
              </div>
              <div class='form-group'>
                <label>View Meal Plans:</label>
                <p>See recommended meal plans tailored to each child.</p>
              </div>
              <div class='form-group'>
                <label>Track Progress:</label>
                <p>Follow up on a child's growth and weight trends.</p>
              </div>
              <div class='form-group'>
                <label>Change Username/Password:</label>
                <p>Secure your account regularly.</p>
              </div>
              <div class='form-group'>
                <label>Logout:</label>
                <p>Click \"Logout\" to safely exit.</p>
              </div>
            </div>
          ";
        }
      ?>
    </main>
  </div>

  <?php include '../includes/footer.php'; ?>
</body>
</html>
