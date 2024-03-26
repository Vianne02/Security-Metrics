<?php
require_once('config.php'); // This includes your database connection
session_start();

if (!isset($_SESSION["loggedin"])) {
    header("location: login.php");
    exit;
}

// Initialize variables to avoid "Undefined variable" notices
$knowledgeBefore = $knowledgeAfter = 0;
$result = $performance = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $knowledgeBefore = isset($_POST['knowledge_before']) ? (int)$_POST['knowledge_before'] : 0;
    $knowledgeAfter = isset($_POST['knowledge_after']) ? (int)$_POST['knowledge_after'] : 0;

    // Calculation logic for result and performance
    $result = $knowledgeAfter - $knowledgeBefore; // Numerical difference
    $performanceDifference = $result;

    if ($performanceDifference > 0) {
        $performance = "Improved";
    } elseif ($performanceDifference == 0) {
        $performance = "Stagnant";
    } else {
        $performance = "Depreciated";
    }

    if ($stmt = $link->prepare("INSERT INTO knowledge (knowledge_before, knowledge_after, result, performance, date_published) VALUES (?, ?, ?, ?, NOW())")) {
        $stmt->bind_param("iiis", $knowledgeBefore, $knowledgeAfter, $result, $performance);

        if ($stmt->execute()) {
            echo "<script>alert('Feedback submitted successfully.');</script>";
        } else {
            echo "<script>alert('Oops! Something went wrong. Please try again later.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Database connection error.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    body {
        min-height: 100vh;
        background-color: #cedbed;
        color: #333;
    }
    .form-section, .table-section {
        padding: 20px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
    .scrollable-table {
        max-height: 400px;
        overflow-y: auto;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .table thead th {
        background-color: #007bff;
        color: #ffffff;
    }
    .modal-body {
        max-height: calc(100vh - 210px);
        overflow-y: auto;}
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row min-vh-100 flex-column flex-md-row">
        <main class="col px-0 flex-grow-1">
            <div class="container py-3">
                <div class="row">
                    <div class="col-md-4 form-section">
                        <h2>Metric : Knowledge Retention</h2>
                        <form method="post">
                            <div class="mb-3">
                                <label for="knowledgeBefore" class="form-label">Knowledge Before Training</label>
                                <select class="form-select" id="knowledge_before" name="knowledge_before">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="knowledgeAfter" class="form-label">Knowledge After Training</label>
                                <select class="form-select" id="knowledge_after" name="knowledge_after">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="dashboard.php" class="btn btn-outline-secondary">Go to Dashboard</a>
                        </form>
                        
                    </div>
                    
                    <div class="col-md-8 table-section">
                        <h2>Feedback Results</h2>
                        <!-- Modal Button -->
                        <button type="button" class="btn btn-info mb-3" data-bs-toggle="modal" data-bs-target="#graphModal">
                            View Knowledge Graph
                        </button>
                        <a href="genKnow.php" class="btn btn-secondary">Generate report</a>

                        <div class="scrollable-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Knowledge Before</th>
                                        <th scope="col">Knowledge After</th>
                                        <th scope="col">Result</th>
                                        <th scope="col">Performance</th>
                                        <th scope="col">Date Published</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT knowledge_before, knowledge_after, result, performance, date_published FROM knowledge ORDER BY date_published DESC";
                                    if ($result = $link->query($query)) {
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $row['knowledge_before'] . "</td>";
                                                echo "<td>" . $row['knowledge_after'] . "</td>";
                                                echo "<td>" . $row['result'] . "</td>";
                                                if ($row['performance'] === "Improved") {
                                                    echo "<td style='color: green;'>" . $row['performance'] . "</td>";
                                                } elseif ($row['performance'] === "Depreciated") {
                                                    echo "<td style='color: red;'>" . $row['performance'] . "</td>";
                                                } elseif ($row['performance'] === "Stagnant") {
                                                    echo "<td style='color: orange;'>" . $row['performance'] . "</td>";
                                                }
                                                echo "<td>" . $row['date_published'] . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='5'>No data found</td></tr>";
                                        }
                                        $result->close();
                                    } else {
                                        echo "<tr><td colspan='5'>Error fetching data</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="graphModal" tabindex="-1" aria-labelledby="graphModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="graphModalLabel">Knowledge Graph</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <canvas id="knowledgeGraph" width="400" height="400"></canvas>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var ctx = document.getElementById('knowledgeGraph').getContext('2d');
    var knowledgeBefore = [];
    var knowledgeAfter = [];
    var labels = [];

    <?php
    $sql = "SELECT knowledge_before, knowledge_after, date_published FROM knowledge ORDER BY date_published ASC";
    if ($result = $link->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            echo "knowledgeBefore.push(" . $row['knowledge_before'] . ");";
            echo "knowledgeAfter.push(" . $row['knowledge_after'] . ");";
            echo "labels.push('" . $row['date_published'] . "');";
        }
    }
    ?>

    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Knowledge Before',
                data: knowledgeBefore,
                borderColor: 'rgb(255, 99, 132)',
                tension: 0.1
            }, {
                label: 'Knowledge After',
                data: knowledgeAfter,
                borderColor: 'rgb(54, 162, 235)',
                tension: 0.1
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
</body>
</html>
