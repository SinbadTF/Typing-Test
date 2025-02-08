<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$id = $_GET['id'] ?? '';
$type = $_GET['type'] ?? 'english';
$table = ($type === 'japanese') ? 'japanese_lessons' : 'lessons';

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
    if ($stmt->execute([$id])) {
        header('Location: manage_lessons.php?success=deleted');
        exit();
    } else {
        header('Location: manage_lessons.php?error=delete_failed');
        exit();
    }
} else {
    header('Location: manage_lessons.php');
    exit();
}
?>