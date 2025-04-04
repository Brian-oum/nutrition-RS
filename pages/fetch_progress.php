<?php
include '../config/db.php';

if (isset($_GET['child_id'])) {
    $child_id = $_GET['child_id'];

    // Fetch child's name
    $sql = "SELECT child_name FROM children WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $child_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $child = $result->fetch_assoc();
    $stmt->close();

    if (!$child) {
        echo json_encode(["error" => "Child not found"]);
        exit();
    }

    // Fetch progress data, ordered by schedule (chronologically)
    $schedule_order = [
        '2-Weeks', '6-Weeks', '10-Weeks', '14-Weeks', '1-Year', '1.25-Years', '1.5-Years',
        '2-Years', '2.5-Years', '3-Years', '3.5-Years', '4-Years', '4.5-Years', '5-Years'
    ];

    // Create a string for the `FIELD()` function to sort schedules in the correct order
    $order_str = "'" . implode("','", $schedule_order) . "'";

    $sql = "SELECT weight, schedule FROM child_progress WHERE child_id = ? 
            ORDER BY FIELD(schedule, $order_str)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $child_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $progress = [];
    while ($row = $result->fetch_assoc()) {
        $progress[] = [
            "weight" => (float)$row["weight"], // Y-axis (Weight)
            "schedule" => $row["schedule"] // X-axis (Schedule)
        ];
    }

    if (empty($progress)) {
        echo json_encode(["error" => "No progress data found for this child"]);
    } else {
        echo json_encode(["child_name" => $child["child_name"], "progress" => $progress]);
    }
} else {
    echo json_encode(["error" => "No child selected"]);
}
?>
