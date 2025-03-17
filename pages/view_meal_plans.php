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
<style>
    .meal-container h1 {
    font-size: 22px;
    margin-bottom: 15px;
    color: #444;
}

/* Search Box */
#searchChild {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    outline: none;
    transition: 0.3s;
}

#searchChild:focus {
    border-color: #007bff;
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
}

/* Search Results */
.search-results {
    margin-top: 10px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    max-height: 150px;
    overflow-y: auto;
    display: none;
}

.search-results .search-item {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid #ddd;
    transition: 0.2s;
}

.search-results .search-item:hover {
    background-color: #f0f0f0;
}

/* Meal Plan Container */
#meal-plan-container {
    margin-top: 20px;
    padding: 15px;
    background: #fafafa;
    border-radius: 8px;
    border: 1px solid #ddd;
}

#meal-plan-container p {
    color: #666;
}
</style>
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