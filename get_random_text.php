<?php
require_once 'config/database.php';

$stmt = $pdo->prepare("SELECT content FROM typing_texts ORDER BY RAND() LIMIT 1");
$stmt->execute();
$text = $stmt->fetch();

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'content' => $text['content'] ?? "The quick brown fox jumps over the lazy dog."
]);
?>