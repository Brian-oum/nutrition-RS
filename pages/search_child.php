<?php
include '../config/db.php';

if (isset($_GET['query']) && !empty($_GET['query'])) {
    $search = $_GET['query'] . "%";

    $sql = "SELECT id, child_name 
            FROM children 
            WHERE child_name LIKE ? 
            ORDER BY child_name ASC 
            LIMIT 10";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search);
    $stmt->execute();

    $result = $stmt->get_result();
    $children = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($children);
    $stmt->close();
} else {
    echo json_encode([]); // Return empty array if no search query
}
?>
