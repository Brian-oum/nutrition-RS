<?php
header("Content-Type: application/json");
require_once '../config/db.php';

// Get child progress endpoint
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_child_progress') {
    if (!isset($_GET['child_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Child ID is required']);
        exit;
    }

    $childId = (int)$_GET['child_id'];
    $conn = getDBConnection();

    // Get child information
    $childInfo = [];
    $stmt = $conn->prepare("SELECT id, name, birth_date, gender FROM children WHERE id = ?");
    $stmt->bind_param("i", $childId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $childInfo = $result->fetch_assoc();
    }
    $stmt->close();

    if (empty($childInfo)) {
        http_response_code(404);
        echo json_encode(['error' => 'Child not found']);
        exit;
    }

    // Get latest metrics
    $currentMetrics = [];
    $stmt = $conn->prepare("SELECT weight, height, measurement_date FROM child_metrics WHERE child_id = ? ORDER BY measurement_date DESC LIMIT 1");
    $stmt->bind_param("i", $childId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $currentMetrics = $result->fetch_assoc();
    }
    $stmt->close();

    // Get previous metrics for comparison
    $previousMetrics = [];
    $stmt = $conn->prepare("SELECT weight, height FROM child_metrics WHERE child_id = ? ORDER BY measurement_date DESC LIMIT 1, 1");
    $stmt->bind_param("i", $childId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $previousMetrics = $result->fetch_assoc();
    }
    $stmt->close();


    // Calculate age in months
    $birthDate = new DateTime($childInfo['birth_date']);
    $today = new DateTime();
    $ageInterval = $birthDate->diff($today);
    $ageInMonths = $ageInterval->y * 12 + $ageInterval->m;

    // Calculate BMI if we have current metrics
    $bmi = null;
    if (!empty($currentMetrics)) {
        $heightInMeters = $currentMetrics['height'] / 100;
        $bmi = $currentMetrics['weight'] / ($heightInMeters * $heightInMeters);
    }

    // Prepare response
    $response = [
        'childInfo' => $childInfo,
        'ageInMonths' => $ageInMonths,
        'currentMetrics' => $currentMetrics,
        'previousMetrics' => $previousMetrics,
        'bmi' => $bmi ? round($bmi, 1) : null
    ];

    echo json_encode($response);
    $conn->close();
    exit;
}


// Invalid endpoint
http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
?>