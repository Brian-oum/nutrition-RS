<?php
include 'db.php';
session_start();

$message = "";

If (isset($_SESSION['message'])){
    $message = $_SESSION['messae'];
    unset($_SESSION['message']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    if (isset($_POST["register"])){
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
        $age = $_POST["age"];
        $weight = $_POST["weight"];

        $check_email = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($check_email);

        if ($result->num_rows > 0) {
            $_SESSION['message'] = "<div class='alert error'>Email already exists!</div>";
        } else {
            $sql = "INSERT INTO users (name, email, password, age, weight) VALUES ('$name', '$email', '$password', '$age', '$weight')";
            if ($conn->query($sql) === true) {
                $_SESSION['message'] = "<div class='alert success'>Registration successful! Proceed to Log in.</div>";
            } else {
                $_SESSION['message'] = "<div class='alert error'>Registration failed! " . $conn->error . "</div>";
            }
        }   
    }
     // Redirect to prevent resubmission
     header("Location: auth.php");
     exit();

     if (isset($_POST["login"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];

        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user["password"])) {
                $_SESSION["caregiver_id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["role"] = $user["role"];
                header("Location: ../index.php");
                exit();
            } else {
                $_SESSION['message'] = "<div class='alert error'>Incorrect Password</div>";
            }
        } else {
            $_SESSION['message'] = "<div class='alert error'>User not found!</div>";
        }

        // Redirect to prevent resubmission
        header("Location: auth.php");
        exit();
    }
}
?>

