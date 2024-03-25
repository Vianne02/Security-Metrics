<?php
require_once('config.php'); // Make sure this points to the correct path of your configuration file
session_start();

if (!isset($_SESSION["loggedin"])) {
    header("location: login.php");
    exit;
}

// Initialize variables
$type_of_risk = $description = $controls = "";
$number_of_incidents = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type_of_risk = $_POST['type_of_risk'];
    $description = $_POST['description'];
    $controls = $_POST['controls'];
    $number_of_incidents = isset($_POST['number_of_incidents']) ? (int)$_POST['number_of_incidents'] : 0;

    // Prepare SQL query to insert data into the database
    $sql = "INSERT INTO risk_management (type_of_risk, description, controls, number_of_incidents) VALUES (?, ?, ?, ?)";

    if ($stmt = $link->prepare($sql)) {
        $stmt->bind_param("sssi", $type_of_risk, $description, $controls, $number_of_incidents);

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
    <title>Risk Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
    .form-section {
        width: 30%;
    }
    .table-section {
        width: 65%;
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
            <div class="container py-3 d-flex justify-content-between">
                <div class="form-section">
                    <h2>Risk Management Form</h2>
                    <form method="post">
                        <div class="mb-3">
                            <label for="type_of_risk" class="form-label">Type of Risk</label>
                            <input type="text" class="form-control" id="type_of_risk" name="type_of_risk" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="controls" class="form-label">Controls</label>
                            <textarea class="form-control" id="controls" name="controls" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="number_of_incidents" class="form-label">Number of Incidents</label>
                            <input type="number" class="form-control" id="number_of_incidents" name="number_of_incidents" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="dashboard.php" class="btn btn-outline-secondary">Go to Dashboard</a>
                    </form>
                </div>
                <div class="table-section">
                    <h2>Risk Data</h2>
                    <div class="scrollable-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Type of Risk</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Controls</th>
                                    <th scope="col">Number of Incidents</th>
                                    <th scope="col">Date Recorded</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT type_of_risk, description, controls, number_of_incidents, date_added FROM risk_management ORDER BY date_added DESC";
                                if ($result = $link->query($query)) {
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['type_of_risk']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['controls']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['number_of_incidents']) . "</td>";
                                            echo "<td>" . $row['date_added'] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No data found</td></tr>";
                                    }
                                    $result->close();
                                } else {
                                    // Enhanced error handling
                                    echo "<tr><td colspan='5'>Error fetching data: " . $link->error . "</td></tr>";
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
