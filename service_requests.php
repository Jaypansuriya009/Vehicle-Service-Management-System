<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "user") {
    header("Location: login.php"); // Redirect to login page
    exit();
}

require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $category = htmlspecialchars($_POST['category']);
    $vehicle_name = htmlspecialchars($_POST['vehicle_name']);
    $vehicle_model = htmlspecialchars($_POST['vehicle_model']);
    $vehicle_brand = htmlspecialchars($_POST['vehicle_brand']);
    $vehicle_registration_number = htmlspecialchars($_POST['vehicle_registration_number']);
    $service_date = $_POST['service_date'];
    $service_time = $_POST['service_time'];
    $delivery_type = $_POST['delivery_type'];
    $terms_accepted = isset($_POST['terms_accepted']) ? 1 : 0;

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO service_requests 
        (user_id, category, vehicle_name, vehicle_model, vehicle_brand, vehicle_registration_number, service_date, service_time, delivery_type, terms_accepted) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssssi", $user_id, $category, $vehicle_name, $vehicle_model, $vehicle_brand, $vehicle_registration_number, $service_date, $service_time, $delivery_type, $terms_accepted);

    if ($stmt->execute()) {
        $message = "Service request submitted successfully!";
    } else {
        $message = "Error submitting request. Try again.";
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






$sql = "SELECT id, category_name FROM vehicle_categories";
$result = $conn->query($sql);
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
            /* text-align: center;
             
            */
            align-items: center;
            margin-left: 350px;
            padding: 20px;
            color: white;
            max-width: 70%;
            justify-content: center;

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
    .modal-body {
    color: black !important;
}
.modal-content {
        border-radius: 12px;
        box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2);
        border: none;
    }

    .modal-header {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        color: white;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .modal-title {
        font-weight: bold;
        font-size: 1.3rem;
    }

    .modal-body {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 10px;
    }

    .modal-body p {
        font-size: 1rem;
        color: #333;
        margin-bottom: 8px;
    }

    .modal-footer {
        background: #fff;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        border: none;
        border-radius: 8px;
        padding: 10px 16px;
        font-weight: bold;
        transition: all 0.3s ease-in-out;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2575fc, #6a11cb);
        transform: scale(1.05);
    }

    .btn-secondary {
        border-radius: 8px;
        padding: 10px 16px;
        font-weight: bold;
    }

    </style>
</head>
<body>
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
    <a href="service_requests.php" class="active"><i class="fas fa-car"></i> My Services</a>
    <a href="enquiries.php"><i class="fas fa-question-circle"></i> Enquiries</a>
    <a href="logout.php" class="btn btn-danger logout-btn" style="color: white;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <h2 class="text-center mb-4">Submit a Service Request</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST">
            
<div class="mb-3">
    <label>Service Category</label>
    <select class="form-control" name="category" required>
        <option value="">Select Category</option>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row["id"] . "'>" . $row["category_name"] . "</option>";
            }
        }
        ?>
    </select>
