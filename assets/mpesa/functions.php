<?php
require_once './config_mpesa.php';

/**
 * Generate access token
 */
function getAccessToken() {
    $credentials = base64_encode(MPESA_CONSUMER_KEY.':'.MPESA_CONSUMER_SECRET);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, MPESA_AUTH_URL);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response);
    return $result->access_token ?? null;
}

/**
 * Initiate STK Push
 */
function initiateSTKPush($phone, $amount, $accountReference, $description) {
    global $db;
    
    $accessToken = getAccessToken();
    if (!$accessToken) return false;
    
    $timestamp = date('YmdHis');
    $password = base64_encode(MPESA_SHORTCODE.MPESA_PASSKEY.$timestamp);
    
    $phone = preg_replace('/^0/', '254', $phone); // Format to 254...
    $phone = preg_replace('/^\+/', '', $phone); // Remove + if present
    
    $headers = [
        'Authorization: Bearer '.$accessToken,
        'Content-Type: application/json'
    ];
    
    $payload = [
        'BusinessShortCode' => MPESA_SHORTCODE,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone,
        'PartyB' => MPESA_SHORTCODE,
        'PhoneNumber' => $phone,
        'CallBackURL' => MPESA_CALLBACK_URL,
        'AccountReference' => $accountReference,
        'TransactionDesc' => $description
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, MPESA_STK_PUSH_URL);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if (isset($result['ResponseCode']) && $result['ResponseCode'] == '0') {
        // Save transaction to database
        $stmt = $db->prepare("INSERT INTO mpesa_transactions 
                             (merchant_request_id, checkout_request_id, phone_number, amount, status) 
                             VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([
            $result['MerchantRequestID'],
            $result['CheckoutRequestID'],
            $phone,
            $amount
        ]);
        
        return $result;
    }
    
    return false;
}

/**
 * Verify transaction status
 */
function verifyTransaction($checkoutRequestID) {
    global $db;
    
    $stmt = $db->prepare("SELECT * FROM mpesa_transactions 
                         WHERE checkout_request_id = ? AND status = 'completed'");
    $stmt->execute([$checkoutRequestID]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>