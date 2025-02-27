<?php
session_start();
include '../db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Handle Registration (Step 1)
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
            header("Location: ../index.php");
            exit();
        } else {
            // Store temporary user details in session
            $_SESSION['temp_user'] = ['username' => $username, 'email' => $email, 'password' => $hashed_password];

            // Redirect to email verification page
            header("Location: ./email_verification.html?email=" . urlencode($email));
            exit();
        }
    }

    // Handle OTP Verification (Step 3)
    if (isset($_POST["verify_otp"])) {
        if (!isset($_SESSION['otp']) || $_SESSION['otp'] != $_POST["otp"]) {
            $_SESSION['message'] = "Invalid OTP!";
            header("Location: ./otp_verification.html");
            exit();
        }
    
        $username = $_SESSION['temp_user']['username'];
        $email = $_SESSION['temp_user']['email'];
        $password = $_SESSION['temp_user']['password']; // Already hashed
    
        $stmt = $conn->prepare("INSERT INTO caregiver (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
    
        if ($stmt->execute()) {
            $_SESSION['message'] = "Registration successful! Please log in.";
            unset($_SESSION['otp']);
            unset($_SESSION['temp_user']);
            header("Location: ../index.php");
            exit();
        } else {
            $_SESSION['message'] = "Error saving user: " . $stmt->error;
            header("Location: ../otp_verification.html");
            exit();
        }
    }
    

    // Handle Login
    if (isset($_POST["login"])) {
        $identifier = trim($_POST["identifier"]);
        $password = trim($_POST["password"]);
    
        $stmt = $conn->prepare("SELECT id, username, password FROM caregiver WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user["password"])) { // Correct way to compare passwords
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                header("Location: dashboard.php");
                exit();
            } else {
                $_SESSION['message'] = "Incorrect password!";
            }
        } else {
            $_SESSION['message'] = "User not found!";
        }
        header("Location: ../index.php");
        exit();
    }
    
}
?>