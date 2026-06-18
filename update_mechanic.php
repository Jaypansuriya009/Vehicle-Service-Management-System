<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if mechanic ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid mechanic ID.");
}

$mechanic_id = intval($_GET['id']);

// Fetch mechanic details
$stmt = $conn->prepare("SELECT name, email, contact, address FROM mechanics WHERE id = ?");
$stmt->bind_param("i", $mechanic_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Mechanic not found.");
}

$mechanic = $result->fetch_assoc();
$stmt->close();

// Update mechanic details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $contact = htmlspecialchars($_POST['contact']);
    $address = htmlspecialchars($_POST['address']);

    $stmt = $conn->prepare("UPDATE mechanics SET name = ?, email = ?, contact = ?, address = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $name, $email, $contact, $address, $mechanic_id);
    
    if ($stmt->execute()) {
        $message = "Mechanic updated successfully!";
        header("Location: manage_mechanics.php");
        exit();
    } else {
        $message = "Error updating mechanic.";
    }
    
    $stmt->close();
}



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
    <title>Update Mechanic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
<!-- Sidebar -->

<body>

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
    <!-- Sidebar Links -->
    <a href="user_dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
    <a href="admin_profile.php"><i class="fas fa-user"></i> Profile</a>
    
        <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user"></i> Mechanics
        </a>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
            <li><a class="dropdown-item" href="add_mechanics.php">Add Mechanics</a></li>
            <li><a class="dropdown-item" href="manage_mechanics.php">Manage Mechanics</a></li>
        </ul>
   
    <a href="admin__service_requests.php"><i class="fas fa-car"></i> My Services</a>
    <a href="admin_enquiries.php"><i class="fas fa-question-circle"></i> Enquiries</a>
    <a href="logout.php" class="btn btn-danger logout-btn" style="color: white;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
<div class="content">
    <div class="container mt-5">
        <h2 class="text-center">Update Mechanic</h2>
        
        <?php if (isset($message)): ?>
            <div class="alert alert-info"> <?php echo $message; ?> </div>
        <?php endif; ?>
        
        <form method="POST" class="bg-light p-4 rounded">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($mechanic['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($mechanic['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact</label>
                <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars($mechanic['contact']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($mechanic['address']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Mechanic</button>
            <a href="manage_mechanics.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
    
</body>
</html>
