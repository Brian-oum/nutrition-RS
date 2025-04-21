<?php
session_start();
date_default_timezone_set('Africa/Nairobi');

if (!isset($_SESSION["username"])) {
    header("Location: ../index.php");
    exit();
}

include '../config/db.php';
include '../config/mpesa_config.php';

// Get M-Pesa Access Token
function getAccessToken() {
    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $credentials = base64_encode(MPESA_CONSUMER_KEY . ':' . MPESA_CONSUMER_SECRET);
    $headers = ['Authorization: Basic ' . $credentials];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response);
    return $result->access_token ?? null;
}

// Send STK Push
function sendSTKPush($phone, $amount, $transactionId) {
    $accessToken = getAccessToken();
    if (!$accessToken) return ['error' => 'Unable to obtain access token.'];

    $timestamp = date('YmdHis');
    $password = base64_encode(MPESA_SHORTCODE . MPESA_PASSKEY . $timestamp);

    $accountNumber = "Nutrition-RS";
    $transactionDesc = "Pay KES $amount to Nutrition-RS account $accountNumber";

    $data = [
        'BusinessShortCode' => MPESA_SHORTCODE,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone,
        'PartyB' => MPESA_SHORTCODE,
        'PhoneNumber' => $phone,
        'CallBackURL' => MPESA_CALLBACK_URL,
        'AccountReference' => $accountNumber,
        'TransactionDesc' => $transactionDesc
    ];

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ];

    $ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) return ['error' => $err];
    return json_decode($response, true);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $phone_number = preg_replace('/^0/', '254', $_POST['phone_number']);
    $amount = (int)$_POST['amount'];
    $days = (int)$_POST['days'];
    $plan = $_POST['plan'];

    $transaction_id = strtoupper("TXN" . uniqid());
    $payment_date = date("Y-m-d H:i:s");
    $expiry_date = date("Y-m-d H:i:s", strtotime("+$days days"));

    $stkResponse = sendSTKPush($phone_number, $amount, $transaction_id);

    if (isset($stkResponse['error'])) {
        echo "<script>alert('❌ Failed to initiate payment: {$stkResponse['error']}'); window.location='make_payment.php';</script>";
        exit();
    }

    if ($stkResponse['ResponseCode'] === "0") {
        $sql = "INSERT INTO payments (username, phone_number, amount, transaction_id, payment_date, expiry_date, status)
                VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssisss", $username, $phone_number, $amount, $transaction_id, $payment_date, $expiry_date);
            $stmt->execute();
            $stmt->close();
        }

        echo "<script>alert('✅ Payment request sent. Check your phone.'); window.location='make_payment.php';</script>";
    } else {
        echo "<script>alert('❌ STK Push failed: {$stkResponse['ResponseDescription']}'); window.location='make_payment.php';</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Process Payment</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
  <h2>Processing Payment...</h2>
  <p>If you are not redirected, <a href="make_payment.php">click here</a>.</p>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>
