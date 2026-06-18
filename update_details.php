<?php
session_start();
require 'config.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo "Unauthorized access.";
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    
    if (empty($username) || empty($email) || empty($phone_number)) {
        echo "All fields are required.";
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }
    
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, phone_number = ? WHERE id = ?");
    $stmt->bind_param("sssi", $username, $email, $phone_number, $user_id);
    
    if ($stmt->execute()) {
        echo "Profile updated successfully.";
        header("Location: profile.php");
                exit();
    } else {
        echo "Error updating profile.";
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
