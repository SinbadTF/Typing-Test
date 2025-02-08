<?php
$host = '127.0.0.1';  // Changed from 'localhost' to explicit IP
$dbname = 'typing_test';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>