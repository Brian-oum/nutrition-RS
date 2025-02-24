<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="success-container">
        <!-- Tick in a circle -->
        <div class="tick-circle"></div>

        <!-- Success message -->
        <div class="success-message">Details added successfully!</div>

        <!-- Button to go back to the homepage -->
        <a href="./dashboard.php" class="home-button">Go Back to Homepage</a>
    </div>
</body>
</html>