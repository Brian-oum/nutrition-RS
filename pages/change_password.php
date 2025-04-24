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
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <style>
        
    </style>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="dash-container">
    <?php include '../includes/sidebar.php'; ?>

    <main class="content">
        <h2>Update Your Password</h2>

        <?php if (isset($message)): ?>
            <div class="alert-message"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="form-wrapper">
            <form action="" method="POST">
                <div class="form-group">
                    <label for="old_password"><i class="fas fa-lock"></i> Old Password:</label>
                    <input type="password" id="old_password" name="old_password" required placeholder="Enter your old password">
                </div>

                <div class="form-group">
                    <label for="new_password"><i class="fas fa-lock-open"></i> New Password:</label>
                    <input type="password" id="new_password" name="new_password" required placeholder="Enter your new password">
                </div>

                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your new password">
                </div>

                <button type="submit" class="btn-primary">Update Password</button>
            </form>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/script.js"></script>

</body>
</html>