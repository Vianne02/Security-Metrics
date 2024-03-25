<?php
require_once('config.php'); // Ensure you have a config file for DB connection
session_start();

if (!isset($_SESSION["loggedin"])) {
    header("location: login.php");
    exit;
}

// Initialize variables
$subjectMatter = $incidentsBefore = $incidentsAfter = $result = $performance = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subjectMatter = $_POST['subject_matter'];
    $incidentsBefore = isset($_POST['incidents_before']) ? (int)$_POST['incidents_before'] : 0;
    $incidentsAfter = isset($_POST['incidents_after']) ? (int)$_POST['incidents_after'] : 0;

    // Calculation logic for result
    $result = $incidentsAfter - $incidentsBefore; // Numerical difference

    // Determine performance based on result
    if ($result < 0) {
        $performance = "Improved";
    } elseif ($result == 0) {
        $performance = "Stagnant";
    } else {
        $performance = "Worsened";
    }

    // Insert data into the database
    if ($stmt = $link->prepare("INSERT INTO behaviour (subject_matter, incidents_before, incidents_after, result, performance, date_published) VALUES (?, ?, ?, ?, ?, NOW())")) {
        $stmt->bind_param("siiss", $subjectMatter, $incidentsBefore, $incidentsAfter, $result, $performance);

        if ($stmt->execute()) {
            echo "<script>alert('Data submitted successfully.');</script>";
        } else {
            echo "<script>alert('Oops! Something went wrong. Please try again later.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Database connection error.');</script>";
    }
}

// Fetch data for chart
$chartDataQuery = "SELECT subject_matter, incidents_before, incidents_after, date_published FROM behaviour ORDER BY date_published ASC";
$chartResult = $link->query($chartDataQuery);

$subjectMatters = [];
$incidentsBeforeData = [];
$incidentsAfterData = [];
$dates = [];

while ($row = $chartResult->fetch_assoc()) {
    $subjectMatters[] = $row['subject_matter'];
    $incidentsBeforeData[] = $row['incidents_before'];
    $incidentsAfterData[] = $row['incidents_after'];
    $dates[] = $row['date_published'];
}

// Convert PHP arrays to JSON
$jsonSubjectMatters = json_encode($subjectMatters);
$jsonIncidentsBeforeData = json_encode($incidentsBeforeData);
$jsonIncidentsAfterData = json_encode($incidentsAfterData);
$jsonDates = json_encode($dates);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Behaviour Analysis</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    /* Add your styles here */
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
                        <!-- Form for input -->
                        <h2>Behaviour Form</h2>
                        <form method="post">
                            <div class="mb-3">
                                <label for="subject_matter" class="form-label">Subject Matter</label>
                                <input type="text" class="form-control" id="subject_matter" name="subject_matter" required>
                            </div>
                            <div class="mb-3">
                                <label for="incidents_before" class="form-label">Incidents Before Training</label>
                                <input type="number" class="form-control" id="incidents_before" name="incidents_before" required>
                            </div>
                            <div class="mb-3">
                                <label for="incidents_after" class="form-label">Incidents After Training</label>
                                <input type="number" class="form-control" id="incidents_after" name="incidents_after" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="dashboard.php" class="btn btn-outline-secondary">Go to Dashboard</a>
                        </form>
                    </div>
                    <div class="col-md-8 table-section">
                        <h2>Behaviour Data</h2>
                        <!-- Button to trigger modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#graphModal">
                          Show Graph
                        </button>
                        <!-- Table to display data -->
                        <div class="scrollable-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Subject Matter</th>
                                        <th scope="col">Security Incidents Before Training</th>
                                        <th scope="col">Security Incidents After Training</th>
                                        <th scope="col">Result</th>
                                        <th scope="col">Performance</th>
                                        <th scope="col">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch and display data from the database
                                    $query = "SELECT subject_matter, incidents_before, incidents_after, result, performance, date_published FROM behaviour ORDER BY date_published DESC";
                                    if ($result = $link->query($query)) {
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . htmlspecialchars($row['subject_matter']) . "</td>";
                                                echo "<td>" . $row['incidents_before'] . "</td>";
                                                echo "<td>" . $row['incidents_after'] . "</td>";
                                                echo "<td>" . $row['result'] . "</td>";
                                                  // Apply color based on performance
                                                  if ($row['performance'] === "Improved") {
                                                    echo "<td style='color: green;'>" . $row['performance'] . "</td>";
                                                } elseif ($row['performance'] === "Worsened") {
                                                    echo "<td style='color: red;'>" . $row['performance'] . "</td>";
                                                } elseif ($row['performance'] === "Stagnant") {
                                                    echo "<td style='color: orange;'>" . $row['performance'] . "</td>";
                                                }
                                                echo "<td>" . $row['date_published'] . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='6'>No data found</td></tr>";
                                        }
                                        $result->free();
                                    } else {
                                        echo "<script>alert('Error fetching data');</script>";
                                   
                                    }
                                    $link->close();
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
        <h5 class="modal-title" id="graphModalLabel">Behaviour Analysis Graph</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <canvas id="behaviorChart" width="400" height="400"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Your existing HTML structure goes here -->
<script>
// Parse PHP variables to JavaScript
var subjectMatters = JSON.parse('<?php echo $jsonSubjectMatters; ?>');
var incidentsBeforeData = JSON.parse('<?php echo $jsonIncidentsBeforeData; ?>');
var incidentsAfterData = JSON.parse('<?php echo $jsonIncidentsAfterData; ?>');
var dates = JSON.parse('<?php echo $jsonDates; ?>');

var ctx = document.getElementById('behaviorChart').getContext('2d');
var behaviorChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: subjectMatters,
        datasets: [{
            label: 'Incidents Before Training',
            data: incidentsBeforeData,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }, {
            label: 'Incidents After Training',
            data: incidentsAfterData,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    afterBody: function(context) {
                        var dateIndex = context[0].dataIndex;
                        var date = dates[dateIndex];
                        return 'Date: ' + date;
                    }
                }
            }
        }
    }
});
</script>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
