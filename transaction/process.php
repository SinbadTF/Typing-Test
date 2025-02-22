<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $amount = $_POST['amount'];
        $user_id = $_SESSION['user_id'];

        // Handle file upload
        $screenshot = $_FILES['screenshot'];
        $fileName = time() . '_' . $screenshot['name'];
        $targetPath = '../uploads/' . $fileName;

        // Create uploads directory if it doesn't exist
        if (!file_exists('../uploads')) {
            mkdir('../uploads', 0777, true);
        }

        // Move uploaded file
        if (move_uploaded_file($screenshot['tmp_name'], $targetPath)) {
            // Insert transaction record
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, name, phone, amount, screenshot, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$user_id, $name, $phone, $amount, $fileName]);

            // Update user's premium status
            $stmt = $pdo->prepare("UPDATE users SET is_premium = 1 WHERE user_id = ?");
            $stmt->execute([$user_id]);

            header('Location: payment.php?status=success');
            exit();
        } else {
            throw new Exception("Error uploading file");
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        header('Location: payment.php?status=error');
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