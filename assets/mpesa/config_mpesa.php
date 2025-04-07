<?php
// M-Pesa API Credentials
define('MPESA_CONSUMER_KEY', 'mJ7mrhrGh5hPzhjMIyjfBGmC4tADup903qM14dv365nM4vYq');
define('MPESA_CONSUMER_SECRET', '7yeZVqCCJa0EFJzlgcFFmvCyRIPHp3FjFHV5CGudz2q1mn3AKUaVTryLckI59dd6');
define('MPESA_SHORTCODE', '174379'); // Paybill or Buygoods number
define('MPESA_PASSKEY', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919');

// Callback URL (Must be HTTPS)
define('MPESA_CALLBACK_URL', 'https://yourdomain.com/mpesa/callback.php');

// Environment (sandbox or production)
define('MPESA_ENV', 'sandbox'); // Change to 'production' when live

// API Endpoints
if (MPESA_ENV == 'sandbox') {
    define('MPESA_AUTH_URL', 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
    define('MPESA_STK_PUSH_URL', 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
} else {
    define('MPESA_AUTH_URL', 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
    define('MPESA_STK_PUSH_URL', 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
}

// Database connection
$db = new PDO('mysql:host=localhost;dbname=nutririon_system', 'db_username= root', 'db_password = ""');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>