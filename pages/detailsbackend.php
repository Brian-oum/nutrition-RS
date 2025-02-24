<?php
session_start();
include "../db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $name = trim($_POST["name"]);
    $age = intval($_POST["age"]);
    $weight = floatval($_POST["weight"]);
    $height = floatval($_POST["height"]);

    // Check if required fields are not empty
    if (empty($name) || $age <= 0 || $weight <= 0 || $height <= 0) {
        echo "Please fill in all fields with valid data.";
        exit();
    }

    // Prepare and execute SQL statement
    $sql = "INSERT INTO children (name, age, weight, height) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }

    $stmt->bind_param("sidd", $name, $age, $weight, $height);

    if ($stmt->execute()) {
        echo "Child details added successfully!";
        // Redirect to another page to prevent form resubmission
        header("Location: ./success.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>