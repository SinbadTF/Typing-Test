<?php
try {
    $host = '127.0.0.1'; // Changed from localhost to IP address
    $dbname = 'typing_test3';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>