<?php
session_start();
include('../config/db.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the caregiver's username from session
    $caregiver_username = $_SESSION['username']; // Assuming username is stored in session
    
    // Fetch the caregiver ID based on the username
    $query = "SELECT id FROM caregiver WHERE username = '$caregiver_username'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $caregiver_id = $row['id']; // Get caregiver ID
        
        // Get new username from form submission
        $new_username = $_POST['new_username']; // New username from form submission
        
        // Sanitize inputs to prevent SQL injection
        $new_username = mysqli_real_escape_string($conn, $new_username);
        
        // SQL to update the caregiver username
        $sql = "UPDATE caregiver SET username = '$new_username' WHERE id = '$caregiver_id'";
        
        // Execute the query and check for success
        if (mysqli_query($conn, $sql)) {
            $message = "Username updated successfully!";
        } else {
            $message = "Error updating username: " . mysqli_error($conn);
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
    <title>Update Username</title>
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

        input[type="text"] {
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
            width: 100%;
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
    <h1>Update Your Username</h1>
    <?php if (isset($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="new_username">New Username:</label>
            <input type="text" id="new_username" name="new_username" required placeholder="Enter your new username">
        </div>
        <input type="submit" value="Update Username">
    </form>

    <a href="../pages/dashboard.php" class="back-btn">Back To Dashboard</a>
</div>

</body>
</html>
