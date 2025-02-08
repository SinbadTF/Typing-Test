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
        $stmt = $pdo->prepare("
            INSERT INTO transactions (user_id, amount, payment_method, status, screenshot_path, created_at)
            VALUES (?, ?, 'KBZ Pay', 'pending', ?, NOW())
        ");
        
        $stmt->execute([$user_id, $amount, $screenshot_path]);
        
        // Redirect with success message
        header('Location: index.php?status=success');
        exit();
        
    } catch (PDOException $e) {
        // Redirect with error message
        header('Location: index.php?status=error');
        exit();
    }
}