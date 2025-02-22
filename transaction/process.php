<?php
session_start();
require_once '../config/database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '../error.log'); // This will create error.log in your root directory

// Add this to test database connection
try {
    $pdo->query("SELECT 1");
    error_log("Database connection successful");
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
}

// Debug user ID
error_log("Session user_id: " . $_SESSION['user_id']);

// Check if user exists before processing
try {
    $checkUser = $pdo->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $checkUser->execute([$_SESSION['user_id']]);
    $user = $checkUser->fetch();
    
    if (!$user) {
        error_log("User not found in database: " . $_SESSION['user_id']);
        $_SESSION['error'] = "Invalid user account. Please try logging in again.";
        header('Location: payment.php?status=error');
        exit();
    }
    
    error_log("User found in database: " . $user['user_id']);
} catch (PDOException $e) {
    error_log("Error checking user: " . $e->getMessage());
    $_SESSION['error'] = "Database error. Please try again.";
    header('Location: payment.php?status=error');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Debug information
        error_log("POST data: " . print_r($_POST, true));
        error_log("FILES data: " . print_r($_FILES, true));

        // Validate form data
        if (empty($_POST['name']) || empty($_POST['phone']) || empty($_POST['amount'])) {
            throw new Exception("All fields are required");
        }

        // Get form data
        $name = trim($_POST['name']);
        $phone = trim($_POST['phone']);
        $amount = floatval($_POST['amount']);
        $user_id = $_SESSION['user_id'];

        // Validate file upload
        if (!isset($_FILES['screenshot']) || $_FILES['screenshot']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Screenshot upload failed: " . getFileUploadError($_FILES['screenshot']['error']));
        }

        // Handle file upload
        $screenshot = $_FILES['screenshot'];
        $fileName = time() . '_' . basename($screenshot['name']);
        $uploadDir = '../uploads/';
        $targetPath = $uploadDir . $fileName;

        // Debug directory information
        error_log("Upload directory exists: " . (file_exists($uploadDir) ? 'yes' : 'no'));
        error_log("Upload directory writable: " . (is_writable($uploadDir) ? 'yes' : 'no'));

        // Create uploads directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new Exception("Failed to create uploads directory");
            }
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($screenshot['type'], $allowedTypes)) {
            throw new Exception("Invalid file type. Only JPG, PNG and GIF are allowed");
        }

        // Begin transaction
        $pdo->beginTransaction();

        try {
            // Move uploaded file
            if (!move_uploaded_file($screenshot['tmp_name'], $targetPath)) {
                error_log("Failed to move file. Error: " . error_get_last()['message']);
                throw new Exception("Failed to move uploaded file");
            }

            // Debug database operations
            error_log("Attempting to insert transaction record");
            
            // Insert transaction record
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, name, phone, amount, screenshot, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            if (!$stmt->execute([$user_id, $name, $phone, $amount, $fileName])) {
                error_log("Database error: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Failed to insert transaction record");
            }

            // Commit transaction
            $pdo->commit();
            error_log("Transaction submitted successfully");

            $_SESSION['success'] = "Transaction submitted successfully! Please wait for admin approval.";
            header('Location: payment.php?status=success');
            exit();

        } catch (Exception $e) {
            // Rollback on error
            $pdo->rollBack();
            error_log("Transaction failed: " . $e->getMessage());
            // Delete uploaded file if it exists
            if (file_exists($targetPath)) {
                unlink($targetPath);
            }
            throw $e;
        }
    } catch (Exception $e) {
        error_log("Transaction Error: " . $e->getMessage());
        $_SESSION['error'] = "Error submitting transaction: " . $e->getMessage();
        header('Location: payment.php?status=error');
        exit();
    }
}

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