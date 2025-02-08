<?php
$host = '127.0.0.1'; // Using IP instead of localhost
$dbname = 'typing_certificates';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => false
        )
    );
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>