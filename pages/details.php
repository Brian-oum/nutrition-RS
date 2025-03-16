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
<style>
    .detail-container h2{
        text-align: center;
        text-transform: uppercase;
    }
       .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        form input,
        form select,
        form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        form button:hover {
            background: #218838;
        }
    </style>
<body>
    <div class="detail-container">
        <h2>Add Child Details</h2>
        <form action="details.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" name="child_name" placeholder="Child's Name" required>

            <label for="gender">Gender:</label>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <label for="dob">Date of Birth:</label>
            <input type="date" name="dob" required>

            <label for="wieght">Weight</label>
            <input type="number" name="weight" placeholder="Weight (kg)" required>

            <label for="height">Height</label>
            <input type="number" name="height" placeholder="Height (cm)" required>

            <label for="dietary_restrictions">Dietary Restrictions (Optional):</label>
            <textarea name="dietary_restrictions" placeholder="Any dietary restrictions?"></textarea>

            <button type="submit">Save Details</button>
        </form>
    </div>
</body>
</html>
