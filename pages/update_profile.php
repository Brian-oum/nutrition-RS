<?php
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include the database configuration file
include '../config/db.php';

// Database connection
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $id = $_SESSION['user_id']; // Use the logged-in user's ID from the session
    $newUsername = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $newEmail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $newPassword = $_POST['password']; // Get the raw password

    // Validate inputs
    if (empty($newUsername) || empty($newEmail) || empty($newPassword)) {
        die("All fields are required.");
    }

    // Hash the password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update query
    $sql = "UPDATE caregiver SET username = :username, email = :email, password = :password WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':username' => $newUsername,
        ':email' => $newEmail,
        ':password' => $hashedPassword,
        ':id' => $id
    ]);

    // Success message
    $_SESSION['success_message'] = "Profile updated successfully!";
    header("Location: update_profile.php"); // Redirect to the profile page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
<style>   
/*Update profile*/
.profile-form {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
}
.profile-form h2 {
    margin-bottom: 20px;
    font-size: 24px;
    text-align: center;
}
.profile-form input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
.profile-form button {
    width: 100%;
    padding: 10px;
    background-color: #28a745;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.profile-form button:hover {
    background-color: #218838;
}
</style>
</head>
<body>
    <div class="profile-form">
        <h2>Update Profile</h2>
        <form method="POST" action="">
            <!-- Hidden input for user ID -->
            <input type="hidden" name="id" value="1"> <!-- Replace with dynamic user ID -->

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>