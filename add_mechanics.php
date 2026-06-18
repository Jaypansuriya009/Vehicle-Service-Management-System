<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php"); // Redirect to login page
    exit();
}

require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mechanism_name'])) {
    $mechanic_name = trim(htmlspecialchars($_POST['mechanism_name']));
    $mechanic_email = trim(htmlspecialchars($_POST['mechanism_email']));
    $mechanic_contact = trim(htmlspecialchars($_POST['mechanism_contact']));
    $mechanic_address = trim(htmlspecialchars($_POST['mechanism_address']));

    // Validate email
    if (!filter_var($mechanic_email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } elseif (!preg_match("/^[0-9]{10}$/", $mechanic_contact)) { // Validate phone number
        $message = "Invalid contact number. Must be 10 digits.";
    } else {
        try {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM mechanics WHERE email = ?");
            $stmt->bind_param("s", $mechanic_email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $message = "Mechanic with this email already exists!";
            } else {
                // Insert new mechanic
                $stmt = $conn->prepare("INSERT INTO mechanics (name, email, contact, address) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $mechanic_name, $mechanic_email, $mechanic_contact, $mechanic_address);

                if ($stmt->execute()) {
                    $message = "Mechanic added successfully!";
                } else {
                    $message = "Error adding mechanic: " . $stmt->error;
                }
            }
            $stmt->close();
        } catch (Exception $e) {
            $message = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, profile_image FROM users WHERE id = ?");
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
    <title>Service Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
        }
        
        .content {
    background-color: #f8f9fa;
    
    color: black;
    padding: 20px;
    max-width: 70%;
    border-radius: 20px;
    margin-left: 330px; /* Center horizontally */
    display: flex;
    flex-direction: column;
    justify-content: center; /* Center vertically */
 /* Adjust height for proper centering */
}

        body
{
    background-color: #212529;
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
} .glassy-bg {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 15px;
    }
    
    .btn-gradient {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        color: white;
        border: none;
        transition: 0.3s;
    }

    .btn-gradient:hover {
        background: linear-gradient(135deg, #2575fc, #6a11cb);
        transform: scale(1.05);
    }

    .shadow-lg {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    </style>
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
    <a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="admin_profile.php"><i class="fas fa-user-circle"></i> Profile</a>

    <!-- Mechanics Dropdown -->
   
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
    <a href="category.php"><i class="fas fa-layer-group"></i> Category</a>
    <a href="admin_enquiries.php"><i class="fas fa-envelope-open-text"></i> Enquiries</a>
    <a href="logout.php" class="btn btn-danger logout-btn" style="color: white;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>


<!-- Main Content -->
<div class="content">
    <h2 class="text-center mb-4">Add VSMS Mechanics</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST">
            <div class="mb-3">
                <label>Mechanic Name</label>
                <input type="text" class="form-control" name="mechanism_name" required>
            </div>
            <div class="mb-3">
                <label>Mechanic Email</label>
                <input type="text" class="form-control" name="mechanism_email" required>
            </div>
            <div class="mb-3">
                <label>Mechanic Contact Number</label>
                <input type="text" class="form-control" name="mechanism_contact" required>
            </div>
            <div class="mb-3">
                <label>Mechanic Address</label>
                <input type="text" class="form-control" name="mechanism_address" required>
            </div>
           
            <button type="submit" class="btn btn-primary w-100">Submit Request</button>
        </form>
    </div>


    
</div>

</body>
</html>
