<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../index.php");
    exit();
}

include '../includes/db_connect.php';

$message = ""; // To hold success or error messages

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $phone_number = $_POST['phone_number'];
    $amount = (int)$_POST['amount'];
    $days = (int)$_POST['days'];
    $plan = $_POST['plan'];

    $transaction_id = strtoupper("TXN" . uniqid());
    $payment_date = date("Y-m-d H:i:s");
    $expiry_date = date("Y-m-d H:i:s", strtotime("+$days days"));

    $sql = "INSERT INTO payments (username, phone_number, amount, transaction_id, payment_date, expiry_date)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ssisss", $username, $phone_number, $amount, $transaction_id, $payment_date, $expiry_date);
        if ($stmt->execute()) {
            $message = "<div class='success'>Payment successful for KES $amount. Access valid until $expiry_date.</div>";
        } else {
            $message = "<div class='error'>Failed to complete payment. Please try again.</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='error'>Server error. Please try later.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Process Payment</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .success {
      background: #d4edda;
      padding: 1rem;
      border-left: 5px solid #28a745;
      margin: 1rem;
      color: #155724;
    }
    .error {
      background: #f8d7da;
      padding: 1rem;
      border-left: 5px solid #dc3545;
      margin: 1rem;
      color: #721c24;
    }
    .main-content {
      padding: 2rem;
    }
  </style>
</head>
<body>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
  <h2>Payment Status</h2>
  <?= $message ?>
  <a href="make_payment.php" style="display:inline-block;margin-top:1rem;">Back to Subscriptions</a>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>
