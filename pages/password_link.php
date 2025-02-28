<?php
session_start();
include "../db.php"; // Include your database connection file

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "./PHPMailer-master/src/Exception.php";
require '../assets/PHPMailer-master/src/PHPMailer.php';
require '../assets/PHPMailer-master/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize the email input
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM caregiver WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(32));

        // Store the token in the password_resets table
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $token);
        $stmt->execute();

        // Send an email with the reset link using PHPMailer
        $reset_link = "http://yourwebsite.com/reset_password.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Click the link below to reset your password:<br><br><a href='$reset_link'>Reset Password</a><br><br>If you did not request this, please ignore this email.";

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@gmail.com'; // Your email
            $mail->Password = 'your-email-password'; // Your email password
            $mail->SMTPSecure = 'tls'; // Enable TLS encryption
            $mail->Port = 587; // TCP port to connect to

            // Recipients
            $mail->setFrom('no-reply@yourwebsite.com', 'Your Website');
            $mail->addAddress($email); // Add a recipient

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();
            echo "A password reset link has been sent to your email.";
        } catch (Exception $e) {
            echo "Failed to send the reset link. Error: " . $mail->ErrorInfo;
        }
    } else {
        echo "Email not found.";
    }
}
?>