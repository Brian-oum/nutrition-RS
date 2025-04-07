<?php
session_start();
require 'subscription_check.php';

// Redirect if user is already subscribed
if (isset($_SESSION['user_id']) && hasActiveSubscription($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subscribe via M-Pesa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .subscription-box {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            color: #28a745;
        }
        input[type="text"], button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            background: #28a745;
            color: #fff;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
        .note {
            font-size: 0.9em;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="subscription-box">
    <h2>Subscribe for KES 650</h2>
    <form action="stk_push.php" method="POST">
        <label for="phone">Enter your M-Pesa number:</label>
        <input type="text" name="phone" id="phone" placeholder="e.g. 2547XXXXXXXX" required>
        <button type="submit">Pay Now</button>
    </form>
    <p class="note">You will receive an M-Pesa prompt on your phone to complete the payment.</p>
</div>

</body>
</html>
