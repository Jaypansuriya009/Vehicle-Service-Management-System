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






// Fetch all inquiries with user details
$query = "SELECT 
            inquiries.id, inquiries.inquiry_type, inquiries.description, inquiries.created_at, 
            users.username, users.email, users.profile_image 
          FROM inquiries 
          INNER JOIN users ON inquiries.user_id = users.id
          ORDER BY inquiries.created_at DESC";

$result = $conn->query($query);







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
.search-bar {
    margin: 20px 0;
    width: 50%;
    padding: 10px;
    border: 2px solid #00509e;
    border-radius: 10px;
    margin-bottom: 15px;
    float: right; /* Moves it to the right */
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
    <a href="admin.php" ><i class="fas fa-tachometer-alt"></i> Dashboard</a>
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
    <a href="admin_enquiries.php" class="active"><i class="fas fa-envelope-open-text"></i> Enquiries</a>
    <a href="logout.php" class="btn btn-danger logout-btn" style="color: white;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>




<div class="main-content container mt-5">
<?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<input type="text" id="searchInput" class="search-bar" placeholder="Search inquiries...">
    <div class="container mt-5">
    <h2 class="text-center mb-4"><b>Customer Inquiries</b></h2>
    <table class="table table-bordered table-striped table-dark" id="inquiryTable">
        <thead>
            <tr>
                <th>User Name</th>
                <th>Email</th>
                <th>Inquiry Type</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['inquiry_type']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            <td>
            <button class="btn btn-primary btn-sm respond-btn" 
    data-bs-toggle="modal" 
    data-bs-target="#respondModal" 
    data-id="<?php echo $row['id']; ?>">
    Respond
</button>

            </td>
        </tr>
    <?php } ?>
</tbody>

    </table>

    <!-- Respond Modal -->
<div class="modal fade" id="respondModal" tabindex="-1" aria-labelledby="respondModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="respondModalLabel">Respond to Inquiry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="response.php" method="POST">
    <input type="hidden" name="id" id="inquiryId">
    <div class="form-group">
        <label for="response">Response:</label>
        <textarea name="response" class="form-control" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

        </div>
    </div>
</div>

</div>
</div>

<script>
document.getElementById('searchInput').addEventListener('input', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#inquiryTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

document.addEventListener("DOMContentLoaded", function () {
    var respondButtons = document.querySelectorAll(".respond-btn");
    respondButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            var inquiryId = this.getAttribute("data-id");
            document.getElementById("inquiryId").value = inquiryId;
        });
    });
});


</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
