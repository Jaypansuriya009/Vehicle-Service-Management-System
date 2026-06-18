<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

require 'config.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $profile_image);
$stmt->fetch();
$stmt->close();

$profile_image = !empty($profile_image) ? 'uploads/' . $profile_image : 'assets/default-avatar.png';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request.");
}

$service_id = intval($_GET['id']);

// Fetch service request details
$stmt = $conn->prepare("SELECT id, user_id, category, vehicle_name, vehicle_model, vehicle_brand, 
                               vehicle_registration_number, service_date, service_time, delivery_type, 
                               status, admin_remark, admin_remark_date, service_charge, parts_charge, 
                               other_charges, total_amount, assigned_mechanic 
                        FROM service_requests 
                        WHERE id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("No service request found.");
}
$service = $result->fetch_assoc();
$stmt->close();

// Fetch mechanics from database
$mechanics = [];
$mech_result = $conn->query("SELECT name FROM mechanics");
while ($row = $mech_result->fetch_assoc()) {
    $mechanics[] = $row['name'];
}

// Handle form submission for updating admin details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_details']) || isset($_POST['mark_complete']) || isset($_POST['mark_rejected'])) {
        $admin_remark = $_POST['admin_remark'];
        $service_charge = floatval($_POST['service_charge']);
        $parts_charge = floatval($_POST['parts_charge']);
        $other_charges = floatval($_POST['other_charges']);
        $total_amount = $service_charge + $parts_charge + $other_charges;
        $assigned_mechanic = $_POST['assigned_mechanic'];
        $admin_remark_date = date("Y-m-d H:i:s");

        $status = $service['status'];
        if (isset($_POST['mark_complete'])) {
            $status = 'Completed';
        } elseif (isset($_POST['mark_rejected'])) {
            $status = 'Rejected';
        }

        $update_stmt = $conn->prepare("UPDATE service_requests SET 
                                    admin_remark = ?, 
                                    admin_remark_date = ?, 
                                    service_charge = ?, 
                                    parts_charge = ?, 
                                    other_charges = ?, 
                                    total_amount = ?,
                                    assigned_mechanic = ?,
                                    status = ? 
                                    WHERE id = ?");
        $update_stmt->bind_param("ssdiddssi", $admin_remark, $admin_remark_date, $service_charge, 
                                $parts_charge, $other_charges, $total_amount, $assigned_mechanic, $status, $service_id);

        if ($update_stmt->execute()) {
            echo "<script>alert('Details updated successfully.'); window.location.href='view_details.php?id=$service_id';</script>";
        } else {
            echo "<script>alert('Error updating details.');</script>";
        }
        $update_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | View Service Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #212529; font-family: 'Arial', sans-serif; }
        .sidebar { width: 300px; height: 100vh; background: #f8f9fa; position: fixed; top: 0; left: 0; padding-top: 20px; box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2); }
        .sidebar a { width: 100%; padding: 10px 20px; text-align: left; display: block; color: #212529; text-decoration: none; transition: 0.3s; }
        .sidebar a:hover, .sidebar .active { background: #495057; }
        .profile-img { width: 100px; height: 100px; border-radius: 50%; border: 3px solid white; object-fit: cover; }
        .main-content { margin-left: 310px; padding: 40px 20px; background-color: #f8f9fa; border-radius: 15px; }
        .btn-primary, .btn-danger, .btn-success { background-color: #212529; border: none; }
        .btn-primary:hover, .btn-danger:hover, .btn-success:hover { background-color: #343a40; }
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
    <!-- Sidebar Links -->
    <a href="admin.php" ><i class="fas fa-home"></i> Dashboard</a>
    <a href="admin_profile.php"><i class="fas fa-user"></i> Profile</a>
    
        <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user"></i> Mechanics
        </a>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
            <li><a class="dropdown-item" href="add_mechanics.php">Add Mechanics</a></li>
            <li><a class="dropdown-item" href="manage_mechanics.php">Manage Mechanics</a></li>
        </ul>
   
    <a href="registered_users.php"><i class="fas fa-car"></i> Users</a>
    <a href="admin__service_requests.php" class="active"><i class="fas fa-car"></i> Service Request</a>
    <a href="category.php"><i class="fas fa-car"></i> Category</a>
    <a href="admin_enquiries.php"><i class="fas fa-question-circle"></i> Enquiries</a>
    <a href="logout.php" class="btn btn-danger logout-btn" style="color: white;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content container">
    <h2 class="text-center mb-4">Service Request Details</h2>
    <div class="row">
        <!-- Vehicle & Service Info -->
        <div class="col-md-6">
            <div class="card p-3 mb-3">
                <h4>Vehicle Details</h4>
                <p><strong>Category:</strong> <?= htmlspecialchars($service['category']); ?></p>
                <p><strong>Vehicle Name:</strong> <?= htmlspecialchars($service['vehicle_name']); ?></p>
                <p><strong>Model:</strong> <?= htmlspecialchars($service['vehicle_model']); ?></p>
                <p><strong>Brand:</strong> <?= htmlspecialchars($service['vehicle_brand']); ?></p>
                <p><strong>Registration Number:</strong> <?= htmlspecialchars($service['vehicle_registration_number']); ?></p>
            </div>
            <div class="card p-3 mb-3">
                <h4>Service Information</h4>
                <p><strong>Service Date:</strong> <?= htmlspecialchars($service['service_date']); ?></p>
                <p><strong>Service Time:</strong> <?= htmlspecialchars($service['service_time']); ?></p>
                <p><strong>Delivery Type:</strong> <?= htmlspecialchars($service['delivery_type']); ?></p>
                <p><strong>Assigned Mechanic:</strong> <?= htmlspecialchars($service['assigned_mechanic']) ?: 'Not Assigned'; ?></p>
                <p><strong>Status:</strong>
                    <span class="badge bg-<?php 
                        if ($service['status'] == 'Completed') echo 'success';
                        elseif ($service['status'] == 'Rejected') echo 'danger';
                        else echo 'warning';
                    ?>">
                    <?= htmlspecialchars($service['status']); ?></span>
                </p>
            </div>
        </div>

        <!-- Admin Remarks & Charges -->
        <div class="col-md-6">
            <div class="card p-3 mb-3">
                <h4>Admin Remarks</h4>
                <p><strong>Remark:</strong> <?= htmlspecialchars($service['admin_remark'] ?: 'N/A'); ?></p>
                <p><strong>Remark Date:</strong> <?= htmlspecialchars($service['admin_remark_date'] ?: 'N/A'); ?></p>
            </div>
            <div class="card p-3 mb-3">
                <h4>Charges</h4>
                <p><strong>Service Charge:</strong> ₹<?= number_format($service['service_charge'], 2); ?></p>
                <p><strong>Parts Charge:</strong> ₹<?= number_format($service['parts_charge'], 2); ?></p>
                <p><strong>Other Charges:</strong> ₹<?= number_format($service['other_charges'], 2); ?></p>
                <p><strong>Total Amount:</strong> ₹<?= number_format($service['total_amount'], 2); ?></p>
            </div>
        </div>
    </div>

    <!-- Update Form -->
    <div class="card p-3 mt-3">
        <h4>Update Service Details</h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Admin Remark</label>
                <textarea class="form-control" name="admin_remark"><?= htmlspecialchars($service['admin_remark']); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Assign Mechanic</label>
                <select name="assigned_mechanic" class="form-control">
                    <option value="">-- Select Mechanic --</option>
                    <?php foreach ($mechanics as $mechanic): ?>
                        <option value="<?= htmlspecialchars($mechanic); ?>" <?= $service['assigned_mechanic'] === $mechanic ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mechanic); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Service Charge</label>
                    <input type="number" step="0.01" class="form-control" name="service_charge" value="<?= htmlspecialchars($service['service_charge']); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Parts Charge</label>
                    <input type="number" step="0.01" class="form-control" name="parts_charge" value="<?= htmlspecialchars($service['parts_charge']); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Other Charges</label>
                    <input type="number" step="0.01" class="form-control" name="other_charges" value="<?= htmlspecialchars($service['other_charges']); ?>" required>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4"><button type="submit" name="update_details" class="btn btn-primary">Update Details</button></div>
                <div class="col-md-4"><button type="submit" name="mark_complete" class="btn btn-success">Mark as Completed</button></div>
                <div class="col-md-4"><button type="submit" name="mark_rejected" class="btn btn-danger">Mark as Rejected</button></div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
