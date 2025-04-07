<?php
require_once '../config/db.php';

session_start();

// Read the raw POST data from M-Pesa
$callbackJSON = file_get_contents('php://input');

// Log it for debugging (optional)
file_put_contents('mpesa_callback_log.json', $callbackJSON);

$data = json_decode($callbackJSON, true);

// Safely extract necessary info
$callback = $data['Body']['stkCallback'] ?? null;

if (!$callback || $callback['ResultCode'] != 0) {
    // Payment failed or cancelled
    http_response_code(200); // Still return OK to Safaricom
    exit;
}

// Extract metadata
$metadata = $callback['CallbackMetadata']['Item'];
$amount = null;
$phone = null;

foreach ($metadata as $item) {
    if ($item['Name'] === 'Amount') {
        $amount = $item['Value'];
    }
    if ($item['Name'] === 'PhoneNumber') {
        $phone = $item['Value'];
    }
}

// Optional: You can identify user using session, phone, or pass a reference during STK push
$user_id = $_SESSION['user_id'] ?? null;

// If you didn't pass session data, look up user by phone
if (!$user_id && $phone) {
    $stmt = $pdo->prepare("SELECT id FROM caregiver WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();
    if ($user) {
        $user_id = $user['id'];
    }
}

// Only proceed if valid
if ($amount && $phone && $user_id) {
    $payment_date = date('Y-m-d H:i:s');
    $expiry_date = date('Y-m-d H:i:s', strtotime('+30 days'));

    // Insert subscription
    $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, phone, amount, payment_date, expiry_date)
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $phone, $amount, $payment_date, $expiry_date]);
}

http_response_code(200); // Always return 200 to Safaricom
echo json_encode(['Result' => 'Received successfully']);