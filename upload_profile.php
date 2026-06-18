<?php
session_start();
require 'config.php'; // Database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['profile_image'];

    // Generate a unique filename to prevent conflicts
    $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = "profile_" . $user_id . "_" . time() . "." . $fileExt;
    $destination = "uploads/" . $fileName;

    // Validate file type (only allow jpg, png, jpeg)
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    if (!in_array(strtolower($fileExt), $allowedTypes)) {
        die("<script>alert('Invalid file type. Only JPG, JPEG, and PNG allowed!'); window.history.back();</script>");
    }

    // Move the uploaded file to the destination
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Update the user's profile image in the database
        $stmt = $conn->prepare("UPDATE users SET profile_image=? WHERE id=?");
        $stmt->bind_param("si", $fileName, $user_id);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Profile Image Updated Successfully!'); window.location='profile.php';</script>";
    } else {
        echo "<script>alert('File upload failed!'); window.history.back();</script>";
    }
}
?>
