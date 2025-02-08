<?php
$db = new mysqli('127.0.0.1', 'root', '', 'kbz_transactions');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Get screenshot path before deleting
    $sql = "SELECT screenshot_path FROM transactions WHERE id = $id";
    $result = $db->query($sql);
    if ($row = $result->fetch_assoc()) {
        $screenshot_path = $row['screenshot_path'];
        // Delete the screenshot file
        if (file_exists($screenshot_path)) {
            unlink($screenshot_path);
        }
    }
    
    // Delete the record
    $sql = "DELETE FROM transactions WHERE id = $id";
    if ($db->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error";
    }
}

$db->close();
?>