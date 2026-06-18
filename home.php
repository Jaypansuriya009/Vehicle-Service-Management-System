<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_id'] === true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VSMS | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460);
            color: #ffffff;
        }
        .navbar {
            background: rgba(0, 0, 0, 0.8) !important;
        }
        .hero-section {
            height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: url('https://source.unsplash.com/1600x900/?luxury,car') center/cover no-repeat;
            position: relative;
        }
        .hero-section::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }
        .btn-custom {
            font-size: 1.2rem;
            padding: 10px 20px;
            border-radius: 50px;
        }
        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: none;
            transition: 0.4s;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0px 10px 20px rgba(255, 255, 255, 0.2);
        }
        footer {
            background: #000;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fs-3" href="#"><i class="fas fa-car"></i> VSMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                </ul>
                <ul class="navbar-nav ms-3">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="btn btn-danger text-white btn-custom" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-light me-2 btn-custom" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-custom" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="hero-section">
        <div class="hero-content text-white">
            <h1 class="display-3">Welcome to VSMS</h1>
            <p class="lead">Your one-stop solution for vehicle servicing and management.</p>
            <a href="login.php" class="btn btn-light btn-lg btn-custom">Get Started</a>
        </div>
    </div>

    <div class="container my-5">
        <div class="row text-center">
            <div class="col-md-4">
                <div class="card p-4">
                    <h3><i class="fas fa-tachometer-alt"></i> Dashboard</h3>
                    <p>View total, rejected, and completed services.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4">
                    <h3><i class="fas fa-question-circle"></i> Enquiry</h3>
                    <p>Fill the enquiry form and track responses.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4">
                    <h3><i class="fas fa-tools"></i> Service Request</h3>
                    <p>Submit service requests, check status, and print receipts.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center text-white p-4">
        &copy; 2025 VSMS. All Rights Reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>