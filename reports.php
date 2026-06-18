<?php
session_start();
require 'config.php'; // Include database connection

// Restrict access to admins only
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch report data directly in this file
$sql = "SELECT id, username, email, phone_number FROM users";
$result = $conn->query($sql);

$reports = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reports</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #6B8DD6, #8E37D7);
            min-height: 100vh;
            margin: 0;
        }

        .container {
            margin-top: 50px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .navbar {
            background: rgba(0, 0, 0, 0.8) !important;
            position: relative;
        }

        /* Logout button styling */
        .navbar .btn-danger {
            position: absolute;
            top: 10px;
            right: 20px;
            padding: 8px 15px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 5px;
        }

        @media (max-width: 992px) {
            .navbar .btn-danger {
                position: static;
                margin-left: auto;
            }
        }

        .table {
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        .no-data {
            text-align: center;
            font-size: 18px;
            color: red;
            margin-top: 20px;
        }
    </style>
</head>
<body>
   


    <!-- Reports Content -->
    <div class="container">
        <h2 class="text-center">User Reports</h2>

        <?php if (!empty($reports)): ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                            
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No data available.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
