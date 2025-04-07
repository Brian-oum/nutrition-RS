<?php
require_once './functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'];
    $amount = $_POST['amount'];
    $accountReference = $_POST['reference']; // e.g., order ID or user ID
    $description = "Payment for order #".$accountReference;
    
    // Validate inputs
    if (empty($phone) || empty($amount) || !is_numeric($amount)) {
        die("Invalid input parameters");
    }
    
    // Initiate payment
    $response = initiateSTKPush($phone, $amount, $accountReference, $description);
    
    if ($response) {
        // Payment initiated successfully
        echo json_encode([
            'success' => true,
            'message' => 'Payment request sent to your phone. Please complete the payment.',
            'checkout_request_id' => $response['CheckoutRequestID']
        ]);
    } else {
        // Failed to initiate payment
        echo json_encode([
            'success' => false,
            'message' => 'Failed to initiate payment. Please try again.'
        ]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Make Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Pay via M-Pesa</h4>
                    </div>
                    <div class="card-body">
                        <form id="paymentForm">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number (2547...)</label>
                                <input type="text" class="form-control" id="phone" required>
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount (KES)</label>
                                <input type="number" class="form-control" id="amount" required>
                            </div>
                            <input type="hidden" id="reference" value="<?= uniqid() ?>">
                            <button type="submit" class="btn btn-primary">Pay Now</button>
                        </form>
                        <div id="paymentStatus" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const phone = document.getElementById('phone').value;
            const amount = document.getElementById('amount').value;
            const reference = document.getElementById('reference').value;
            const statusDiv = document.getElementById('paymentStatus');
            
            statusDiv.innerHTML = '<div class="alert alert-info">Processing...</div>';
            
            fetch('payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `phone=${encodeURIComponent(phone)}&amount=${encodeURIComponent(amount)}&reference=${encodeURIComponent(reference)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusDiv.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                    // Poll for payment completion
                    checkPaymentStatus(data.checkout_request_id);
                } else {
                    statusDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                }
            })
            .catch(error => {
                statusDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
                console.error('Error:', error);
            });
        });
        
        function checkPaymentStatus(checkoutRequestID) {
            const statusDiv = document.getElementById('paymentStatus');
            
            const poll = setInterval(() => {
                fetch('check_payment.php?checkout_request_id=' + checkoutRequestID)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'completed') {
                        statusDiv.innerHTML = '<div class="alert alert-success">Payment received! Thank you.</div>';
                        clearInterval(poll);
                        // Redirect or update UI as needed
                    } else if (data.status === 'failed') {
                        statusDiv.innerHTML = '<div class="alert alert-danger">Payment failed. Please try again.</div>';
                        clearInterval(poll);
                    }
                    // Continue polling if still pending
                });
            }, 3000); // Check every 3 seconds
        }
    </script>
</body>
</html>