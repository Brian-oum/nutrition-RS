<?php
session_start();
date_default_timezone_set('Africa/Nairobi');

if (!isset($_SESSION["username"])) {
    header("Location: ../index.php");
    exit();
}

include '../config/db.php';

$username = $_SESSION["username"];
$now = new DateTime();

$sql = "SELECT amount, transaction_id, payment_date, expiry_date 
        FROM payments 
        WHERE username = ? 
        ORDER BY payment_date DESC LIMIT  3";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

function formatTimeRemaining($expiryDate) {
    $now = new DateTime();
    $expiry = new DateTime($expiryDate);
    if ($expiry < $now) return "Expired";

    $interval = $now->diff($expiry);
    return $interval->format('%a days %h hours %i mins %s secs');
}

function getSubscriptionName($amount) {
    return match ((int)$amount) {
        50 => "Daily Access",
        120 => "3-Day Access",
        330 => "Weekly Access",
        1000 => "Monthly Access",
        2700 => "3-Month Access",
        5000 => "6-Month Access",
        9000 => "Yearly Access",
        default => "Unknown Plan",
    };
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Subscriptions</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include '../includes/header.php'; ?>
<div class="dash-container">
<?php include '../includes/sidebar.php'; ?>

<main class="content">
    <h2>My Subscriptions</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): 
            $subscriptionName = getSubscriptionName($row['amount']);
            $paymentDate = new DateTime($row['payment_date']);
            $expiryDate = new DateTime($row['expiry_date']);
            $isExpired = $expiryDate < $now;
            $statusClass = $isExpired ? "expired" : "active";
            $statusText = $isExpired ? "Expired" : "Active";
            $timeRemaining = $isExpired ? "Expired" : formatTimeRemaining($row['expiry_date']);
        ?>
        <div class="sub-card">
            <div class="sub-icon"><i class="fas fa-paperclip"></i></div>
            <div>
                <h3><?= $subscriptionName ?></h3>
                <p><strong>Status:</strong> <span class="status <?= $statusClass ?>"><?= $statusText ?></span></p>
                <p><strong>Time Remaining:</strong> <?= $timeRemaining ?></p>
                <p><strong>Date:</strong> <?= $paymentDate->format("jS M Y H:i") ?></p>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No subscriptions found.</p>
    <?php endif; ?>

    </main>
</div>
<?php include '../includes/footer.php'; ?>
<script src="../assets/js/script.js"></script>

</body>
</html>
