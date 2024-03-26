<?php
require_once('config.php'); // Ensure you have a config file for DB connection
session_start();

if (!isset($_SESSION["loggedin"])) {
    header("location: login.php");
    exit;
}

// Initialize variables to avoid "Undefined variable" notices
$securitylast = $securitycurrent = 0;
$result = $securitycurrent - $securitylast;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $securitylast = isset($_POST['security_last']) ? (int)$_POST['security_last'] : 0;
    $securitycurrent = isset($_POST['security_current']) ? (int)$_POST['security_current'] : 0;

    // Calculation logic for result and performance
    $result = $securitycurrent - $securitylast; // Numerical difference

    if ($result < 0) {
        $performance = "Improved";
    } elseif ($result == 0) {
        $performance = "Stagnant";
    } else {
        $performance = "Reduced";
    }

    if ($stmt = $link->prepare("INSERT INTO security_incidents (security_last, security_current, result, performance, date_published) VALUES (?, ?, ?, ?, NOW())")) {
        $stmt->bind_param("iiis", $securitylast, $securitycurrent, $result, $performance);

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
$chartDataQuery = "SELECT date_published, security_last, security_current FROM security_incidents ORDER BY date_published ASC";
$chartResult = $link->query($chartDataQuery);

$labels = [];
$SecurityLastData = [];
$SecurityCurrentData = [];

while ($row = $chartResult->fetch_assoc()) {
    $labels[] = $row['date_published'];
    $SecurityLastData[] = $row['security_last'];
    $SecurityCurrentData[] = $row['security_current'];
}

// Convert PHP arrays to JSON
$jsonLabels = json_encode($labels);
$jsonSecurityLastData = json_encode($SecurityLastData);
$jsonSecurityCurrentData = json_encode($SecurityCurrentData);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Participant Feedback</title>
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
                        <h2>Security incidents Form</h2>
                        <form method="post">
                            <div class="mb-3">
                                <label for="participants_last" class="form-label">No. of Security Incidents Before Training</label>
                                <input type="number" class="form-control" id="security_last" name="security_last" required>
                            </div>
                            <div class="mb-3">
                                <label for="participants_current" class="form-label">No. of Security Incidents after Current Training</label>
                                <input type="number" class="form-control" id="security_current" name="security_current" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="dashboard.php" class="btn btn-outline-secondary">Go to Dashboard</a>
                        </form>
                    </div>
                    <div class="col-md-8 table-section">
                        <h2>Security Incidents</h2>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#graphModal">
                          Show Graph
                        </button>
    <a href="generateSec.php" class="btn btn-secondary">Generate report</a>

                        <div class="scrollable-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Last Training</th>
                                        <th scope="col">Current Training</th>
                                        <th scope="col">Result</th>
                                        <th scope="col">Performance</th>
                                        <th scope="col">Date Published</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT security_last, security_current, result, performance, date_published FROM security_incidents ORDER BY date_published DESC";
                                    if ($result = $link->query($query)) {
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $row['security_last'] . "</td>";
                                                echo "<td>" . $row['security_current'] . "</td>";
                                                echo "<td>" . $row['result'] . "</td>";
                                                // Apply color based on performance
                                                if ($row['performance'] === "Improved") {
                                                    echo "<td style='color: green;'>" . $row['performance'] . "</td>";
                                                } elseif ($row['performance'] === "Reduced") {
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

<!-- Modal for Graph -->
<div class="modal fade" id="graphModal" tabindex="-1" aria-labelledby="graphModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="graphModalLabel">Security Graph</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <canvas id="SecurityGraph" width="400" height="400"></canvas>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalElement = document.getElementById('graphModal');
    modalElement.addEventListener('shown.bs.modal', function (event) {
        var ctx = document.getElementById('SecurityGraph').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo $jsonLabels; ?>,
                datasets: [{
                    label: 'Security Incidents before Training',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    data: <?php echo $jsonSecurityLastData; ?>
                }, {
                    label: 'Security Incidents after Current Training',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    data: <?php echo $jsonSecurityCurrentData; ?>
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
});
</script>

</body>
</html>
