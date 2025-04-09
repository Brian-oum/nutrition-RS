<?php
include '../config/db.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Child Progress</title>
    <link rel="stylesheet" href="../assets/css/track.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Main Progress Container */
        .progress-container {
            width: 80%;
            margin: 40px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .progress-container h2 {
            font-size: 26px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }

        .search-box {
            margin-bottom: 30px;
        }
        .search-box label {
            font-size: 18px;
            color: #333;
            margin-right: 10px;
        }

        .search-box select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fff;
            width: 60%;
            cursor: pointer;
            transition: border 0.3s ease;
        }
        .search-box select:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }

        .chart-container {
            margin-top: 40px;
        }
        .back-btn {
            display: inline-block;
            margin-top: 30px;
            color: #4CAF50;
            text-decoration: none;
            font-size: 16px;
            padding: 8px 16px;
            border: 2px solid #4CAF50;
            border-radius: 30px;
            transition: 0.3s;
        }
        .back-btn:hover {
            background-color: #4CAF50;
            color: white;
            text-decoration: none;

        }
    </style>
</head>
<body>

<div class="progress-container">
    <h2>Track Child Progress</h2>

    <div class="search-box">
        <label for="child_id">Select Child:</label>
        <select id="child_id" name="child_id">
            <option value="">-- Select a Child --</option>
            <?php
            $sql = "SELECT id, child_name FROM children";
            $result = $conn->query($sql);
            while ($child = $result->fetch_assoc()) {
                echo "<option value='{$child['id']}'>{$child['child_name']}</option>";
            }
            ?>
        </select>
    </div>

    <div class="chart-container">
        <canvas id="progressChart"></canvas>
    </div>
    <a href="../pages/dashboard.php" class="back-btn">Back To Dashboard</a>
</div>

<script>
document.getElementById('child_id').addEventListener('change', function() {
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
        }
    }
});

function updateGraph(childName, progressData) {
    let weights = progressData.map(p => p.weight);
    let schedules = progressData.map(p => p.schedule);

    progressChart.data.labels = schedules;
    progressChart.data.datasets[0].data = weights;
    progressChart.options.plugins = { title: { display: true, text: `Weight Progress for ${childName}` } };
    progressChart.update();
}
</script>
</body>
</html>
