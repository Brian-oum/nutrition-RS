<?php
session_start();
include('../config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parent_username = $_SESSION["username"];
    $child_name = $_POST["child_name"];
    $gender = $_POST["gender"];  // Capture gender input
    $dob = $_POST["dob"];
    $weight = $_POST["weight"];
    $height = $_POST["height"];
    $dietary_restrictions = $_POST["dietary_restrictions"];

    $query = "INSERT INTO children (parent_username, child_name, gender, dob, weight, height, dietary_restrictions) 
              VALUES ('$parent_username', '$child_name', '$gender', '$dob', '$weight', '$height', '$dietary_restrictions')";
    mysqli_query($conn, $query);

    echo "<script>alert('Child details added successfully!'); window.location.href='dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Child Details</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="detail-container">
        <h2>Add Child Details</h2>
        <form action="details.php" method="POST">
            <input type="text" name="child_name" placeholder="Child's Name" required>

            <label for="gender">Gender:</label>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <label for="dob">Date of Birth:</label>
            <input type="date" name="dob" required>

            <input type="number" name="weight" placeholder="Weight (kg)" required>
            <input type="number" name="height" placeholder="Height (cm)" required>

            <label for="dietary_restrictions">Dietary Restrictions (Optional):</label>
            <textarea name="dietary_restrictions" placeholder="Any dietary restrictions?"></textarea>

            <button type="submit">Save Details</button>
        </form>
        <a href="./dashboard.php">back</a>
    </div>
</body>
</html>
