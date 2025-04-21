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
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="dash-container">
    <?php include '../includes/sidebar.php'; ?>

    <main class="content">
        <h2>Update Your Username</h2>

        <?php if (isset($message)): ?>
            <div class="alert-message"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="form-wrapper">
            <form action="" method="POST">
                <div class="form-group">
                    <label for="new_username"><i class="fas fa-user-edit"></i> New Username:</label>
                    <input type="text" id="new_username" name="new_username" required placeholder="Enter your new username">
                </div>
                <button type="submit" class="btn-primary">Update Username</button>
            </form>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>
