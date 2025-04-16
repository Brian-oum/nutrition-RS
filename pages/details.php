<?php
/*
session_start();
include("../config/db.php");

// Check if the user is logged in
if (!isset($_SESSION["username"])) {
    echo "<script>alert('You must be logged in to add child details.'); window.location.href='login.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parent_username = $_SESSION["username"]; // Get logged-in user's email
    $child_name = $_POST["name"];
    $gender = $_POST["gender"];
    $dob = $_POST["dob"];
    $height = $_POST["height"];
    $weight = $_POST["weight"];
    $dietary_restrictions = $_POST["diet"];

    // Prepare SQL statement to prevent SQL injection
    $query = "INSERT INTO children (parent_username, child_name, gender, dob, height, weight, dietary_restrictions)
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ssssdds", $parent_username, $child_name, $gender, $dob, $height, $weight, $dietary_restrictions);

        if ($stmt->execute()) {
            echo "<script>alert('Child details added successfully!'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Error: Unable to save details.');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Error preparing the statement.');</script>";
    }

    $conn->close();
}
*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Child Details</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
</head>
<body>

  <!-- Include the header -->
  <?php include '../includes/header.php'; ?>

  <!-- Flex container that wraps sidebar + content -->
  <div class="dash-container">
    
    <!-- Sidebar on the left -->
    <?php include '../includes/sidebar.php'; ?>
    
    <!-- Main content area -->
    <main class="content">
      <h2>Add Child's Details</h2>

      <form class="child-form" action="details.php" method="POST">
        <label for="name"><i class="fas fa-user"></i>Child's Name:</label>
        <input type="text" id="name" name="name" placeholder="Enter child's name e.g., Maurice" required>

        <label for="gender"><i class="fas fa-venus-mars"></i>Gender:</label>
        <select id="gender" name="gender" required>
            <option value="">-- Please Select Gender --</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label for="dob"><i class="fas fa-calendar"></i>Date of Birth:</label>
        <input type="date" id="dob" name="dob" required>

        <label for="weight"><i class="fas fa-weight"></i>Weight (kg):</label>
        <input type="number" id="weight" name="weight" placeholder="Enter weight in kg" required>

        <label for="height"><i class="fas fa-ruler-vertical"></i>Height (cm):</label>
        <input type="number" id="height" name="height" placeholder="Enter height in cm" required>

        <label for="diet"><i class="fas fa-utensils"></i>Dietary Restrictions:</label>
        <textarea id="diet" name="diet" placeholder="Enter any dietary restrictions" rows="3"></textarea>

        <button type="submit"><i class="fas fa-paper-plane"></i> Save Details</button>
      </form>
    </main>
  </div>

  <!-- Include footer -->
  <?php include '../includes/footer.php'; ?>

  <script src="../assets/js/script.js"></script>
</body>
</html>
