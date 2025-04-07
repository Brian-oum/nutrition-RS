<?php
require 'mpesa_configuration.php';

function getMpesaAccessToken() {
    $url = MPESA_ENV == 'sandbox' ? 
        'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials' : 
        'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    $credentials = base64_encode(MPESA_CONSUMER_KEY . ':' . MPESA_CONSUMER_SECRET);

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $result = json_decode($response);
    return $result->access_token;
}
?>
