<?php
require '../config/db.php';
session_start();

function hasActiveSubscription($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM subscriptions WHERE user_id = ? AND expiry_date > NOW() ORDER BY expiry_date DESC LIMIT 1");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}
