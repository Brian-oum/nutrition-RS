<?php
require_once '../config/db.php';

// In a real application, you would have proper authentication
$childId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($childId <= 0) {
    die("Invalid child ID");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Nutrition Progress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .progress-container {
            width: 100%;
            background-color: #e9ecef;
            border-radius: 0.25rem;
            margin: 1rem 0;
        }
        .progress-bar {
            height: 1.5rem;
            background-color: #28a745;
            border-radius: 0.25rem;
            transition: width 0.6s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .bmi-scale {
            width: 100%;
            height: 20px;
            background: linear-gradient(to right, #3498db, #2ecc71, #f1c40f, #e67e22, #e74c3c);
            border-radius: 10px;
            position: relative;
            margin: 10px 0;
        }
        .bmi-pointer {
            position: absolute;
            top: -5px;
            width: 2px;
            height: 30px;
            background: #000;
            transform: translateX(-50%);
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .positive {
            color: #28a745;
        }
        .negative {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <h1 class="mb-4">Child Nutrition Progress</h1>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 id="childName">Loading...</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Age:</strong> <span id="childAge">-</span></p>
                        <p><strong>Gender:</strong> <span id="childGender">-</span></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Nutrition Progress</h5>
                    </div>
                    <div class="card-body">
                        <div id="weightProgressCard" class="mb-4">
                            <h6>Weight Progress</h6>
                            <p>Current: <span id="currentWeight">-</span> kg</p>
                            <p>Target: <span id="targetWeight">-</span> kg</p>
                            <div class="progress-container">
                                <div id="weightProgressBar" class="progress-bar" style="width: 0%">0%</div>
                            </div>
                            <p id="weightComparison" class="mt-1"></p>
                        </div>
                        
                        <div id="heightProgressCard" class="mb-4">
                            <h6>Height Progress</h6>
                            <p>Current: <span id="currentHeight">-</span> cm</p>
                            <p>Target: <span id="targetHeight">-</span> cm</p>
                            <div class="progress-container">
                                <div id="heightProgressBar" class="progress-bar" style="width: 0%">0%</div>
                            </div>
                            <p id="heightComparison" class="mt-1"></p>
                        </div>
                        
                        <div id="bmiCard">
                            <h6>BMI Status</h6>
                            <div class="bmi-scale">
                                <div id="bmiPointer" class="bmi-pointer"></div>
                            </div>
                            <p><strong>BMI:</strong> <span id="bmiValue">-</span></p>
                            <p id="bmiCategory" class="fw-bold"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const childId = <?php echo $childId; ?>;
            fetchChildProgress(childId);
            
            // Handle form submission
            document.getElementById('measurementForm').addEventListener('submit', function(e) {
                e.preventDefault();
                addNewMeasurement(childId);
            });
        });

        async function fetchChildProgress(childId) {
            try {
                const response = await fetch(`api.php?action=get_child_progress&child_id=${childId}`);
                const data = await response.json();
                
                if (response.ok) {
                    updateChildProfile(data);
                    updateProgressBars(data);
                    updateBMI(data);
                } else {
                    console.error('Error:', data.error);
                    alert('Failed to load child progress: ' + data.error);
                }
            } catch (error) {
                console.error('Network error:', error);
                alert('Network error occurred. Please try again.');
            }
        }

        function updateChildProfile(data) {
            document.getElementById('childName').textContent = data.childInfo.name;
            document.getElementById('childGender').textContent = data.childInfo.gender.charAt(0).toUpperCase() + data.childInfo.gender.slice(1);
            
            // Format age display
            let ageDisplay;
            if (data.ageInMonths < 24) {
                ageDisplay = `${data.ageInMonths} months`;
            } else {
                const years = Math.floor(data.ageInMonths / 12);
                const months = data.ageInMonths % 12;
                ageDisplay = `${years} years`;
                if (months > 0) ageDisplay += ` ${months} months`;
            }
            document.getElementById('childAge').textContent = ageDisplay;
        }

        function updateProgressBars(data) {
            // Update weight progress
            if (data.currentMetrics && data.goals && data.goals.target_weight) {
                document.getElementById('currentWeight').textContent = data.currentMetrics.weight;
                document.getElementById('targetWeight').textContent = data.goals.target_weight;
                
                const weightProgress = Math.min(100, (data.currentMetrics.weight / data.goals.target_weight * 100));
                const weightProgressBar = document.getElementById('weightProgressBar');
                weightProgressBar.style.width = `${weightProgress}%`;
                weightProgressBar.textContent = `${Math.round(weightProgress)}%`;
                
                // Show comparison if previous data exists
                if (data.previousMetrics) {
                    const weightDiff = data.currentMetrics.weight - data.previousMetrics.weight;
                    const comparisonElement = document.getElementById('weightComparison');
                    comparisonElement.textContent = `${weightDiff >= 0 ? '+' : ''}${weightDiff.toFixed(1)} kg from last measurement`;
                    comparisonElement.className = weightDiff >= 0 ? 'positive' : 'negative';
                }
            }
            
            // Update height progress
            if (data.currentMetrics && data.goals && data.goals.target_height) {
                document.getElementById('currentHeight').textContent = data.currentMetrics.height;
                document.getElementById('targetHeight').textContent = data.goals.target_height;
                
                const heightProgress = Math.min(100, (data.currentMetrics.height / data.goals.target_height * 100));
                const heightProgressBar = document.getElementById('heightProgressBar');
                heightProgressBar.style.width = `${heightProgress}%`;
                heightProgressBar.textContent = `${Math.round(heightProgress)}%`;
                
                // Show comparison if previous data exists
                if (data.previousMetrics) {
                    const heightDiff = data.currentMetrics.height - data.previousMetrics.height;
                    const comparisonElement = document.getElementById('heightComparison');
                    comparisonElement.textContent = `${heightDiff >= 0 ? '+' : ''}${heightDiff.toFixed(1)} cm from last measurement`;
                    comparisonElement.className = heightDiff >= 0 ? 'positive' : 'negative';
                }
            }
        }

        function updateBMI(data) {
            if (data.bmi) {
                document.getElementById('bmiValue').textContent = data.bmi;
                
                // Position BMI pointer on scale (assuming scale represents BMI 10-30)
                const bmiPointer = document.getElementById('bmiPointer');
                const bmiPercentage = Math.min(100, Math.max(0, (data.bmi - 10) / 20 * 100));
                bmiPointer.style.left = `${bmiPercentage}%`;
                
                // Determine BMI category
                const bmiCategoryElement = document.getElementById('bmiCategory');
                let category = '';
                let categoryClass = '';
                
                if (data.bmi < 18.5) {
                    category = 'Underweight';
                    categoryClass = 'text-primary';
                } else if (data.bmi < 25) {
                    category = 'Normal weight';
                    categoryClass = 'text-success';
                } else if (data.bmi < 30) {
                    category = 'Overweight';
                    categoryClass = 'text-warning';
                } else {
                    category = 'Obese';
                    categoryClass = 'text-danger';
                }
                
                bmiCategoryElement.textContent = category;
                bmiCategoryElement.className = `fw-bold ${categoryClass}`;
            }
        }

    </script>
</body>
</html>