<?php
require_once '../config/database.php';

// First create the admins table if it doesn't exist
$pdo->exec("CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)");

// Create admin account with PHP's password_hash
$username = 'ChawSuThwe';
$hashedPassword = password_hash('achawlay123', PASSWORD_DEFAULT);

// For verification purposes, let's print the stored values
$stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
$stmt->execute([$username, $hashedPassword]);

// Verify the insertion
$stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
$stmt->execute([$username]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    echo "Admin account created successfully!\n";
    echo "Username: " . $admin['username'] . "\n";
    // Verify if the password works
    if (password_verify('achawlay123', $admin['password'])) {
        echo "Password hash verification successful!\n";
    } else {
        echo "Password hash verification failed!\n";
    }
} else {
    echo "Failed to create admin account!\n";
}
?>