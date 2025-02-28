<?php
session_start();
include '../db.php';// database connection

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    
    //registeration (step1)
    if(isset($_POST["register"])){
        $username = trim($_POST["username"]);
        $email = trim($_POST["email"]);
        $password =  trim($_POST["password"]);
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // check if email already exists
        $stmt = $conn->prepare("SELECT id from caregiver where email = ?");
        $stmt-> bind_param("s", $email);
        $stmt-> execute();
        $stmt-> store_result();

        if($stmt->num_rows > 0){
            $_SESSION['message'] = "Email is already registered!";
            header("Location: ../index.php");
            exit();
        }else{
            // temporary storing user registration details
            $_SESSION['temp_user']=[
                'username' => $username,
                'email' => $email,
                'password' => $hashed_password
            ];

            // redirect to email verification page
            header("Location: ./email_verification.php?email=" .urlencode($email));
            exit();
        }
    }

    // handle OTP Verification for user email verification
    if(isset($_POST["verify_otp"])){
        if(!isset($_SESSION['temp_user'])){
            echo json_encode(["status"=> "error", "message"=> "Session expired! Please try again"]);
            exit();
        }
        $username = $_SESSION['temp_user']['username'];
        $email = $_SESSION['temp_user']['email'];
        $password = $_SESSION['temp_user']['password'];
    
        // inserting user details after verification
        $stmt = $conn->prepare("INSERT INTO caregiver (username, email, password, created_at) VALUES(?, ?, ?, NOW())");
        $stmt->bind_param("sss", $username, $email, $password);
    
        if(!$stmt-> execute()){
            error_log("Database error:" .$stmt->error);
            echo json_encode(["status"=> "error", "message"=>"Database error: " .$stmt->error]);
            exit();
        } else{
            $_SESSION['message'] = "Registration successful! Please Log in. ";
            unset($_SESSION['temp_user']);
            echo json_encode(["status"=> "success", "message"=> "Registration successful"]);
            exit();
        }
    }
}

// login for existing users
if(isset($_POST["login"])){
    $identifier = trim($_POST["identifier"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT id, username, password FROM caregiver WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
        if(password_verify($password,$user["password"])){
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];

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
?>