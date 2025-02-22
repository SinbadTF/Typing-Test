<?php
require_once '../config/database.php';

try {
    // Fix users table
    $pdo->exec("ALTER TABLE users MODIFY user_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
    echo "Users table fixed successfully\n";

    // Recreate transactions table
    $pdo->exec("DROP TABLE IF EXISTS transactions");
    
    $sql = "CREATE TABLE transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        screenshot VARCHAR(255) NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "Transactions table recreated successfully";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 