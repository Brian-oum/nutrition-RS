<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../index.php");
    exit();
}
$username = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Choose a Subscription</title>
  <link rel="stylesheet" href="../assets/css/style.css"/>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="dash-container">
  <?php include '../includes/sidebar.php'; ?>

  <main class="content">
    <h2 class="page-title">Select a Subscription Plan</h2>

    <div class="cards-container">
      <?php
      $plans = [
        ["Daily Access", "daily", 50, 1, "Daily access to all features. Perfect for short-term users."],
        ["3-Day Access", "3days", 120, 3, "Access for 3 days. Great for testing out the service."],
        ["Weekly Access", "weekly", 330, 7, "Access for a week with all premium features."],
        ["Monthly Access", "monthly", 1000, 30, "Full month of unlimited access to everything."],
        ["3-Month Access", "3months", 2700, 90, "Quarterly access. Save with longer durations."],
        ["6-Month Access", "6months", 5000, 180, "Half-year subscription. Get the best value."],
        ["Yearly Access", "yearly", 9000, 365, "Annual plan. Ideal for long-term access with discounts."],
      ];

      foreach ($plans as $plan) {
        list($title, $planName, $amount, $days, $description) = $plan;
        echo "
        <div class='card'>
          <h3>$title</h3>
          <p>Access for $days day" . ($days > 1 ? "s" : "") . "</p>
          <p><strong>KES $amount</strong></p>
          <p class='plan-description'>$description</p>
          <button class='btn' onclick=\"openModal('$planName', $amount, $days, '$description')\">Buy Now</button>
        </div>";
      }
      ?>
    </div>
  </main>
</div>

<!-- Modal -->
<div class="modal" id="paymentModal">
  <div class="modal-content">
    <span class="close-modal" onclick="closeModal()">&times;</span>
    <h3>Enter Phone Number</h3>
    <div class="plan-info">
      <h4 id="modal-title"></h4>
      <p id="modal-description"></p>
      <p><strong>Price: <span id="modal-price"></span></strong></p>
      <p><strong>Duration: <span id="modal-duration"></span></strong></p>
    </div>
    <form action="process_payment.php" method="POST">
      <input type="hidden" name="plan" id="modal-plan">
      <input type="hidden" name="amount" id="modal-amount">
      <input type="hidden" name="days" id="modal-days">
      <input type="hidden" name="username" value="<?= htmlspecialchars($username); ?>">

      <label for="phone_number">Phone Number (Safaricom - e.g., 07XXXXXXXX):</label>
      <input type="text" name="phone_number" id="phone_number" pattern="^07\d{8}$" maxlength="10" placeholder="07XXXXXXXX" required>

      <br><br>
      <button type="submit" class="btn">Proceed to Pay</button>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
  function openModal(plan, amount, days, description) {
    document.getElementById('modal-plan').value = plan;
    document.getElementById('modal-amount').value = amount;
    document.getElementById('modal-days').value = days;
    document.getElementById('modal-title').textContent = plan.charAt(0).toUpperCase() + plan.slice(1) + " Access";
    document.getElementById('modal-description').textContent = description;
    document.getElementById('modal-price').textContent = "KES " + amount;
    document.getElementById('modal-duration').textContent = days + " day" + (days > 1 ? "s" : "");
    document.getElementById('paymentModal').style.display = 'flex';
  }

  function closeModal() {
    document.getElementById('paymentModal').style.display = 'none';
  }

  window.onclick = function(event) {
    const modal = document.getElementById("paymentModal");
    if (event.target === modal) {
      closeModal();
    }
  }
</script>

</body>
</html>