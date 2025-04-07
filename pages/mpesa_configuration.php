<?php
// Check if constants are already defined before declaring them
if (!defined('MPESA_CONSUMER_KEY')) {
    define('MPESA_CONSUMER_KEY', 'mJ7mrhrGh5hPzhjMIyjfBGmC4tADup903qM14dv365nM4vYq');
    define('MPESA_CONSUMER_SECRET', '7yeZVqCCJa0EFJzlgcFFmvCyRIPHp3FjFHV5CGudz2q1mn3AKUaVTryLckI59dd6');
    define('MPESA_SHORTCODE', '174379');
    define('MPESA_PASSKEY', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919');
    define('MPESA_ENV', 'sandbox'); // or 'production'
    define('CALLBACK_URL', 'https://yourdomain.com/callback.php');
}
?>