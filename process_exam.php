<?php
session_start();
require_once 'config/database.php';

// Set JSON header
header('Content-Type: application/json');

// Check if request is POST and is JSON
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SERVER["CONTENT_TYPE"]) || strpos($_SERVER["CONTENT_TYPE"], "application/json") === false) {
    echo json_encode(['error' => 'Invalid request method or content type']);
    exit();
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

try {
    // Insert exam results
    $stmt = $pdo->prepare("INSERT INTO certificate_exams (user_id, wpm, accuracy, passed) VALUES (?, ?, ?, ?)");
    
    $stmt->execute([
        $_SESSION['user_id'],
        $data['wpm'],
        $data['accuracy'],
        $data['passed']
    ]);

    echo json_encode([
        'status' => 'success',
        'passed' => $data['passed']
    ]);
    exit();

} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['error' => 'Database error occurred']);
    exit();
}

// Calculate special characters accuracy
function calculateSpecialCharAccuracy($input, $target) {
    preg_match_all('/[^a-zA-Z0-9\s]/', $target, $specialChars);
    $totalSpecialChars = count($specialChars[0]);
    
    preg_match_all('/[^a-zA-Z0-9\s]/', $input, $inputSpecialChars);
    $correctSpecialChars = count(array_intersect($specialChars[0], $inputSpecialChars[0]));
    
    return ($totalSpecialChars > 0) ? ($correctSpecialChars / $totalSpecialChars) * 100 : 0;
}

// Calculate programming accuracy
function calculateProgrammingAccuracy($input, $target) {
    $programmingPatterns = [
        '/function\s+\w+/', '/\{|\}/', '/if\s*\(/', '/return\s+/',
        '/SELECT\s+.*\s+FROM/', '/WHERE\s+.*=/'
    ];
    
    $totalMatches = 0;
    $correctMatches = 0;
    
    foreach ($programmingPatterns as $pattern) {
        preg_match_all($pattern, $target, $targetMatches);
        preg_match_all($pattern, $input, $inputMatches);
        
        $totalMatches += count($targetMatches[0]);
        $correctMatches += count(array_intersect($targetMatches[0], $inputMatches[0]));
    }
    
    return ($totalMatches > 0) ? ($correctMatches / $totalMatches) * 100 : 0;
}

$wpm = $data['wpm'];
$accuracy = $data['accuracy'];
$specialCharAccuracy = calculateSpecialCharAccuracy($data['input'], $data['target']);
$programmingAccuracy = calculateProgrammingAccuracy($data['input'], $data['target']);

// Calculate total score
$totalScore = ($wpm * 0.3) + ($accuracy * 0.3) + ($specialCharAccuracy * 0.2) + ($programmingAccuracy * 0.2);

// Check if passed
$passed = ($wpm >= 40 && 
          $accuracy >= 95 && 
          $specialCharAccuracy >= 90 && 
          $programmingAccuracy >= 90);

// Save results to database
$stmt = $pdo->prepare("INSERT INTO certificate_exams 
    (user_id, wpm, accuracy, special_char_accuracy, programming_accuracy, total_score, passed) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");

$stmt->execute([
    $_SESSION['user_id'],
    $wpm,
    $accuracy,
    $specialCharAccuracy,
    $programmingAccuracy,
    $totalScore,
    $passed
]);

// Return result
echo json_encode(['status' => 'success', 'passed' => $passed]);