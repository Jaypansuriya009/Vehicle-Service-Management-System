<?php
session_start();
include 'config.php'; // Ensure your database connection file is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $inquiry_id = $_POST['id'];
    $response_text = $_POST['response'];

    if (!empty($inquiry_id) && !empty($response_text)) {
        $stmt = $conn->prepare("UPDATE inquiries SET responce = ? WHERE id = ?");
        $stmt->bind_param("si", $response_text, $inquiry_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Response submitted successfully.";
        } else {
            $_SESSION['error'] = "Failed to submit response.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "All fields are required.";
    }
}

header("Location: admin_enquiries.php");
exit();
?>
