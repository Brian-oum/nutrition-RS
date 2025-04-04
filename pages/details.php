<?php
session_start();
include("../config/db.php");

// Check if the user is logged in
if (!isset($_SESSION["username"])) {
    echo "<script>alert('You must be logged in to add child details.'); window.location.href='login.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parent_username = $_SESSION["username"]; // Get logged-in user's email
    $child_name = $_POST["name"];
    $gender = $_POST["gender"];
    $dob = $_POST["dob"];
    $height = $_POST["height"];
    $weight = $_POST["weight"];
    $dietary_restrictions = $_POST["diet"];

    // Prepare SQL statement to prevent SQL injection
    $query = "INSERT INTO children (parent_username, child_name, gender, dob, height, weight, dietary_restrictions)
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ssssdds", $parent_username, $child_name, $gender, $dob, $height, $weight, $dietary_restrictions);

        if ($stmt->execute()) {
            echo "<script>alert('Child details added successfully!'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Error: Unable to save details.');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Error preparing the statement.');</script>";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Child Details</title>
    <style>
        /* Reset some default styles */
        .child-form-container {
            max-width: 700px;
            background: #fff;
            padding: 25px;
            margin: 40px auto;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            transition: 0.3s ease-in-out;
        }

        .child-form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #444;
        }

        .child-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .child-form input,
        .child-form select,
        .child-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            outline: none;
        }

        .child-form input:focus,
        .child-form select:focus,
        .child-form textarea:focus {
            border-color: #5b9bd5;
            box-shadow: 0 0 5px rgba(91, 155, 213, 0.5);
        }

        .child-form button {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        .child-form button:hover {
            background-color: #45a049;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            text-align: center;
            color: #4CAF50;
            text-decoration: none;
            font-size: 16px;
        }

        .back-btn:hover {
            text-decoration: underline;
        }

        .child-form textarea {
            resize: vertical;
        }
    </style>
</head>
<body>

<div class="child-form-container">
    <h2>Add Child's Details</h2>
    
    <form class="child-form" action="details.php" method="POST">
        <label for="name">Child's Name:</label>
        <input type="text" id="name" name="name" placeholder="Enter child's name e.g., Maurice" required>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="">-- Please Select Gender --</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" required>

        <label for="weight">Weight (kg):</label>
        <input type="number" id="weight" name="weight" placeholder="Enter weight in kg" required>

        <label for="height">Height (cm):</label>
        <input type="number" id="height" name="height" placeholder="Enter height in cm" required>

        <label for="diet">Dietary Restrictions:</label>
        <textarea id="diet" name="diet" placeholder="Enter any dietary restrictions" rows="3"></textarea>

        <button type="submit">Save Details</button>
    </form>
    <a href="../pages/dashboard.php" class="back-btn">Back To Dashboard</a>

</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
