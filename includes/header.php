<?php
if (!isset($_SESSION)) session_start();

date_default_timezone_set("Africa/Nairobi");
$hour = date("H");
$current_time = date("H:i");

$greeting = match (true) {
    $hour >= 5 && $hour < 12 => "Good Morning",
    $hour >= 12 && $hour < 17 => "Good Afternoon",
    $hour >= 17 && $hour < 23 => "Good Evening",
    default => "Good Night",
};

$username = $_SESSION["username"] ?? "Guest";
?>

<div class="dashboard-header">
  <button id="toggle-btn"><i class="fas fa-bars"></i></button>
  <h2 class="header-center"><?= $greeting . " , " . htmlspecialchars($username) . "!"; ?></h2>
  <div class="header-right">
    <i class="fas fa-question-circle" title="How to use the system"></i>
    <span><?= $current_time; ?> HRS</span>
  </div>
</div>
