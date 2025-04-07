<?php
session_start();
require 'access_token.php';
require 'mpesa_configuration.php';

// Get phone from form input
$phone = $_POST['phone'] ?? null;

// Get user ID from session safely
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$phone || !$user_id) {
    echo "Missing phone number or user not logged in.";
    exit;
}

$timestamp = date('YmdHis');
$password = base64_encode(MPESA_SHORTCODE . MPESA_PASSKEY . $timestamp);
$access_token = getMpesaAccessToken();

if (!$access_token) {
    echo "Failed to get M-Pesa access token.";
    exit;
}

$payload = [
    "BusinessShortCode" => MPESA_SHORTCODE,
    "Password" => $password,
    "Timestamp" => $timestamp,
    "TransactionType" => "CustomerPayBillOnline",
    "Amount" => 10,
    "PartyA" => $phone,
    "PartyB" => MPESA_SHORTCODE,
    "PhoneNumber" => $phone,
    "CallBackURL" => CALLBACK_URL,
    "AccountReference" => "NutritionPlan",
    "TransactionDesc" => "Monthly Subscription"
];

$url = MPESA_ENV == 'sandbox' ? 
    'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest' : 
    'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access_token
]);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
$res = json_decode($response);

// Optional: Log response
// file_put_contents("stk_response.json", json_encode($res, JSON_PRETTY_PRINT));

echo "<script>alert('✅ Payment request sent. Check your phone.'); window.location='dashboard.php';</script>";
?>
