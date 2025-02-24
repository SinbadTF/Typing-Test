<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Typing Test - Game Modes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #323437;
            color: #d1d0c5;
            font-family: 'Roboto Mono', monospace;
            min-height: 100vh;
        }

        .game-modes-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .mode-card {
            background: rgba(32, 34, 37, 0.95);
            border-radius: 15px;
            padding: 25px;
            margin: 15px;
            border: 1px solid rgba(209, 208, 197, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .mode-card:hover {
            transform: translateY(-5px);
            border-color: #007bff;
            box-shadow: 0 10px 20px rgba(0, 123, 255, 0.2);
        }

        .mode-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #007bff;
        }

        .mode-title {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #fff;
        }

        .mode-description {
            color: #adb5bd;
            margin-bottom: 15px;
        }

        .mode-stats {
            display: flex;
            justify-content: space-between;
            color: #646669;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="game-modes-container">
        <h1 class="text-center mb-5">Choose Your Game Mode</h1>
        
        <div class="row">
            <div class="col-md-4">
                <div class="mode-card" onclick="window.location.href='typing_test.php?mode=time'">
                    <div class="mode-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="mode-title">Time Attack</h3>
                    <p class="mode-description">Race against the clock! Type as many words as you can in 60 seconds.</p>
                    <div class="mode-stats">
                        <span><i class="fas fa-stopwatch me-2"></i>60 seconds</span>
                        <span><i class="fas fa-star me-2"></i>Classic</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="mode-card" onclick="window.location.href='typing_test.php?mode=words'">
                    <div class="mode-icon">
                        <i class="fas fa-font"></i>
                    </div>
                    <h3 class="mode-title">Word Sprint</h3>
                    <p class="mode-description">Complete a set number of words as quickly as possible.</p>
                    <div class="mode-stats">
                        <span><i class="fas fa-list me-2"></i>25 words</span>
                        <span><i class="fas fa-tachometer-alt me-2"></i>Speed</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="mode-card" onclick="window.location.href='typing_test.php?mode=zen'">
                    <div class="mode-icon">
                        <i class="fas fa-infinity"></i>
                    </div>
                    <h3 class="mode-title">Zen Mode</h3>
                    <p class="mode-description">Practice without pressure. No timer, just pure typing practice.</p>
                    <div class="mode-stats">
                        <span><i class="fas fa-peace me-2"></i>Relaxed</span>
                        <span><i class="fas fa-feather me-2"></i>No Pressure</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>