<?php
include '../config/db.php';

if (isset($_GET['query'])) {
    $search = $conn->real_escape_string($_GET['query']);
    
    $sql = "SELECT id, child_name FROM children WHERE child_name LIKE '%$search%' LIMIT 10";
    $result = $conn->query($sql);

    $children = [];
    while ($row = $result->fetch_assoc()) {
        $children[] = $row;
    }

    echo json_encode($children);
}
?>
