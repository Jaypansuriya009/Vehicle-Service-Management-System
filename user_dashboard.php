<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "user") {
    header("Location: login.php"); // Redirect to login page
    exit();
}




require 'config.php'; // Include database connection file

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email , profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $profile_image);


$stmt->fetch();
$stmt->close();

$profile_image = !empty($profile_image) ? 'uploads/' . $profile_image : 'assets/default-avatar.png';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #212529; /* Light background for content */
            font-family: 'Arial', sans-serif;
        }
      
        .sidebar a {
            padding: 15px;
            text-decoration: none;
            font-size: 18px;
            color: black;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background: black; /* Lighter blue on hover */
            color: black;
        }
        .sidebar a.active {
            background: #00509e; /* Active state background */
        }
        .main-content {

            width: 75%;
            margin-top: 25%;
            margin: 310px;
            border: 2px solid black;
            border-radius: 30px;
            background-color: #f8f9fa; /* Light background for content */
        }
        .card {
            border-radius: 15px;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .card-shadow {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .list-group-item {
            font-size: 1rem;
        }
        .text-muted {
            color: #6c757d !important;
        }
        .btn-primary {
            background-color:#212529; /* Match sidebar hover color */
            border-color: #00509e;
        }
        .btn-primary:hover {
            background-color: #212529;
            border-color: #003366;
        }
        .btn-danger {
            background-color: #212529;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: white;
            border-color: #bd2130;
        }
        .sidebar {
    width: 300px;
    height: 100vh;
    background: #f8f9fa ; /* Semi-transparent */
    backdrop-filter: blur(10px); /* Glassmorphism */
    color: #212529;
    position: fixed;
    top: 0;
    left: 0;
    padding-top: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
    border-right: 1px solid rgba(255, 255, 255, 0.2);
}


    .profile-container {
        margin: 20px 0;
    }

    .profile-img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 3px solid white;
        object-fit: cover;
    }

    .sidebar a {
        display: block;
        color: #212529;
        text-decoration: none;
        padding: 10px 20px;
        width: 100%;
        text-align: left;
        transition: 0.3s;
    }

    .sidebar a i {
        margin-right: 20%;
    }

    .sidebar a:hover,
    .sidebar .active {
        background: #495057;
    }
    .profile-img {
    width: 100px; /* Set the width */
    height: 100px; /* Set the height */
    border-radius: 50%; /* Makes it circular */
    border: 3px solid white; /* Optional: Adds a border */
    object-fit: cover; /* Ensures the image fits properly */
}


    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">


</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center text-bold"><b> VSMS | USER</b></h4>
    
    <!-- User Profile Image -->
<div class="profile-container text-center">
    <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="User Profile" class="profile-img">
    <div class="username">
        <?php echo htmlspecialchars($name); ?>
    </div>
</div>

    <!-- Sidebar Links -->
    <!-- Sidebar Links -->
    <a href="user_dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
    <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
    <a href="service_requests.php"><i class="fas fa-car"></i> My Services</a>
    <a href="enquiries.php"><i class="fas fa-question-circle"></i> Enquiries</a>
    <a href="logout.php" class="btn btn-danger logout-btn" style="color: white;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
<!-- Main Content
<div class="profile-container text-end">
<img src="?>" alt="User Profile" class="profile-img">
</div> -->

<hr style="color: white;">
<div class="main-content container mt-5">
    <div class="text-center">
        <h2 class="fw-bold">Welcome, <?php echo htmlspecialchars($name); ?>! 👋</h2>
        <p class="text-muted">Manage your profile and activities effortlessly.</p>
    </div>

    <div class="row mt-4">
        <!-- Profile Card -->
        <div class="col-md-4">
            <div class="card shadow-lg border-0 rounded-4 p-4 text-center">
                <div class="card-body">
                <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="User Profile" class="profile-img">
                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($name); ?></h5>
                    <p class="text-muted mb-1"><?php echo htmlspecialchars($email); ?></p>
                    <a href="profile.php" class="btn btn-primary w-100 mt-3">Edit Profile</a>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-4 p-4">
                <h5 class="fw-bold">📌 Recent Activity</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>🚗 Requested a car service</span>
                        <span class="badge bg-light text-dark">2 days ago</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>📝 Updated profile information</span>
                        <span class="badge bg-light text-dark">1 week ago</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>📩 Raised an enquiry</span>
                        <span class="badge bg-light text-dark">3 weeks ago</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
