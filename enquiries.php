<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "user") {
    header("Location: login.php"); // Redirect to login page
    exit();
}

require 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $inquiry_type = htmlspecialchars($_POST['inquiry_type'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');

    $stmt = $conn->prepare("INSERT INTO inquiries (user_id, inquiry_type, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $inquiry_type, $description);
    if ($stmt->execute()) {
        $message = "Inquiry submitted successfully!";
    } else {
        $message = "There was an error submitting your inquiry. Please try again.";
    }
    $stmt->close();
}

// Fetch inquiry history
$sql = "SELECT inquiry_type, description, created_at FROM inquiries WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();



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
    <title>Inquiry Form and History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>

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
        .main-content {

            width: 75%;
            margin-top: 50px;
            margin-left: 310px;
            padding: 20px;
            border: 2px solid black;
            border-radius: 25px;
           
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
    <h4 class="text-center text-bold"><b> VSMS | USER</b></h4>
    
    <!-- User Profile Image -->
<div class="profile-container text-center">
    <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="User Profile" class="profile-img">
    <div class="username">
        <?php echo htmlspecialchars($name); ?>
    </div>
</div>

    <!-- Sidebar Links -->
    <a href="user_dashboard.php" ><i class="fas fa-home"></i> Dashboard</a>
    <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
    <a href="service_requests.php"><i class="fas fa-car"></i> My Services</a>
    <a href="enquiries.php" class="active"><i class="fas fa-question-circle"></i> Enquiries</a>
    <a href="logout.php" class="btn btn-danger logout-btn" style="color: white;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
<!-- Main Content -->
<div class="main-content container py-5">
    <!-- Inquiry Form Section -->
    <div class="form-container p-4 shadow-lg rounded-4 glassy-bg">
        <h2 class="card-title text-center fw-bold  " style="color: #212529;">📩 Submit an Inquiry</h2>

        <?php if (isset($message)): ?>
            <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="enquiries.php">
            <div class="mb-3">
                <label for="inquiry_type" class="form-label fw-semibold">Inquiry Type</label>
                <select class="form-select border-0 shadow-sm" id="inquiry_type" name="inquiry_type" required>
                    <option value="Service Request">🔧 Service Request</option>
                    <option value="Complaint">⚠️ Complaint</option>
                    <option value="General Inquiry">💡 General Inquiry</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Description</label>
                <textarea class="form-control border-0 shadow-sm" id="description" name="description" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn  w-100 py-2 fw-bold" style="background-color:#212529; color:white" >🚀 Submit Inquiry</button>
        </form>
    </div>

    <!-- Inquiry History Section -->
<div class="history-container mt-5 p-4 shadow-lg rounded-4 glassy-bg">
    <h2 class="card-title text-center fw-bold text-success">📜 Your Inquiry History</h2>

    <?php
    $stmt = $conn->prepare("SELECT inquiry_type, description, responce, created_at FROM inquiries WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <?php if ($result->num_rows == 0): ?>
        <div class="alert alert-warning text-center mt-3">You have not submitted any inquiries yet. 🤷‍♂️</div>
    <?php else: ?>
        <div class="list-group mt-3">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="list-group-item shadow-sm rounded-3 mb-2 p-3">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1 text-primary fw-bold"><?php echo htmlspecialchars($row['inquiry_type']); ?></h5>
                        <small class="text-muted">
                            📅 <?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?>
                        </small>
                    </div>
                    <p class="mb-1 text-dark"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>

                    <?php if (!empty($row['responce'])): ?>
                        <div class="alert alert-success mt-2 p-2 rounded">
                            <strong>Admin Response:</strong>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($row['responce'])); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-secondary mt-2 p-2 rounded">⏳ Awaiting admin response...</div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

</div>

 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>