<?php
session_start();
include('../config/db.php');

$parent_username = $_SESSION["username"];
$children_query = "SELECT * FROM children WHERE parent_username = '$parent_username' ORDER BY id DESC";
$children_result = mysqli_query($conn, $children_query);

if (mysqli_num_rows($children_result) == 0) {
    echo "<script>alert('No child details found! Please add your child’s details first.'); window.location.href='details.php';</script>";
    exit();
}
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
        <h1>Search for a Child's Meal Plan</h1>
        
        <!-- 🔍 Search Box -->
        <input type="text" id="searchChild" placeholder="Search child by name..." onkeyup="searchChild()">
        <div class="search-results" id="search-results"></div>

        <!-- 🚀 This will update dynamically -->
        <div id="meal-plan-container">
            <p>Select a child to view their meal plan.</p>
        </div>
    </div>
    <script src="../assets/js/script.js"></script>
</body>
</html>