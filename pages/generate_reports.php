<?php
session_start();
include '../config/db.php';

// Check login
if (!isset($_SESSION["username"])) {
    echo "<script>alert('Please log in first.'); window.location.href='login.php';</script>";
    exit();
}

// Check subscription status
$username = $_SESSION["username"];
$now = date("Y-m-d H:i:s");

$sub_query = "SELECT * FROM payments WHERE username = '$username' AND expiry_date >= '$now' ORDER BY expiry_date DESC LIMIT 1";
$sub_result = mysqli_query($conn, $sub_query);

if (!$sub_result || mysqli_num_rows($sub_result) === 0) {
    echo "<script>alert('You must have an active subscription to access reports.'); window.location.href='make_payment.php';</script>";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Generate Reports</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="../assets/css/style.css"/>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="dash-container">
    <?php include '../includes/sidebar.php'; ?>
    <main class="content">
        <h2>Generate Reports</h2>

        <div id="printable-section">

            <!-- CAREGIVER REPORT -->
            <div class="report-section">
                <h3>Caregiver Report</h3>
                <?php
                $caregiver_query = "SELECT username, email, created_at FROM caregiver ORDER BY created_at DESC";
                $caregiver_result = mysqli_query($conn, $caregiver_query);

                if ($caregiver_result && mysqli_num_rows($caregiver_result) > 0) {
                    echo "<table>
                        <tr><th>Username</th><th>Email</th><th>Date Created</th></tr>";
                    while ($row = mysqli_fetch_assoc($caregiver_result)) {
                        echo "<tr>
                            <td>" . htmlspecialchars($row['username']) . "</td>
                            <td>" . htmlspecialchars($row['email']) . "</td>
                            <td>" . htmlspecialchars($row['created_at']) . "</td>
                        </tr>";
                    }
                    echo "</table>";
                    echo "<p><strong>Total Caregivers:</strong> " . mysqli_num_rows($caregiver_result) . "</p>";
                } else {
                    echo "<p>No caregiver records found.</p>";
                }
                ?>
            </div>

            <!-- CHILDREN REPORT -->
            <div class="report-section">
                <h3>Children Report</h3>
                <?php
                $children_query = "SELECT child_name, dob, gender, created_at FROM children ORDER BY created_at DESC";
                $children_result = mysqli_query($conn, $children_query);

                if ($children_result && mysqli_num_rows($children_result) > 0) {
                    echo "<table>
                        <tr><th>Name</th><th>Date of Birth</th><th>Gender</th><th>Date Created</th></tr>";
                    while ($row = mysqli_fetch_assoc($children_result)) {
                        echo "<tr>
                            <td>" . htmlspecialchars($row['child_name']) . "</td>
                            <td>" . htmlspecialchars($row['dob']) . "</td>
                            <td>" . htmlspecialchars($row['gender']) . "</td>
                            <td>" . htmlspecialchars($row['created_at']) . "</td>
                        </tr>";
                    }
                    echo "</table>";
                    echo "<p><strong>Total Children:</strong> " . mysqli_num_rows($children_result) . "</p>";
                } else {
                    echo "<p>No child records found.</p>";
                }
                ?>
            </div>

            <!-- PAYMENTS REPORT -->
            <div class="report-section">
                <h3>Payments Report</h3>
                <?php
                $payments_query = "SELECT username, phone_number, amount, payment_date FROM payments ORDER BY payment_date DESC";
                $payments_result = mysqli_query($conn, $payments_query);

                if ($payments_result && mysqli_num_rows($payments_result) > 0) {
                    $total_amount = 0;
                    echo "<table>
                        <tr><th>Username</th><th>Phone Number</th><th>Amount (KES)</th><th>Payment Date</th></tr>";
                    while ($row = mysqli_fetch_assoc($payments_result)) {
                        $total_amount += $row['amount'];
                        echo "<tr>
                            <td>" . htmlspecialchars($row['username']) . "</td>
                            <td>" . htmlspecialchars($row['phone_number']) . "</td>
                            <td>" . number_format($row['amount']) . "</td>
                            <td>" . htmlspecialchars($row['payment_date']) . "</td>
                        </tr>";
                    }
                    echo "</table>";
                    echo "<p><strong>Total Payments:</strong> KES " . number_format($total_amount) . "</p>";
                } else {
                    echo "<p>No payment records found.</p>";
                }
                ?>
            </div>

        </div>
        <button class="print-btn" onclick="window.print()"><i class="fas fa-print"></i> Print Report</button>
        
    </main>
</div>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/script.js"></script>

</body>
</html>
