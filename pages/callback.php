<?php
include '../config/db.php';

$callbackData = file_get_contents('php://input');
$data = json_decode($callbackData, true);

file_put_contents('callback_log.txt', print_r($data, true), FILE_APPEND);

if (isset($data['Body']['stkCallback'])) {
    $stk = $data['Body']['stkCallback'];
    $resultCode = $stk['ResultCode'];
    $resultDesc = $stk['ResultDesc'];
    $status = ($resultCode == 0) ? 'Success' : 'Failed';

    $amount = null;
    $phone = null;
    $receipt = null;

    if (isset($stk['CallbackMetadata']['Item'])) {
        foreach ($stk['CallbackMetadata']['Item'] as $item) {
            if ($item['Name'] === 'Amount') $amount = $item['Value'];
            if ($item['Name'] === 'MpesaReceiptNumber') $receipt = $item['Value'];
            if ($item['Name'] === 'PhoneNumber') $phone = $item['Value'];
        }
    }

    // Format phone number if needed (2547XXXXXXXX -> 07XXXXXXXX)
    if ($phone && substr($phone, 0, 4) === '2547') {
        $phone = '0' . substr($phone, 4);
    }

    // Update record based on phone number and 'Pending' status
    $update = "UPDATE payments SET status = ?, status_message = ?, payment_amount = ?, transaction_id = IFNULL(?, transaction_id)
               WHERE phone_number = ? AND status = 'Pending' ORDER BY id DESC LIMIT 1";

    $stmt = $conn->prepare($update);
    $stmt->bind_param("ssiss", $status, $resultDesc, $amount, $receipt, $phone);
    $stmt->execute();
    $stmt->close();
}
?>
