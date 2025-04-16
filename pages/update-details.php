<?php
include '../config/db.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $weight = trim($_POST['weight']);
    $height = trim($_POST['height']);
    $schedule = trim($_POST['schedule']);
    $diet = trim($_POST['diet']);

    if (empty($name) || empty($weight) || empty($height) || empty($schedule)) {
        echo "<script>alert('❌ All fields are required.'); window.location='dashboard.php';</script>";
        exit();
    }

    $schedule_order = [
        "2-Weeks", "6-Weeks", "10-Weeks", "14-Weeks", "1-Year", "1.25-Years",
        "1.5-Years", "2-Years", "2.5-Years", "3-Years", "3.5-Years", 
        "4-Years", "4.5-Years", "5-Years"
    ];

    $sql = "SELECT id FROM children WHERE child_name = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL Error (Fetching Child ID): " . $conn->error);
    }

    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $child = $result->fetch_assoc();
    $stmt->close();

    if ($child) {
        $child_id = $child['id'];

        $sql = "SELECT schedule FROM child_progress WHERE child_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("SQL Error (Fetching Child Progress): " . $conn->error);
        }

        $stmt->bind_param("i", $child_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $existing_schedules = [];
        while ($row = $result->fetch_assoc()) {
            $existing_schedules[] = $row['schedule'];
        }
        $stmt->close();

        if (in_array($schedule, $existing_schedules)) {
            echo "<script>alert('❌ This schedule has already been recorded for this child. Please choose a different one.'); window.location='dashboard.php';</script>";
            exit();
        }

        $last_completed_index = -1;
        foreach ($schedule_order as $index => $sched) {
            if (in_array($sched, $existing_schedules)) {
                $last_completed_index = $index;
            }
        }

        $current_index = array_search($schedule, $schedule_order);
        if ($current_index === false || $current_index !== $last_completed_index + 1) {
            echo "<script>alert('❌ Please complete the previous schedule before adding this one.'); window.location='dashboard.php';</script>";
            exit();
        }

        $sql = "INSERT INTO child_progress (child_id, weight, height, schedule, dietary_restrictions) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("SQL Error (Inserting Progress): " . $conn->error);
        }

        $stmt->bind_param("iddss", $child_id, $weight, $height, $schedule, $diet);

        if ($stmt->execute()) {
            echo "<script>alert('✅ Child details updated successfully.'); window.location='dashboard.php';</script>";
        } else {
            echo "<script>alert('❌ Failed to update child details.'); window.location='dashboard.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('❌ Child not found. Please enter a valid name.'); window.location='dashboard.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Child Details</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="dash-container">
        <?php include '../includes/sidebar.php'; ?>

        <main class="content">
            <h2>Update Child Details</h2>

            <!-- Search Bar -->
            
            <!-- Update Form -->
            <form id="update-form" action="update-details.php" method="POST">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="search-child" placeholder="Search child by name..." autocomplete="off">
                    <div id="search-results" class="search-dropdown"></div>
                </div>

                <div class="form-group">
                    <label for="name"><i class="fas fa-user"></i> Child's Name:</label>
                    <input type="text" id="name" name="name" readonly>
                </div>

                <div class="form-group">
                    <label for="weight"><i class="fas fa-weight"></i> Weight (kg):</label>
                    <input type="number" id="weight" name="weight">
                </div>

                <div class="form-group">
                    <label for="height"><i class="fas fa-ruler-vertical"></i> Height (cm):</label>
                    <input type="number" id="height" name="height">
                </div>

                <div class="form-group">
                    <label for="schedule"><i class="fas fa-clock"></i> Time Schedule:</label>
                    <select name="schedule" id="schedule" required>
                        <option value="">=== Select Time Schedule ===</option>
                        <option value="2-Weeks">2 Weeks</option>
                        <option value="6-Weeks">6 Weeks</option>
                        <option value="10-Weeks">10 Weeks</option>
                        <option value="14-Weeks">14 Weeks</option>
                        <option value="1-Year">1 Year</option>
                        <option value="1.25-Years">1 Year 3 Months</option>
                        <option value="1.5-Years">1 Year 6 Months</option>
                        <option value="2-Years">2 Years</option>
                        <option value="2.5-Years">2 Years 6 Months</option>
                        <option value="3-Years">3 Years</option>
                        <option value="3.5-Years">3 Years 6 Months</option>
                        <option value="4-Years">4 Years</option>
                        <option value="4.5-Years">4 Years 6 Months</option>
                        <option value="5-Years">5 Years</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="diet"><i class="fas fa-utensils"></i> Dietary Restrictions:</label>
                    <textarea id="diet" name="diet" placeholder="Enter any dietary restrictions"></textarea>
                </div>

                <button type="submit"><i class="fas fa-paper-plane"></i> Update Details</button>
            </form>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/script.js"></script>
</body>
</html>