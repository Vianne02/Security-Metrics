<?php
require_once('config.php');
session_start();
if (!isset($_SESSION["loggedin"])) {
    header("location:login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f5f9ff;
            font-family: Arial, sans-serif;
        }
        .bg-dark {
            background-color: #003366 !important; /* Adjusted to match login theme */
        }
        .btn-outline-danger, .btn-outline-secondary {
            color: #fff;
        }
        .btn-outline-danger:hover, .btn-outline-secondary:hover {
            color: #fff;
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
        }
        .nav-link.active {
            background-color: #0056b3;
            border-radius: .25rem;
        }
        .nav-link:hover {
            color: #f5f9ff !important;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row min-vh-100 flex-column flex-md-row">
        <aside class="col-12 col-md-3 col-xl-2 p-0 bg-dark flex-shrink-1">
            <nav class="navbar navbar-expand-md navbar-dark bg-dark flex-md-column flex-row align-items-center py-2 text-center sticky-top" id="sidebar">
                <div class="text-center p-3">
                    <a href="#" class="navbar-brand mx-0 font-weight-bold text-nowrap"><i class="fas fa-user-shield"></i> <?php echo $_SESSION["username"]; ?></a>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav flex-column w-100">
                        <li class="nav-item">
                            <a class="nav-link active">Welcome <?php echo $_SESSION["username"]; ?>, you're logged in</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="metricsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Metrics
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="metricsDropdown">
                                <li><a class="dropdown-item" href="knowledge.php">Knowledge Retention</a></li>
                                <li><a class="dropdown-item" href="Participation.php">Participation Rate</a></li>
                                <li><a class="dropdown-item" href="Security.php">Security Incidents</a></li>
                                <li><a class="dropdown-item" href="Behaviour.php">Behaviour</a></li>
                                <li><a class="dropdown-item" href="Feedback.php">Feedback</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="vendorsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Third-party Vendors
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="vendorsDropdown">
                                <li><a class="dropdown-item" href="Risk.php">Risk Management</a></li>
                                <li><a class="dropdown-item" href="Business.php">Business Impact Analysis</a></li>
                                
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="p-3">
                <button class="btn btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal">Logout</button>
            </div>
        </aside>
        <main class="col px-0 flex-grow-1 " style="
    background: url('bg.jpg') no-repeat center center; 
    background-size: cover; /* Cover the entire area */
    min-height: 100vh; /* Ensure it takes at least the height of the viewport */
    background-position: center; /* Center the background image */
">
            <div class="container py-3">
               
            </div>
        </main>
    </div>
</div>

<!-- Logout Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Logout Confirmation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to logout?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <form action="logout.php" method="post">
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
