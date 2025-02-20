<?php
include '../db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Handle Registration
    if (isset($_POST["register"])) {
        $username = trim($_POST["username"]);
        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM caregiver WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['message'] = "Email is already registered!";
        } else {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO caregiver (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Registration successful! You can now log in.";
                header("Location: ../index.php");
                exit();
            } else {
                $_SESSION['message'] = "Registration failed! " . $conn->error;
            }
        }
        $stmt->close();
        header("Location: ../index.php");
        exit();
    }

    // Handle Login
    if (isset($_POST["login"])) {
        $identifier = trim($_POST["identifier"]); // Can be email or username
        $password = trim($_POST["password"]);

        // Query to check if the user exists with either email or username
        $stmt = $conn->prepare("SELECT id, username, password FROM caregiver WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user["password"])) {
                // Store user info in session
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                header("Location: dashboard.php"); // Redirect to dashboard
                exit();
            } else {
                $_SESSION['message'] = "Incorrect password!";
            }
        } else {
            $_SESSION['message'] = "User not found!";
        }
        $stmt->close();
        header("Location: ../index.php");
        exit();
    }
}
?>
