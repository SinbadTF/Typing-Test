<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Typing Games - Boku no Typing</title>
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

        .games-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .game-card {
            background: rgba(32, 34, 37, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(209, 208, 197, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .game-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #007bff, #00ff88);
            transform: scaleX(0);
            transition: transform 0.5s ease;
        }

        .game-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .game-card:hover::before {
            transform: scaleX(1);
        }

        .game-icon {
            font-size: 2.5rem;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: float 3s ease-in-out infinite;
        }

        .game-title {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #fff;
        }

        .game-description {
            color: #adb5bd;
            margin-bottom: 20px;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .game-stats {
            display: flex;
            justify-content: space-between;
            color: #646669;
            font-size: 0.9rem;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-play {
            background: linear-gradient(45deg, #007bff, #00ff88);
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 10px;
            color: #fff;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-play:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,123,255,0.3);
            color: #fff;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 3rem;
            text-align: center;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .difficulty-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.1);
        }

        .difficulty-badge.beginner {
            color: #28a745;
            background: rgba(40, 167, 69, 0.1);
        }

        .difficulty-badge.intermediate {
            color: #ffc107;
            background: rgba(255, 193, 7, 0.1);
        }

        .difficulty-badge.advanced {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
        }

        .premium-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #f0b232;
            background: rgba(240, 178, 50, 0.1);
            border: 1px solid rgba(240, 178, 50, 0.3);
        }

        .premium-game {
            border: 1px solid rgba(240, 178, 50, 0.3);
        }

        .premium-game:hover {
            border-color: rgba(240, 178, 50, 0.5);
            box-shadow: 0 10px 30px rgba(240, 178, 50, 0.2);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="games-container">
        
        
        
        <h1 class="section-title">Typing Games</h1>

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="game-card">
                    <div class="game-icon">
                        <i class="fas fa-water"></i>
                    </div>
                    <div class="difficulty-badge beginner">Beginner Friendly</div>
                    <h2 class="game-title">Word Bubbles</h2>
                    <p class="game-description">Pop floating word bubbles by typing them correctly. Watch out as they float up the screen! Perfect for improving your typing speed and accuracy while having fun.</p>
                    <div class="game-stats">
                        <span><i class="fas fa-trophy"></i> High Score: 2,450</span>
                        <span><i class="fas fa-users"></i> 120 Playing</span>
                    </div>
                    <a href="word_bubbles.php" class="btn-play">Play Now <i class="fas fa-play ms-2"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="game-card premium-game">
                    <div class="game-icon">
                        <i class="fas fa-car-side"></i>
                    </div>
                    <div class="difficulty-badge intermediate">Intermediate</div>
                    <div class="premium-badge"><i class="fas fa-crown"></i> Premium</div>
                    <h2 class="game-title">Type Racer</h2>
                    <p class="game-description">Race against time by typing accurately to move your car forward. The faster and more accurate you type, the faster your car goes!</p>
                    <div class="game-stats">
                        <span><i class="fas fa-trophy"></i> High Score: 3,120</span>
                        <span><i class="fas fa-users"></i> 85 Playing</span>
                    </div>
                    <a href="type_racer.php" class="btn-play">Play Now <i class="fas fa-play ms-2"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="game-card">
                    <div class="game-icon">
                        <i class="fas fa-font"></i>
                    </div>
                    <div class="difficulty-badge advanced">Advanced</div>
                    <h2 class="game-title">Falling Letters</h2>
                    <p class="game-description">Type the falling letters before they hit the ground! A fast-paced arcade-style game that helps improve your reaction time and typing accuracy.</p>
                    <div class="game-stats">
                        <span><i class="fas fa-trophy"></i> High Score: 4,890</span>
                        <span><i class="fas fa-users"></i> 95 Playing</span>
                    </div>
                    <a href="falling_letters.php" class="btn-play">Play Now <i class="fas fa-play ms-2"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="game-card">
                    <div class="game-icon">
                        <i class="fas fa-keyboard"></i>
                    </div>
                    <div class="difficulty-badge beginner">Beginner Friendly</div>
                    <h2 class="game-title">Typing Defense</h2>
                    <p class="game-description">Defend your base by typing words to eliminate incoming threats. A strategic typing game that combines defense mechanics with typing skills.</p>
                    <div class="game-stats">
                        <span><i class="fas fa-trophy"></i> High Score: 1,850</span>
                        <span><i class="fas fa-users"></i> 75 Playing</span>
                    </div>
                    <a href="typing_defense.php" class="btn-play">Play Now <i class="fas fa-play ms-2"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="game-card premium-game">
                    <div class="game-icon">
                        <i class="fas fa-puzzle-piece"></i>
                    </div>
                    <div class="difficulty-badge intermediate">Intermediate</div>
                    <div class="premium-badge"><i class="fas fa-crown"></i> Premium</div>
                    <h2 class="game-title">Word Puzzle</h2>
                    <p class="game-description">Solve word puzzles by typing the correct answers. Combines vocabulary enhancement with typing practice in an engaging puzzle format.</p>
                    <div class="game-stats">
                        <span><i class="fas fa-trophy"></i> High Score: 2,750</span>
                        <span><i class="fas fa-users"></i> 60 Playing</span>
                    </div>
                    <a href="word_puzzle.php" class="btn-play">Play Now <i class="fas fa-play ms-2"></i></a>
                </div>
            </div>

            

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="game-card premium-game">
                    <div class="game-icon">
                        <i class="fas fa-wand-magic-sparkles"></i>
                    </div>
                    <div class="difficulty-badge advanced">Advanced</div>
                    <div class="premium-badge"><i class="fas fa-crown"></i> Premium</div>
                    <h2 class="game-title">Typing Battle</h2>
                    <p class="game-description">Cast spells by typing them correctly to defend against waves of enemies. An epic typing adventure that combines RPG elements with typing skills!</p>
                    <div class="game-stats">
                        <span><i class="fas fa-trophy"></i> High Score: 6,720</span>
                        <span><i class="fas fa-users"></i> 145 Playing</span>
                    </div>
                    <a href="typing_battle.php" class="btn-play">Play Now <i class="fas fa-play ms-2"></i></a>
                </div>
            </div>

          
                    
            </div>
        </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>