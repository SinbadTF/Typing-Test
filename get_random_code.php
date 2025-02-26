<?php
session_start();
require_once 'config/database.php';

try {
    $stmt = $pdo->prepare("SELECT content, language FROM programming_snippets ORDER BY RAND() LIMIT 1");
    $stmt->execute();
    $code = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($code) {
        echo json_encode([
            'success' => true,
            'content' => $code['content'],
            'language' => $code['language']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'No code snippets found'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error'
    ]);
}