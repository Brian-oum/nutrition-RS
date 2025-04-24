<?php
session_start();
include '../config/db.php';  

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    echo "<script>alert('Please log in to view this page.'); window.location.href='login.php';</script>";
    exit();
}

// Subscription check
$parent_username = $_SESSION["username"];
$now = date('Y-m-d H:i:s');
$subQuery = "SELECT * FROM payments WHERE username = ? AND expiry_date > ?";
$subStmt = $conn->prepare($subQuery);
$subStmt->bind_param("ss", $parent_username, $now);
$subStmt->execute();
$subResult = $subStmt->get_result();

if ($subResult->num_rows === 0) {
    echo "<script>alert('You need an active subscription to access this feature.'); window.location.href='make_payment.php';</script>";
    exit();
}
$subStmt->close();

// Verify DB connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Track Child Progress</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link rel="stylesheet" href="../assets/css/style.css"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="dash-container">
    <?php include '../includes/sidebar.php'; ?>

    <main class="content">
        <h2>Track Child Progress</h2>

        <div class="progress-wrapper">
            <div class="search-box">
                <label for="child_id"><i class="fas fa-child"></i> Select Child:</label>
                <select id="child_id" name="child_id">
                    <option value="">-- Select a Child --</option>
                    <?php
                    $sql = "SELECT id, child_name FROM children WHERE parent_username = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $parent_username);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($child = $result->fetch_assoc()) {
                        $id = htmlspecialchars($child['id']);
                        $name = htmlspecialchars($child['child_name']);
                        echo "<option value='{$id}'>{$name}</option>";
                    }
                    $stmt->close();
                    ?>
                </select>
            </div>

            <div class="chart-container">
                <canvas id="progressChart"></canvas>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>

<script>
let ctx = document.getElementById('progressChart').getContext('2d');
let progressChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [],
        datasets: [{
            label: 'Weight (kg)',
            data: [],
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1,
            barThickness: 30,
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: { title: { display: true, text: 'Schedule' } },
            y: { title: { display: true, text: 'Weight (kg)' }, beginAtZero: true }
        },
        plugins: {
            title: {
                display: true,
                text: 'Weight Progress'
            }
        }
    }
});

document.getElementById('child_id').addEventListener('change', function () {
    let childId = this.value;
    if (childId) {
        fetch(`fetch_progress.php?child_id=${childId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                updateGraph(data.child_name, data.progress);
            })
            .catch(error => console.error('Error:', error));
    }
});

function updateGraph(childName, progressData) {
    let weights = progressData.map(p => p.weight);
    let schedules = progressData.map(p => p.schedule);

    progressChart.data.labels = schedules;
    progressChart.data.datasets[0].data = weights;
    progressChart.options.plugins.title.text = `Weight Progress for ${childName}`;
    progressChart.update();
}
</script>
<script src="../assets/js/script.js"></script>

</body>
</html>
