<?php
session_start();
include '../config/db.php'; // Include database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to change your password.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if new passwords match
    if ($new_password !== $confirm_password) {
        echo "<p style='color:red;'>New passwords do not match.</p>";
    } else {
        // Fetch the current hashed password from the database
        $query = $conn->prepare("SELECT password FROM caregiver WHERE id = ?");
        $query->bind_param("i", $user_id);
        $query->execute();
        $result = $query->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            die("User not found.");
        }

        // Verify old password
        if (!password_verify($old_password, $row['password'])) {
            echo "<p style='color:red;'>Incorrect current password.</p>";
        } else {
            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password in the database
            $update = $conn->prepare("UPDATE caregiver SET password = ? WHERE id = ?");
            $update->bind_param("si", $hashed_password, $user_id);

            if ($update->execute()) {
                echo "<p style='color:green;'>Password changed successfully!</p>";
            } else {
                echo "<p style='color:red;'>Error updating password.</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
            width: 300px;
        }
        h2 {
            text-align: center;
            color: green;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: green;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
        button:hover {
            background-color: darkgreen;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Change Password</h2>
    <form method="post" action="change_password.php">
        <label>Current Password</label>
        <input type="password" name="old_password" required>

        <label>New Password</label>
        <input type="password" name="new_password" required>

        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Change Password</button>
    </form>
</div>

</body>
</html>
