<?php
//import the database
include 'db.php';

session_start();

$message = "";

// Fixing session message retrieval
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["register"])) {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
        $age = $_POST["age"];
        $weights = $_POST["weight"];

        // Using prepared statements to prevent SQL Injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['message'] = "<div class='alert error'>Email already exists!</div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, age, weight) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $name, $email, $password, $age, $weight);

            if ($stmt->execute()) {
                $_SESSION['message'] = "<div class='alert success'>Registration successful! Proceed to Log in.</div>";
            } else {
                $_SESSION['message'] = "<div class='alert error'>Registration failed! " . $conn->error . "</div>";
            }

        }   
    }
     // Redirect to prevent resubmission
     header("Location: auth.php");
     exit();
        }

        $stmt->close();
        // Redirect to prevent form resubmission
        header("Location: auth.php");
        exit();
    

    if (isset($_POST["login"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];

        // Using prepared statements
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

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

        $stmt->close();
        // Redirect to prevent form resubmission
        header("Location: auth.php");
        exit();
    }

?>
