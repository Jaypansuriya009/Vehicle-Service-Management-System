<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "user") {
    header("Location: login.php"); // Redirect to login page
    exit();
}

require 'config.php'; // Include database connection

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, profile_image, phone_number FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $profile_image,$phone_number);
$stmt->fetch();
$stmt->close();

// Default profile image if not set
if (!$profile_image) {
    $profile_image = "default.jpg"; // Default profile image
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile | User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #212529;
            font-family: 'Arial', sans-serif;
        }
       
        .profile-container {
            text-align: center;
            margin-bottom: 20px;
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
           
       margin-left: 320px;
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

<div class="sidebar">
    <h4 class="text-center"><b>VSMS | USER</b></h4>
    <div class="profile-container text-center">
        <img src="uploads/<?php echo htmlspecialchars($profile_image); ?>" alt="User Profile" class="profile-img">
        <div class="username">
            <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>
        </div>
    </div>
    <a href="user_dashboard.php" ><i class="fas fa-home"></i> Dashboard</a>
    <a href="profile.php" class="active"><i class="fas fa-user"></i> Profile</a>
    <a href="service_requests.php"><i class="fas fa-car"></i> My Services</a>
    <a href="enquiries.php"><i class="fas fa-question-circle"></i> Enquiries</a>
    <a href="logout.php" class="btn btn-danger logout-btn" style="color: white;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
    <h2 >👤 My Profile</h2>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-lg p-4">
                <h5 class="fw-bold">👤 Account Details</h5>
                <p><b>Name:</b> <?php echo htmlspecialchars($name); ?></p>
                <p><b>Email:</b> <?php echo htmlspecialchars($email); ?></p>
                <p><b>phone number:</b> <?php echo htmlspecialchars($phone_number); ?></p>
                
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-lg p-4">
                <h5 class="fw-bold">🔒 Update Password</h5>
                <form action="update_password.php" method="post">
                    <div class="mb-3">
                        <label>Current Password</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label>New Password</label>
                        <input type="password" class="form-control" name="new_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Password</button>
                </form>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-lg p-4 text-center">
                <h5 class="fw-bold">🖼️ Change Profile Picture</h5>
                <form action="upload_profile.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="profile_image" class="form-control mt-3" required>
                    <button type="submit" class="btn btn-primary w-100 mt-3">Upload New Picture</button>
                </form>
            </div>
        </div>
    </div>

<div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-lg p-4">
                <h5 class="fw-bold">✏️ Update Details</h5>
                <form action="update_details.php" method="post">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Phone Number</label>
                        <input type="text" class="form-control" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Details</button>
                </form>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
