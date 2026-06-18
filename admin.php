<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
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






$userCount = $conn->query("SELECT COUNT(*) FROM users where role='user'")->fetch_row()[0];
$enquiryCount = $conn->query("SELECT COUNT(*) FROM inquiries")->fetch_row()[0];
$mechanicsCount = $conn->query("SELECT COUNT(*) FROM mechanics")->fetch_row()[0];
$serviceRequests = $conn->query("SELECT COUNT(*) FROM service_requests")->fetch_row()[0];

$newRequests = $conn->query("SELECT COUNT(*) FROM service_requests WHERE status='Pending'")->fetch_row()[0];
$completeRequests = $conn->query("SELECT COUNT(*) FROM service_requests WHERE status='Completed'")->fetch_row()[0];
$rejectedRequests = $conn->query("SELECT COUNT(*) FROM service_requests WHERE status='Rejected'")->fetch_row()[0];








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



.title {

    text-transform: uppercase;
    font-size: 35px;
    font-weight: bold;
    margin-bottom: 20px;
    color: #333;
}

.dashboard {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
    max-width: 900px;
    margin: auto;
}

.card {
    background: white;
    padding: 20px;
    border-radius: 50%;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
}

.progress {
    position: relative;
    width: 70px;
    height: 70px;
}

.progress::before {
    content: attr(data-value);
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 18px;
    font-weight: bold;
    color: #333;
}

p {
    font-size: 14px;
    margin-top: 10px;
    color: #666;
}


    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">


</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center text-bold"><b> VSMS | ADMIN</b></h4>
    
    <!-- User Profile Image -->
    <div class="profile-container text-center">
        <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="User Profile" class="profile-img">
        <div class="username">
            <?php echo htmlspecialchars($name); ?>
        </div>
    </div>

    <!-- Sidebar Links -->
    <a href="admin.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="admin_profile.php"><i class="fas fa-user-circle"></i> Profile</a>
<!-- Mechanics Dropdown -->
<a class="btn dropdown-toggle w-100 text-start" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="fas fa-tools"></i> Mechanics
</a>
<ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuLink">
    <li><a class="dropdown-item" href="add_mechanics.php"><i class="fas fa-user-plus"></i> Add Mechanics</a></li>
    <li><a class="dropdown-item" href="manage_mechanics.php"><i class="fas fa-users-cog"></i> Manage Mechanics</a></li>
</ul>


    <a href="registered_users.php"><i class="fas fa-users"></i> Users</a>
    <a href="admin__service_requests.php"><i class="fas fa-clipboard-list"></i> Service Requests</a>
    <a href="category.php"><i class="fas fa-th-list"></i> Category</a>
    <a href="admin_enquiries.php"><i class="fas fa-envelope-open-text"></i> Enquiries</a>
    <a href="reports.php"><i class="fas fa-envelope-open-text"></i> Report</a>
    
    <a href="logout.php" class="btn btn-danger logout-btn" style="color: white;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>



<hr style="color: white;">
<div class="main-content container mt-5 ">
<div class="container m-3">
    <h3 class="text-center fw-bold mb-4">ACCOUNT OVERVIEW</h3>
    <div class="row g-4">
        <?php 
        $cards = [
            ["Total Registered User", $userCount, "#00A86B"],
            ["Total Enquiry", $enquiryCount, "#F4A100"],
            ["Total Mechanics", $mechanicsCount, "#E63946"],
            ["Total Service Requests", $serviceRequests, "#007BFF"],
            ["New Service Requests", $newRequests, "#4C4C4C"],
            ["Rejected Service Requests", $rejectedRequests, "#D90429"],
            ["Completed Services", $completeRequests, "#00A86B"]
        ];
        foreach ($cards as $card) : ?>
            <div class="col-md-4">
                <div class="card shadow-lg border-0 rounded-4 text-center p-4" style="background: linear-gradient(135deg, <?php echo $card[2]; ?>, #ffffff20);">
                    <div class="progress mx-auto mb-3" style="height: 10px;">
                        <div class="progress-bar" role="progressbar" style="width: <?php echo $card[1]; ?>%; background: <?php echo $card[2]; ?>;" aria-valuenow="<?php echo $card[1]; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <h5 class="fw-bold text-white"><?php echo $card[0]; ?></h5>
                    <h2 class="fw-bold text-white"><?php echo $card[1]; ?></h2>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
