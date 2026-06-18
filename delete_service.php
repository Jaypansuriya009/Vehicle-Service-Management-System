<?php
session_start();
require 'config.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

// Check if service request ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $service_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Verify the service request belongs to the logged-in user
    $stmt = $conn->prepare("SELECT id FROM service_requests WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $service_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Delete the service request
        $stmt = $conn->prepare("DELETE FROM service_requests WHERE id = ?");
        $stmt->bind_param("i", $service_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Service request deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete service request.";
        }
    } else {
        $_SESSION['error'] = "Invalid service request.";
    }
    $stmt->close();
} else {
    $_SESSION['error'] = "No valid request ID provided.";
}

$conn->close();
header("Location: service_requests.php");
exit();
?>
