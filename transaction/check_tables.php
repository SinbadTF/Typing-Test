<?php
require_once '../config/database.php';

try {
    // Check transactions table structure
    $result = $pdo->query("DESCRIBE transactions");
    echo "<h3>Transactions Table Structure:</h3>";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "Field: " . $row['Field'] . ", Type: " . $row['Type'] . "<br>";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 