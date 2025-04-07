<?php
require_once __DIR__.'/config_mpesa.php';

if (!isset($_GET['checkout_request_id'])) {
    die("Invalid request");
}

$checkoutRequestID = $_GET['checkout_request_id'];

global $db;
$stmt = $db->prepare("SELECT status FROM mpesa_transactions 
                     WHERE checkout_request_id = ?");
$stmt->execute([$checkoutRequestID]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode([
    'status' => $transaction['status'] ?? 'unknown'
]);
?>