</div>
            <div class="mb-3">
                <label>Vehicle Name</label>
                <input type="text" class="form-control" name="vehicle_name" required>
            </div>
            <div class="mb-3">
                <label>Vehicle Model</label>
                <input type="text" class="form-control" name="vehicle_model" required>
            </div>
            <div class="mb-3">
                <label>Vehicle Brand</label>
                <input type="text" class="form-control" name="vehicle_brand" required>
            </div>
            <div class="mb-3">
                <label>Vehicle Registration Number</label>
                <input type="text" class="form-control" name="vehicle_registration_number" required>
            </div>
            <div class="mb-3">
                <label>Service Date</label>
                <input type="date" class="form-control" name="service_date" required>
            </div>
            <div class="mb-3">
                <label>Service Time</label>
                <input type="time" class="form-control" name="service_time" required>
            </div>
            <div class="mb-3">
                <label>Delivery Type</label>
                <select class="form-select" name="delivery_type">
                    <option value="Pickup">Pickup</option>
                    <option value="Drop-off">Drop-off</option>
                </select>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="terms_accepted" required>
                <label class="form-check-label">I accept the terms and conditions</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit Request</button>
        </form>
    </div>
    <h2 class="text-center mt-5">Your Service Requests</h2>
    <div class="table-container mt-3">
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Category</th>
                <th>Vehicle</th>
                <th>Registration</th>
                <th>Service Date</th>
                <th>Time</th>
                <th>Delivery Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $conn->prepare("SELECT * FROM service_requests WHERE user_id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                    <td><?= htmlspecialchars($row['vehicle_name']) ?> - <?= htmlspecialchars($row['vehicle_model']) ?></td>
                    <td><?= htmlspecialchars($row['vehicle_registration_number']) ?></td>
                    <td><?= $row['service_date'] ?></td>
                    <td><?= $row['service_time'] ?></td>
                    <td><?= $row['delivery_type'] ?></td>
                    <td>
                        <!-- View Details Button -->
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailsModal<?= $row['id'] ?>">View Details</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteService(<?= $row['id'] ?>)">Delete</button>
                    </td>
                </tr>

                <div class="modal fade" id="detailsModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="detailsModalLabel<?= $row['id'] ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel<?= $row['id'] ?>">Service Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" id="printContent<?= $row['id'] ?>">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <p><strong>🚗 Category:</strong> <?= htmlspecialchars($row['category']) ?></p>
                        <p><strong>🚘 Vehicle:</strong> <?= htmlspecialchars($row['vehicle_name']) ?> - <?= htmlspecialchars($row['vehicle_model']) ?> (<?= htmlspecialchars($row['vehicle_brand']) ?>)</p>
                        <p><strong>🔢 Registration Number:</strong> <?= htmlspecialchars($row['vehicle_registration_number']) ?></p>
                        <p><strong>📅 Service Date:</strong> <?= $row['service_date'] ?></p>
                        <p><strong>⏰ Service Time:</strong> <?= $row['service_time'] ?></p>
                        <p><strong>📦 Delivery Type:</strong> <?= $row['delivery_type'] ?></p>
                        <p><strong>🛠 Assigned Mechanics:</strong> <?= $row['assigned_mechanic'] ?></p>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <p><strong>✅ Terms Accepted:</strong> <?= $row['terms_accepted'] ? 'Yes' : 'No' ?></p>
                        <p><strong>📅 Created At:</strong> <?= $row['created_at'] ?></p>
                        <p><strong>🔄 Status:</strong> <?= $row['status'] ?></p>
                        <p><strong>📝 Admin Remark:</strong> <?= $row['admin_remark'] ?: 'N/A' ?></p>
                        <p><strong>📆 Admin Remark Date:</strong> <?= $row['admin_remark_date'] ?: 'N/A' ?></p>
                        <p><strong>💰 Service Charge:</strong> ₹<?= number_format($row['service_charge'], 2) ?></p>
                        <p><strong>🛠 Parts Charge:</strong> ₹<?= number_format($row['parts_charge'], 2) ?></p>
                        <p><strong>📦 Other Charges:</strong> ₹<?= number_format($row['other_charges'], 2) ?></p>
                        <p><strong>💵 Total Amount:</strong> <span class="text-success">₹<?= number_format($row['total_amount'], 2) ?></span></p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="printRequest('printContent<?= $row['id'] ?>')">🖨 Print</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Close</button>
            </div>
        </div>
    </div>
</div>


            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>
   
<!-- JavaScript for Print Functionality -->
<script>

function deleteService(serviceId) {
        if (confirm("Are you sure you want to delete this service request?")) {
            window.location.href = "delete_service.php?id=" + serviceId;
        }
    }

    
    function printRequest(divId) {
        let printContent = document.getElementById(divId).innerHTML;
        let originalContent = document.body.innerHTML;

        document.body.innerHTML = `<div>${printContent}</div>`;
        window.print();
        document.body.innerHTML = originalContent;
        location.reload();
    }
</script>

<!-- Ensure Bootstrap JavaScript is Included -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



</body>
</html>
