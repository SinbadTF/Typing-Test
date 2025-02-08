<?php
require_once 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user's test history
// Update the SQL query to use the correct table name
$stmt = $pdo->prepare("
    SELECT 
        wpm,
        accuracy,
        mistakes,
        time_taken,
        created_at as test_date
    FROM typing_results
    WHERE user_id = ? 
    ORDER BY created_at DESC"
);
$stmt->execute([$_SESSION['user_id']]);
$testHistory = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test History - TypeMaster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #ffffff;
            min-height: 100vh;
            padding: 40px 0;
        }
        .history-section {
            padding: 60px;
            background: rgba(45, 45, 45, 0.98);
            border-radius: 25px;
            margin: 40px auto;
            max-width: 1000px;
            box-shadow: 0 0 40px rgba(0,123,255,0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }
        .table {
            color: #ffffff;
            background: rgba(35, 35, 35, 0.7);
            border-radius: 15px;
            overflow: hidden;
            margin-top: 30px;
        }
        .table th {
            background: rgba(0, 123, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.1);
            font-weight: 600;
            padding: 20px;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #adb5bd;
        }
        .table td {
            border-color: rgba(255, 255, 255, 0.1);
            padding: 20px;
            font-size: 1.1rem;
            vertical-align: middle;
        }
        .wpm-cell {
            font-weight: 600;
            color: #00ff88;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .accuracy-cell {
            font-weight: 600;
            color: #00ff88;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .date-cell {
            color: #adb5bd;
        }
        .table tbody tr:hover {
            background: rgba(0, 123, 255, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.2);
        }
        .table tbody tr {
            transition: all 0.3s ease;
        }
        .table tbody tr:hover {
            background: rgba(0, 123, 255, 0.1);
            transform: translateY(-2px);
        }
        .wpm-cell {
            font-weight: 600;
            color: #00ff88;
        }
        .accuracy-cell {
            font-weight: 600;
            color: #007bff;
        }
        .date-cell {
            font-weight: 600;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .empty-message {
            padding: 40px;
            font-size: 1.2rem;
            color: #adb5bd;
            font-style: italic;
        }
        .history-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #007bff, #00ff88);
        }
        .history-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 2rem;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }
        .table {
            color: #ffffff;
            background: rgba(45, 45, 45, 0.5);
            border-radius: 15px;
            overflow: hidden;
        }
        .table th {
            background: rgba(0, 123, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.1);
            font-weight: 600;
        }
        .table td {
            border-color: rgba(255, 255, 255, 0.1);
        }
        .btn-primary {
            background: linear-gradient(45deg, #007bff, #00ff88);
            border: none;
            padding: 15px 40px;
            font-size: 1.2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.4s ease;
        }
        .btn-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,123,255,0.4);
        }
        .mistakes-cell {
    font-weight: 600;
    color: #dc3545;
    background: linear-gradient(45deg, #dc3545, #ff6b6b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="history-section">
            <h1 class="history-title">Your Typing Test History</h1>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar me-2"></i>Date</th>
                            <th><i class="fas fa-tachometer-alt me-2"></i>Speed</th>
                            <th><i class="fas fa-bullseye me-2"></i>Accuracy</th>
                            <th><i class="fas fa-times-circle me-2"></i>Mistakes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($testHistory as $test): ?>
                            <tr>
                                <td class="date-cell">
                                    <?php echo date('M d, Y H:i', strtotime($test['test_date'])); ?>
                                </td>
                                <td class="wpm-cell">
                                    <?php echo number_format($test['wpm'], 1); ?> WPM
                                </td>
                                <td class="accuracy-cell">
                                    <?php echo number_format($test['accuracy'], 1); ?>%
                                </td>
                                <td class="mistakes-cell">
                                    <?php echo $test['mistakes']; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($testHistory)): ?>
                            <tr>
                                <td colspan="4" class="text-center empty-message">
                                    <i class="fas fa-info-circle me-2"></i>No test history available yet
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-4">
                <a href="profile.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Profile
                </a>
            </div>
        </div>
    </div>
</body>
</html>