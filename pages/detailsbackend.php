<?php
session_start();
include "../db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $age = $_POST["age"];
    $weight = $_POST["weight"];
    $height = $_POST["height"];

    $sql = "INSERT INTO children (name, age, weight, height) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidd", $name, $age, $weight, $height);

    if ($stmt->execute()) {
        echo "Child details added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>