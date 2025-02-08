<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Start transaction
    $pdo->beginTransaction();

    // Check if progress already exists
    $stmt = $pdo->prepare("SELECT id FROM lesson_progress WHERE user_id = ? AND lesson_id = ?");
    $stmt->execute([$data['userId'], $data['lessonId']]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Update existing progress
        $stmt = $pdo->prepare("UPDATE lesson_progress SET wpm = ?, accuracy = ?, completed_at = NOW() WHERE id = ?");
        $stmt->execute([$data['wpm'], $data['accuracy'], $existing['id']]);
    } else {
        // Insert new progress
        $stmt = $pdo->prepare("INSERT INTO lesson_progress (user_id, lesson_id, wpm, accuracy) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['userId'], $data['lessonId'], $data['wpm'], $data['accuracy']]);
    }

    // Commit transaction
    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}