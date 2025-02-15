<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $amount = $_POST['amount'];
    
    // Handle file upload
    $screenshot = $_FILES['screenshot'];
    $screenshot_path = '';
    
    if ($screenshot['error'] === 0) {
        $upload_dir = '../uploads/transactions/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $filename = uniqid() . '_' . time() . '_' . $screenshot['name'];
        $target_path = $upload_dir . $filename;
        
        // Update the screenshot path storage
        if (move_uploaded_file($screenshot['tmp_name'], $target_path)) {
            // Store the path relative to the root directory
            $screenshot_path = 'uploads/transactions/' . $filename;
        }
    }
    
    try {
        // Validate inputs
        if (empty($user_id) || empty($name) || empty($phone) || empty($amount)) {
            throw new Exception("All fields are required");
        }

        if (!is_numeric($amount) || $amount <= 0) {
            throw new Exception("Invalid amount");
        }

        // Validate phone number format
        if (!preg_match('/^(\+959|09)\d{9,11}$/', $phone)) {
            throw new Exception("Invalid phone number format");
        }

        // Check if screenshot was uploaded
        if ($screenshot['error'] !== 0) {
            throw new Exception("Screenshot upload failed: " . getFileUploadError($screenshot['error']));
        }

        // Validate file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($screenshot['type'], $allowed_types)) {
            throw new Exception("Invalid file type. Only JPG, PNG and GIF are allowed");
        }

        $stmt = $pdo->prepare("
            INSERT INTO transactions (user_id, name, phone, amount, payment_method, status, screenshot_path, created_at)
            VALUES (?, ?, ?, ?, 'KBZ Pay', 'pending', ?, NOW())
        ");
        
        $stmt->execute([$user_id, $name, $phone, $amount, $screenshot_path]);
        
        // Redirect with success message
        header('Location: index.php?status=success');
        exit();
        
    } catch (Exception $e) {
        // Encode the error message to safely pass in URL
        $error_message = urlencode($e->getMessage());
        header("Location: index.php?status=error&message={$error_message}");
        exit();
    }
}

// Helper function to get file upload error message
function getFileUploadError($code) {
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
            return "The uploaded file exceeds the upload_max_filesize directive";
        case UPLOAD_ERR_FORM_SIZE:
            return "The uploaded file exceeds the MAX_FILE_SIZE directive";
        case UPLOAD_ERR_PARTIAL:
            return "The uploaded file was only partially uploaded";
        case UPLOAD_ERR_NO_FILE:
            return "No file was uploaded";
        case UPLOAD_ERR_NO_TMP_DIR:
            return "Missing a temporary folder";
        case UPLOAD_ERR_CANT_WRITE:
            return "Failed to write file to disk";
        case UPLOAD_ERR_EXTENSION:
            return "File upload stopped by extension";
        default:
            return "Unknown upload error";
    }
}