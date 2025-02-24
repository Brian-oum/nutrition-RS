<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:rgb(57, 30, 179);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .success-container {
            text-align: center;
            background-color: #fff;
            padding: 5%;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
        }
        .tick-circle {
            width: 20vw;
            height: 20vw;
            max-width: 80px;
            max-height: 80px;
            border-radius: 50%;
            background-color:rgb(57, 30, 179);
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
        }
        .tick-circle::after {
            content: "✓";
            font-size: 10vw;
            color: #fff;
            max-font-size: 40px;
        }st
        .success-message {
            font-size: 5vw;
            color: #333;
            margin-bottom: 20px;
            font-size: clamp(18px, 5vw, 24px);
        }
        .home-button {
            background-color: rgb(57, 30, 179);
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: clamp(14px, 4vw, 16px);
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .home-button:hover {
            background-color: rgb(74, 37, 177);
        }
    </style>
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