<?php
session_start();
include "../db.php"; // Include your database connection file

if (!isset($_GET["token"])) {
    die("Invalid token.");
}

$token = $_GET["token"];

// Check if the token is valid and not expired (e.g., within 1 hour)
$stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND created_at >= NOW() - INTERVAL 1 HOUR");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Invalid or expired token.");
}

$row = $result->fetch_assoc();
$email = $row["email"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <form action="update_password.php" method="POST">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <label for="password">New Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>