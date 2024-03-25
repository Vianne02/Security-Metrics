<?php
require_once('config.php'); // Ensure your config file is included
session_start();

if (!isset($_SESSION["loggedin"])) {
    header("location: login.php");
    exit;
}

// Initialize variables
$incidents_before = $incidents_after = $incident_response_rating = $continuity_plan_effectiveness = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $incidents_before = $_POST['incidents_before'];
    $incidents_after = $_POST['incidents_after'];
    $incident_response_rating = $_POST['incident_response_rating'];
    $continuity_plan_effectiveness = $_POST['continuity_plan_effectiveness'];

    // Insert data into the database
    if ($stmt = $link->prepare("INSERT INTO business_impact_analysis (incidents_before, incidents_after, incident_response_rating, continuity_plan_effectiveness) VALUES (?, ?, ?, ?)")) {
        $stmt->bind_param("iiii", $incidents_before, $incidents_after, $incident_response_rating, $continuity_plan_effectiveness);

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Impact Analysis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        min-height: 100vh;
        background-color: #cedbed;
        color: #333;
    }
    .form-section {
        padding: 20px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        width: 30%; /* Adjusted form width */
    }
    .table-section {
        padding: 20px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        width: 65%; /* Increased table space */
    }
    .scrollable-table {
        max-height: 400px;
        overflow-y: auto;
    }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row min-vh-100 flex-column flex-md-row">
        <main class="col px-0 flex-grow-1">
            <div class="container py-3 d-flex justify-content-between"> <!-- Adjusted layout -->
                <div class="form-section">
                    <h2>Business Impact Analysis Form</h2>
                    <form method="post">
                        <div class="mb-3">
                            <label for="incidents_before" class="form-label">Incidents Before Continuity Plan</label>
                            <input type="number" class="form-control" id="incidents_before" name="incidents_before" required>
                        </div>
                        <div class="mb-3">
                            <label for="incidents_after" class="form-label">Incidents After Continuity Plan</label>
                            <input type="number" class="form-control" id="incidents_after" name="incidents_after" required>
                        </div>
                        <div class="mb-3">
                            <label for="incident_response_rating" class="form-label">Incident Response (1-5)</label>
                            <input type="number" class="form-control" id="incident_response_rating" name="incident_response_rating" min="1" max="5" required>
                        </div>
                        <div class="mb-3">
                            <label for="continuity_plan_effectiveness" class="form-label">Plan Effectiveness (1-5)</label>
                            <input type="number" class="form-control" id="continuity_plan_effectiveness" name="continuity_plan_effectiveness" min="1" max="5" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="dashboard.php" class="btn btn-outline-secondary">Go to Dashboard</a>
                    </form>
                </div>
                <div class="table-section">
                    <h2>Analysis Data</h2>
                    <div class="scrollable-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Incidents Before</th>
                                    <th scope="col">Incidents After</th>
                                    <th scope="col">Response Rating</th>
                                    <th scope="col">Plan Effectiveness</th>
                                    <th scope="col">Date Recorded</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT incidents_before, incidents_after, incident_response_rating, continuity_plan_effectiveness, date_recorded FROM business_impact_analysis ORDER BY date_recorded DESC";
                                if ($result = $link->query($query)) {
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['incidents_before']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['incidents_after']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['incident_response_rating']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['continuity_plan_effectiveness']) . "</td>";
                                            echo "<td>" . $row['date_recorded'] . "</td>";
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
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
