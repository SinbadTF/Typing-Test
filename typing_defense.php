<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Typing Defense - Tower Defense Typing Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #323437;
            color: #d1d0c5;
            font-family: 'Roboto Mono', monospace;
            min-height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .game-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            position: relative;
        }

        .game-area {
            background: rgba(32, 34, 37, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(209, 208, 197, 0.1);
            position: relative;
            height: 600px;
            overflow: hidden;
        }

        .game-stats {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 10;
            background: rgba(25, 25, 25, 0.9);
            padding: 15px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-item {
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .stat-value {
            color: #007bff;
            font-weight: 600;
        }

        .base {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, #007bff, #00ff88);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            box-shadow: 0 0 30px rgba(0, 123, 255, 0.5);
        }

        .enemy {
            position: absolute;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: moveDown 10s linear;
        }

        .enemy-icon {
            font-size: 2rem;
            color: #dc3545;
            margin-bottom: 5px;
        }

        .enemy-word {
            background: rgba(25, 25, 25, 0.8);
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 1rem;
        }

        .input-area {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 300px;
            text-align: center;
            z-index: 10;
        }

        .word-input {
            background: rgba(25, 25, 25, 0.8);
            border: 2px solid #646669;
            border-radius: 10px;
            color: #d1d0c5;
            font-size: 1.2rem;
            padding: 10px 20px;
            width: 100%;
            text-align: center;
            transition: all 0.3s ease;
        }

        .word-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.3);
        }

        @keyframes moveDown {
            from { top: -50px; }
            to { top: calc(100% + 50px); }
        }

        .explosion {
            position: absolute;
            font-size: 3rem;
            color: #ffc107;
            animation: explode 0.5s ease-out forwards;
            pointer-events: none;
        }

        @keyframes explode {
            0% { transform: scale(0.5); opacity: 1; }
            100% { transform: scale(2); opacity: 0; }
        }

        .game-over {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(25, 25, 25, 0.95);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            display: none;
            z-index: 100;
        }

        .game-over h2 {
            color: #dc3545;
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .restart-btn {
            background: linear-gradient(45deg, #007bff, #00ff88);
            border: none;
            padding: 10px 30px;
            border-radius: 10px;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .restart-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="game-container">
        <div class="game-area" id="gameArea">
            <div class="game-stats">
                <div class="stat-item">
                    Score: <span class="stat-value" id="score">0</span>
                </div>
                <div class="stat-item">
                    Lives: <span class="stat-value" id="lives">3</span>
                </div>
                <div class="stat-item">
                    Level: <span class="stat-value" id="level">1</span>
                </div>
            </div>

            <div class="base">
                <i class="fas fa-shield-alt"></i>
            </div>

            <div class="input-area">
                <input type="text" class="word-input" id="wordInput" placeholder="Type to defend..." autocomplete="off">
            </div>
        </div>

        <div class="game-over" id="gameOver">
            <h2>Game Over!</h2>
            <p>Final Score: <span id="finalScore">0</span></p>
            <p>Level Reached: <span id="finalLevel">1</span></p>
            <button class="restart-btn" onclick="restartGame()">Play Again</button>
        </div>
    </div>

    <script>
        const words = [
            'attack', 'defend', 'shield', 'power', 'energy',
            'castle', 'tower', 'battle', 'warrior', 'knight',
            'magic', 'spell', 'sword', 'armor', 'dragon',
            'quest', 'victory', 'defeat', 'health', 'mana'
        ];

        let gameState = {
            score: 0,
            lives: 3,
            level: 1,
            activeEnemies: [],
            spawnInterval: 3000,
            isGameOver: false
        };

        function createEnemy() {
            if (gameState.isGameOver) return;

            const enemy = document.createElement('div');
            enemy.className = 'enemy';
            enemy.style.left = Math.random() * (gameArea.offsetWidth - 100) + 'px';

            const word = words[Math.floor(Math.random() * words.length)];
            enemy.innerHTML = `
                <div class="enemy-icon">👾</div>
                <div class="enemy-word">${word}</div>
            `;
            enemy.dataset.word = word;

            gameArea.appendChild(enemy);
            gameState.activeEnemies.push(enemy);

            enemy.addEventListener('animationend', () => {
                if (gameArea.contains(enemy)) {
                    gameArea.removeChild(enemy);
                    gameState.lives--;
                    updateStats();
                    if (gameState.lives <= 0) endGame();
                }
            });
        }

        function updateStats() {
            document.getElementById('score').textContent = gameState.score;
            document.getElementById('lives').textContent = gameState.lives;
            document.getElementById('level').textContent = gameState.level;
        }

        function createExplosion(x, y) {
            const explosion = document.createElement('div');
            explosion.className = 'explosion';
            explosion.style.left = x + 'px';
            explosion.style.top = y + 'px';
            explosion.innerHTML = '💥';
            gameArea.appendChild(explosion);

            setTimeout(() => gameArea.removeChild(explosion), 500);
        }

        function endGame() {
            gameState.isGameOver = true;
            document.getElementById('finalScore').textContent = gameState.score;
            document.getElementById('finalLevel').textContent = gameState.level;
            document.getElementById('gameOver').style.display = 'block';
            document.getElementById('wordInput').disabled = true;
        }

        function restartGame() {
            gameState = {
                score: 0,
                lives: 3,
                level: 1,
                activeEnemies: [],
                spawnInterval: 3000,
                isGameOver: false
            };

            gameArea.querySelectorAll('.enemy').forEach(enemy => enemy.remove());
            document.getElementById('gameOver').style.display = 'none';
            document.getElementById('wordInput').disabled = false;
            document.getElementById('wordInput').value = '';
            document.getElementById('wordInput').focus();
            updateStats();

            startGame();
        }

        const gameArea = document.getElementById('gameArea');
        const wordInput = document.getElementById('wordInput');

        wordInput.addEventListener('input', (e) => {
            const input = e.target.value.toLowerCase();
            const enemies = document.querySelectorAll('.enemy');

            enemies.forEach(enemy => {
                if (enemy.dataset.word === input) {
                    const rect = enemy.getBoundingClientRect();
                    createExplosion(rect.left - gameArea.getBoundingClientRect().left, rect.top - gameArea.getBoundingClientRect().top);
                    enemy.remove();
                    gameState.score += 10 * gameState.level;
                    updateStats();
                    e.target.value = '';

                    if (gameState.score >= gameState.level * 100) {
                        gameState.level++;
                        gameState.spawnInterval = Math.max(1000, gameState.spawnInterval - 300);
                        updateStats();
                    }
                }
            });
        });

        function startGame() {
            setInterval(createEnemy, gameState.spawnInterval);
        }

        startGame();
        wordInput.focus();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>