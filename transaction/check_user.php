<?php
session_start();
require_once '../config/database.php';

echo "Session data:<br>";
print_r($_SESSION);
echo "<br><br>";

if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "User found in database:<br>";
            echo "User ID: " . $user['user_id'] . "<br>";
            echo "Username: " . $user['username'] . "<br>";
            echo "Email: " . $user['email'] . "<br>";
            echo "Premium Status: " . ($user['is_premium'] ? 'Yes' : 'No') . "<br>";
        } else {
            echo "No user found with ID: " . $_SESSION['user_id'] . "<br>";
            
            // Check all users in database
            $stmt = $pdo->query("SELECT user_id, username, email FROM users");
            echo "<br>All users in database:<br>";
            while ($row = $stmt->fetch()) {
                print_r($row);
                echo "<br>";
            }
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    echo "No user logged in (no session)";
}
?> 