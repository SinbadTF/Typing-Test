<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    $stmt = $pdo->prepare("INSERT INTO myanmar_lesson_progress 
        (user_id, lesson_id, wpm, accuracy, mistakes, time_spent, completed_at, status) 
        VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)");
    
    // Set status based on accuracy
    $status = $data['accuracy'] >= 80 ? 'completed' : 'in_progress';
    
    $stmt->execute([
        $_SESSION['user_id'],
        $data['lesson_id'],
        $data['wpm'],
        $data['accuracy'],
        $data['mistakes'],
        $data['time_spent'],
        $status
    ]);

    // Update lesson status if accuracy is >= 80%
    if ($data['accuracy'] >= 80) {
        $stmt = $pdo->prepare("UPDATE myanmar_lessons 
            SET status = 'completed', completed_at = NOW() 
            WHERE id = ? AND user_id = ?");
        $stmt->execute([$data['lesson_id'], $_SESSION['user_id']]);
    }
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}