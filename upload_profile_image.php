<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $file = $_FILES['profile_image'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if (!in_array($file['type'], $allowedTypes)) {
        $_SESSION['message'] = "Error: Only JPG, PNG and GIF images are allowed";
        header('Location: profile.php');
        exit();
    }

    if ($file['size'] > $maxSize) {
        $_SESSION['message'] = "Error: File size must be less than 5MB";
        header('Location: profile.php');
        exit();
    }

    // Create uploads directory if it doesn't exist
    $uploadDir = __DIR__ . '/uploads/profile_images';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($file['name']);
    $destination = $uploadDir . '/' . $fileName;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Delete old profile image if exists
        $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $oldImage = $stmt->fetchColumn();
        
        if ($oldImage && file_exists($uploadDir . '/' . $oldImage)) {
            unlink($uploadDir . '/' . $oldImage);
        }

        // Update database
        $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE user_id = ?");
        $stmt->execute([$fileName, $_SESSION['user_id']]);
        
        $_SESSION['profile_image'] = $fileName;
        $_SESSION['message'] = "Profile image updated successfully!";
    } else {
        $_SESSION['message'] = "Error: Failed to upload image";
    }

    header('Location: profile.php');
    exit();
}