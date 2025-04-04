<?php
session_start();
include '../config/db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Registration (step 1)
    if (isset($_POST["register"])) {
        $username = trim($_POST["username"]);
        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id from caregiver where email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['message'] = "Email is already registered!";
            header("Location: ../index.php");
            exit();
        } else {
            // Insert user details into the database directly
            $stmt = $conn->prepare("INSERT INTO caregiver (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if (!$stmt->execute()) {
                error_log("Database error:" . $stmt->error);
                $_SESSION['message'] = "Database error: " . $stmt->error;
                header("Location: ../index.php");
                exit();
            } else {
                $_SESSION['message'] = "Registration successful! Please Log in.";
                header("Location: ../index.php"); // Redirect to the login page
                exit();
            }
        }
    }

    // Login for existing users
    if (isset($_POST["login"])) {
        $identifier = trim($_POST["identifier"]);
        $password = trim($_POST["password"]);

        $stmt = $conn->prepare("SELECT id, username, email, password FROM caregiver WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user["password"])) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["email"] = $user["email"];

                header("Location: ./dashboard.php");
                exit();
            } else {
                $_SESSION['message'] = "Incorrect password!";
            }
        } else {
            $_SESSION['message'] = "User not found!";
        }
        header("Location: ../index.php"); // Redirect back to login page
        exit();
    }
}
?>
