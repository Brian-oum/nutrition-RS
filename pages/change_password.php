<?php
session_start();
include('../config/db.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the caregiver's username from session
    $caregiver_username = $_SESSION['username']; // Assuming username is stored in session
    
    // Fetch the caregiver details based on the username
    $query = "SELECT id, password FROM caregiver WHERE username = '$caregiver_username'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $caregiver_id = $row['id']; // Get caregiver ID
        $stored_password = $row['password']; // Get stored password

        // Get user input from the form
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Hash the old password for comparison
        $old_password = mysqli_real_escape_string($conn, $old_password);
        $new_password = mysqli_real_escape_string($conn, $new_password);
        $confirm_password = mysqli_real_escape_string($conn, $confirm_password);

        // Check if the old password is correct
        if (password_verify($old_password, $stored_password)) {
            // Check if the new password and confirm password match
            if ($new_password === $confirm_password) {
                // Hash the new password before storing it
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

                // SQL to update the caregiver's password
                $sql = "UPDATE caregiver SET password = '$hashed_new_password' WHERE id = '$caregiver_id'";

                // Execute the query and check for success
                if (mysqli_query($conn, $sql)) {
                    $message = "Password updated successfully!";
                } else {
                    $message = "Error updating password: " . mysqli_error($conn);
                }
            } else {
                $message = "New password and confirm password do not match!";
            }
        } else {
            $message = "Old password is incorrect!";
        }
    } else {
        $message = "Caregiver not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password</title>
    <style>
        /* Basic styling for the page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-size: 16px;
            color: #555;
        }

        input[type="password"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 5px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .message {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #4CAF50;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            color: #4CAF50;
            text-decoration: none;
            font-size: 16px;
            padding: 8px 16px;
            border: 2px solid #4CAF50;
            border-radius: 30px;
            text-align: center;
        }

        .back-btn:hover {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Update Your Password</h1>
    <?php if (isset($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="old_password">Old Password:</label>
            <input type="password" id="old_password" name="old_password" required placeholder="Enter your old password">
        </div>

        <div class="form-group">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required placeholder="Enter your new password">
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your new password">
        </div>

        <input type="submit" value="Update Password">
    </form>

    <a href="../pages/dashboard.php" class="back-btn">Back To Dashboard</a>
</div>

</body>
</html>
