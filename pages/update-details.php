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

    // Define the required sequence of schedules
    $schedule_order = [
        "2-Weeks", "6-Weeks", "10-Weeks", "14-Weeks", "1-Year", "1.25-Years",
        "1.5-Years", "2-Years", "2.5-Years", "3-Years", "3.5-Years", 
        "4-Years", "4.5-Years", "5-Years"
    ];

    // Get child ID from the name
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

        // Check if the same schedule already exists for the child
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

        // Check if the schedule is already recorded for the child
        if (in_array($schedule, $existing_schedules)) {
            echo "<script>alert('❌ This schedule has already been recorded for this child. Please choose a different one.'); window.location='dashboard.php';</script>";
            exit();
        }

        // Ensure schedules are followed sequentially
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

        // Insert new progress record
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
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<style>
    /* Search Box */
    .update-container {
    max-width: 700px;
    background: #fff;
    padding: 25px;
    margin: 40px auto;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
    transition: 0.3s ease-in-out;
}
.update-container .search-box {
    position: relative;
    display: flex;
    align-items: center;
    border: 1px solid #4CAF50;
    border-radius: 5px;
    padding: 10px;
    background: #fff;
    margin-bottom: 20px;
    transition: 0.3s;
    width: 100%;
}

.update-container .search-box input {
    width: 100%;
    border: none;
    outline: none;
    font-size: 16px;
    padding: 10px;
    border-radius: 5px;
    font-family: Arial, sans-serif;
}

.update-container .search-box i {
    color: #4CAF50;
    font-size: 20px;
    margin-right: 10px;
}

/* Search Dropdown */
.search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.search-result {
    padding: 10px;
    font-size: 16px;
    cursor: pointer;
    border-bottom: 1px solid #ddd;
    transition: background 0.3s ease-in-out;
}

.search-result:hover {
    background-color: #f1f1f1;
}

/* Form Styling */
.update-container label {
    font-weight: 600;
    display: block;
    margin-top: 12px;
    color: #333;
}

.update-container select,
.update-container input,
.update-container textarea {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
    transition: 0.3s;
    font-family: Arial, sans-serif;
}

.update-container select {
    background-color: #fff;
    cursor: pointer;
}

.update-container select:focus,
.update-container input:focus,
.update-container textarea:focus {
    border-color: #4CAF50;
    box-shadow: 0px 0px 5px rgba(76, 175, 80, 0.5);
    outline: none;
}

.update-container select option {
    font-size: 16px;
    padding: 10px;
    cursor: pointer;
}

.update-container textarea {
    resize: none;
    height: 80px;
}

/* Button Styling */
.update-container button {
    width: 100%;
    padding: 12px;
    margin-top: 20px;
    background: #4CAF50;
    color: #fff;
    border: none;
    border-radius: 50px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

.update-container button:hover {
    background: #2E7D32;
    transform: scale(1.05);
}

/* Back Button Styling */
.update-container .back-btn {
    display: inline-block;
    margin-top: 20px;
    text-align: center;
    color: #4CAF50;
    text-decoration: none;
    font-size: 16px;
    transition: 0.3s;
    align-items: center;
}

.update-container .back-btn:hover {
    text-decoration: underline;
    color: #2E7D32;
}

/* Responsive Design for Smaller Screens */
@media (max-width: 600px) {
    .update-container {
        padding: 20px;
        width: 90%;
    }

    .update-container button {
        font-size: 16px;
        padding: 10px;
    }

    .update-container .search-box input {
        font-size: 14px;
    }

    .update-container select,
    .update-container input,
    .update-container textarea {
        font-size: 14px;
    }
}

</style>
<body>
<div class="update-container">
    <h2>Update Child Details</h2>

    <!-- Search Bar -->
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="search-child" placeholder="search child by name..." autocomplete="off">
        <div id="search-results" class="search-dropdown"></div>
    </div>

    <!-- Update Form -->
    <form id="update-form" action="update-details.php" method="POST">
        <label for="name">Child's Name:</label>
        <input type="text" id="name" name="name" placeholder="Enter child's name eg Maurice" readonly>
        
        <label for="weight">Weight (kg):</label>
        <input type="number" id="weight" name="weight" placeholder="Enter new weight">

        <label for="height">Height (cm):</label>
        <input type="number" id="height" name="height" placeholder="Enter new height">

        <label for="schedule">Time Schedule:</label>
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
        <label for="diet">Dietary Restrictions:</label>
        <textarea id="diet" name="diet" placeholder="Enter any dietary restrictions"></textarea>

        <button type="submit">Update Details</button>
    </form>
    <a href="../pages/dashboard.php" class="back-btn">Back To Dashboard</a>

</div>
</body>
</html>

<script src="../assets/js/script.js"></script>