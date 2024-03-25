<?php
require_once('config.php'); // Use your existing config file for DB connection
session_start();

if (!isset($_SESSION["loggedin"])) {
    header("location: login.php");
    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['feedback'])) {
    $feedback = (int)$_POST['feedback'];

    // Insert feedback into database with current timestamp
    if ($stmt = $link->prepare("INSERT INTO feedback (feedback, date_submitted) VALUES (?, NOW())")) {
        $stmt->bind_param("i", $feedback);

        if (!$stmt->execute()) {
            echo "<script>alert('Oops! Something went wrong. Please try again later.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Database connection error.');</script>";
    }
}

// Initialize an array to hold feedback counts and latest feedback time
$feedbackCounts = [
    5 => ['count' => 0, 'description' => 'Great', 'latest_date_submitted' => ''],
    4 => ['count' => 0, 'description' => 'Extremely Good', 'latest_date_submitted' => ''],
    3 => ['count' => 0, 'description' => 'Good', 'latest_date_submitted' => ''],
    2 => ['count' => 0, 'description' => 'Moderate', 'latest_date_submitted' => ''],
    1 => ['count' => 0, 'description' => 'Poor', 'latest_date_submitted' => ''],
];

// Fetch feedback counts and latest feedback time
if ($result = $link->query("SELECT feedback, COUNT(*) as count, MAX(date_submitted) as latest_date_submitted FROM feedback GROUP BY feedback")) {
    while ($row = $result->fetch_assoc()) {
        $feedbackCounts[$row['feedback']]['count'] = $row['count'];
        $feedbackCounts[$row['feedback']]['latest_date_submitted'] = $row['latest_date_submitted'];
    }
    $result->free();
}

// Prepare data for the chart
$labels = json_encode(array_column($feedbackCounts, 'description'));
$data = json_encode(array_column($feedbackCounts, 'count'));

$link->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
    <style>
    body {
        min-height: 100vh;
        background-color: #cedbed;
        color: #333;
    }
    .feedback-section {
        padding: 20px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
    .flashcard {
        padding: 20px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        text-align: center;
        font-size: 18px;
        margin-top: 20px;
    }
    </style>
</head>
<body>

<div class="container py-3">
    <div class="row">
        <div class="col-md-4 feedback-section">
            <h2>Submit Feedback</h2>
            <form method="post">
                <label for="feedback" class="form-label">Average Feedback:</label>
                <select class="form-select" id="feedback" name="feedback" required>
                    <option value="5">Great</option>
                    <option value="4">Extremely Good</option>
                    <option value="3">Good</option>
                    <option value="2">Moderate</option>
                    <option value="1">Poor</option>
                </select>
                <button type="submit" class="btn btn-primary mt-3">Submit</button>
                <a href="dashboard.php" class="btn btn-outline-secondary">Go to Dashboard</a>
            </form>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-info mt-3" data-bs-toggle="modal" data-bs-target="#feedbackChartModal">
              View Feedback Chart
            </button>
        </div>
        <div class="col-md-8">
    <?php foreach ($feedbackCounts as $score => $info): ?>
        <?php
        // Assign a color based on the feedback score
        switch ($score) {
            case 5:
                $color = '#c8e6c9'; // Green for 'Great'
                break;
            case 4:
                $color = '#b3e5fc'; // Light blue for 'Extremely Good'
                break;
            case 3:
                $color = '#fff9c4'; // Yellow for 'Good'
                break;
            case 2:
                $color = '#ffe0b2'; // Orange for 'Moderate'
                break;
            case 1:
                $color = '#ffcdd2'; // Red for 'Poor'
                break;
            default:
                $color = '#f0f0f0'; // Default background color
        }
        ?>
        <div class="flashcard" style="background-color: <?php echo $color; ?>;">
            Feedback: <b><?php echo $info['description']; ?></b> (Score: <?php echo $score; ?>)<br>
            Total Count: <?php echo $info['count']; ?><br>
            Latest Feedback Time: <?php echo (!empty($info['latest_date_submitted'])) ? date('Y-m-d H:i:s', strtotime($info['latest_date_submitted'])) : "N/A"; ?>
        </div>
    <?php endforeach; ?>
</div>

    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="feedbackChartModal" tabindex="-1" aria-labelledby="feedbackChartModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="feedbackChartModalLabel">Feedback Chart</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <canvas id="feedbackChart"></canvas>
      </div>
    </div>
  </div>
</div>

<script>
// Chart.js chart
document.addEventListener('DOMContentLoaded', function () {
  var ctx = document.getElementById('feedbackChart').getContext('2d');
  var feedbackChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?php echo $labels; ?>,
      datasets: [{
        label: 'Number of Feedbacks',
        data: <?php echo $data; ?>,
        backgroundColor: [
          'rgba(255, 99, 132, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(153, 102, 255, 0.2)'
        ],
        borderColor: [
          'rgba(255, 99, 132, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
