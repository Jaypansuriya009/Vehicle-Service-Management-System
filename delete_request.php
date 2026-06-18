<?php
session_start();
include 'config.php'; // Ensure you have the correct database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];

    $stmt = $conn->prepare("DELETE FROM service_requests WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $request_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Service request deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete service request.";
    }

    header("Location: service_requests.php"); // Replace with your actual page
    exit();
}
?>
