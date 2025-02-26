<?php
require_once '../config/database.php';

try {
    // Drop existing transactions table if it exists
    $pdo->exec("DROP TABLE IF EXISTS transactions");
    
    // Create transactions table with all required columns
    $sql = "CREATE TABLE transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        screenshot VARCHAR(255) NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "Transactions table recreated successfully";

    // Create uploads directory if it doesn't exist
    $uploadDir = '../uploads/';
    if (!file_exists($uploadDir)) {
        if (mkdir($uploadDir, 0777, true)) {
            echo "<br>Uploads directory created successfully";
        } else {
            echo "<br>Failed to create uploads directory";
        }
    } else {
        echo "<br>Uploads directory already exists";
    }

    // Check if directory is writable
    if (is_writable($uploadDir)) {
        echo "<br>Uploads directory is writable";
    } else {
        echo "<br>Warning: Uploads directory is not writable";
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 