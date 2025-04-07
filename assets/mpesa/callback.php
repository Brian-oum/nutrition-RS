<?php
require_once './config_mpesa.php';

// Get the callback data
$callbackJSON = file_get_contents('php://input');
$callbackData = json_decode($callbackJSON, true);

// Log the callback for debugging
file_put_contents('mpesa_callback.log', $callbackJSON.PHP_EOL, FILE_APPEND);

// Check if this is a valid callback
if (isset($callbackData['Body']['stkCallback'])) {
    $resultCode = $callbackData['Body']['stkCallback']['ResultCode'];
    $merchantRequestID = $callbackData['Body']['stkCallback']['MerchantRequestID'];
    $checkoutRequestID = $callbackData['Body']['stkCallback']['CheckoutRequestID'];
    
    global $db;
    
    if ($resultCode == 0) {
        // Successful payment
        $items = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'];
        
        $paymentData = [];
        foreach ($items as $item) {
            $paymentData[$item['Name']] = $item['Value'] ?? null;
        }
        
        // Update transaction in database
        $stmt = $db->prepare("UPDATE mpesa_transactions 
                             SET mpesa_receipt_number = ?, 
                                 transaction_date = ?,
                                 status = 'completed'
                             WHERE checkout_request_id = ?");
        $stmt->execute([
            $paymentData['MpesaReceiptNumber'],
            date('Y-m-d H:i:s', strtotime($paymentData['TransactionDate'])),
            $checkoutRequestID
        ]);
        
        // Here you can trigger any post-payment actions
        // e.g., send email, update order status, etc.
        
    } else {
        // Failed payment
        $stmt = $db->prepare("UPDATE mpesa_transactions 
                             SET status = 'failed'
                             WHERE checkout_request_id = ?");
        $stmt->execute([$checkoutRequestID]);
    }
}

// Send response to M-Pesa
header('Content-Type: application/json');
echo json_encode([
    'ResultCode' => 0,
    'ResultDesc' => 'Success'
]);
?>