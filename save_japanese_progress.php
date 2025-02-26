<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

// Validate required data
if (!isset($data['wpm']) || !isset($data['accuracy']) || !isset($data['mistakes']) || !isset($data['time_taken']) || !isset($data['lesson_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit();
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Check if progress already exists
    $stmt = $pdo->prepare("SELECT id FROM japanese_lesson_progress WHERE user_id = ? AND lesson_id = ?");
    $stmt->execute([$_SESSION['user_id'], $data['lesson_id']]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Update existing progress
        $stmt = $pdo->prepare("UPDATE japanese_lesson_progress 
                              SET wpm = ?, accuracy = ?, mistakes = ?, 
                                  time_taken = ?, completed_at = NOW(),
                                  status = 'completed'
                              WHERE id = ?");
        $stmt->execute([
            $data['wpm'],
            $data['accuracy'],
            $data['mistakes'],
            $data['time_taken'],
            $existing['id']
        ]);
    } else {
        // Insert new progress
        $stmt = $pdo->prepare("INSERT INTO japanese_lesson_progress 
                              (user_id, lesson_id, wpm, accuracy, mistakes, time_taken, status) 
                              VALUES (?, ?, ?, ?, ?, ?, 'completed')");
        $stmt->execute([
            $_SESSION['user_id'],
            $data['lesson_id'],
            $data['wpm'],
            $data['accuracy'],
            $data['mistakes'],
            $data['time_taken']
        ]);
    }

    // Commit transaction
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Progress saved successfully']);
} catch (PDOException $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}