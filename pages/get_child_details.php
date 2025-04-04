<?php
include '../config/db.php';

if (isset($_GET['child_id'])) {
    $child_id = $_GET['child_id'];

    $sql = "SELECT schedule, weight FROM child_progress WHERE child_id = ? ORDER BY schedule ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $child_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $progress = [];
    while ($row = $result->fetch_assoc()) {
        $progress[] = $row;
    }

    echo json_encode($progress);
    $stmt->close();
} else {
    echo json_encode([]);
}
?>
