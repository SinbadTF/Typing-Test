<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Typing Battle - RPG Typing Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #323437;
            color: #d1d0c5;
            font-family: 'Roboto Mono', monospace;
            margin: 0;
            overflow: hidden;
        }

        .game-container {
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .battle-area {
            width: 100%;
            max-width: 1000px;
            height: 500px;
            background: rgba(32, 34, 37, 0.95);
            border-radius: 15px;
            position: relative;
            margin-bottom: 20px;
            border: 1px solid rgba(209, 208, 197, 0.1);
            overflow: hidden;
        }

        .player {
            position: absolute;
            bottom: 50px;
            left: 100px;
            width: 100px;
            height: 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .player-icon {
            font-size: 3rem;
            color: #00ff88;
            margin-bottom: 10px;
        }

        .player-health {
            width: 100px;
            height: 10px;
            background: #444;
            border-radius: 5px;
            overflow: hidden;
        }

        .health-bar {
            width: 100%;
            height: 100%;
            background: #00ff88;
            transition: width 0.3s ease;
        }

        .enemy {
            position: absolute;
            top: 50%;
            right: 100px;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .enemy-icon {
            font-size: 4rem;
            color: #ff4444;
            margin-bottom: 10px;
        }

        .enemy-health {
            width: 150px;
            height: 10px;
            background: #444;
            border-radius: 5px;
            overflow: hidden;
        }

        .spell-area {
            width: 100%;
            max-width: 600px;
            background: rgba(32, 34, 37, 0.95);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
        }

        .spell-word {
            font-size: 2rem;
            color: #007bff;
            margin-bottom: 15px;
            min-height: 48px;
        }

        .spell-input {
            width: 300px;
            background: rgba(25, 25, 25, 0.8);
            border: 2px solid #646669;
            border-radius: 10px;
            color: #d1d0c5;
            font-size: 1.5rem;
            padding: 10px 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .spell-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.3);
        }

        .stats {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(32, 34, 37, 0.95);
            padding: 15px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-item {
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .effect {
            position: absolute;
            pointer-events: none;
            font-size: 2rem;
            animation: effectFloat 1s ease-out forwards;
        }

        @keyframes effectFloat {
            0% { transform: translateY(0); opacity: 1; }
            100% { transform: translateY(-50px); opacity: 0; }
        }

        .game-over {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(32, 34, 37, 0.95);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            display: none;
            z-index: 100;
        }

        .btn-restart {
            background: linear-gradient(45deg, #007bff, #00ff88);
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            color: #fff;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .btn-restart:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.4);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="game-container">
        <div class="battle-area">
            <div class="player">
                <div class="player-icon">
                    <i class="fas fa-user-ninja"></i>
                </div>
                <div class="player-health">
                    <div class="health-bar" id="player-health-bar"></div>
                </div>
            </div>
            <div class="enemy">
                <div class="enemy-icon">
                    <i class="fas fa-dragon"></i>
                </div>
                <div class="enemy-health">
                    <div class="health-bar" id="enemy-health-bar"></div>
                </div>
            </div>
        </div>

        <div class="spell-area">
            <div class="spell-word" id="spell-word"></div>
            <input type="text" class="spell-input" id="spell-input" placeholder="Type the spell..." autocomplete="off">
        </div>

        <div class="stats">
            <div class="stat-item">Level: <span id="level">1</span></div>
            <div class="stat-item">Score: <span id="score">0</span></div>
            <div class="stat-item">Enemies Defeated: <span id="enemies">0</span></div>
        </div>

        <div class="game-over">
            <h2>Game Over!</h2>
            <p>Final Score: <span id="final-score">0</span></p>
            <p>Enemies Defeated: <span id="final-enemies">0</span></p>
            <p>Highest Level: <span id="final-level">1</span></p>
            <button class="btn-restart" id="restart-button">Play Again</button>
        </div>
    </div>

    <script>
        const spells = [
            { word: 'fireball', damage: 20, color: '#ff4444' },
            { word: 'lightning', damage: 25, color: '#ffeb3b' },
            { word: 'frostbolt', damage: 15, color: '#40c4ff' },
            { word: 'heal', damage: -20, color: '#00ff88' },
            { word: 'poison', damage: 10, color: '#7e57c2' }
        ];

        const gameState = {
            playerHealth: 100,
            enemyHealth: 100,
            score: 0,
            level: 1,
            enemiesDefeated: 0,
            gameActive: true
        };

        const elements = {
            spellWord: document.getElementById('spell-word'),
            spellInput: document.getElementById('spell-input'),
            playerHealthBar: document.getElementById('player-health-bar'),
            enemyHealthBar: document.getElementById('enemy-health-bar'),
            levelDisplay: document.getElementById('level'),
            scoreDisplay: document.getElementById('score'),
            enemiesDisplay: document.getElementById('enemies'),
            gameOverScreen: document.querySelector('.game-over'),
            finalScore: document.getElementById('final-score'),
            finalEnemies: document.getElementById('final-enemies'),
            finalLevel: document.getElementById('final-level'),
            restartButton: document.getElementById('restart-button'),
            battleArea: document.querySelector('.battle-area')
        };

        function updateDisplays() {
            elements.playerHealthBar.style.width = `${gameState.playerHealth}%`;
            elements.enemyHealthBar.style.width = `${gameState.enemyHealth}%`;
            elements.levelDisplay.textContent = gameState.level;
            elements.scoreDisplay.textContent = gameState.score;
            elements.enemiesDisplay.textContent = gameState.enemiesDefeated;
        }

        function getRandomSpell() {
            return spells[Math.floor(Math.random() * spells.length)];
        }

        function createEffect(text, color, x, y) {
            const effect = document.createElement('div');
            effect.className = 'effect';
            effect.textContent = text;
            effect.style.color = color;
            effect.style.left = `${x}px`;
            effect.style.top = `${y}px`;
            elements.battleArea.appendChild(effect);

            setTimeout(() => effect.remove(), 1000);
        }

        function castSpell(spell) {
            if (spell.word === 'heal') {
                gameState.playerHealth = Math.min(100, gameState.playerHealth - spell.damage);
                createEffect(`+${-spell.damage}`, spell.color, 150, 300);
            } else {
                gameState.enemyHealth -= spell.damage;
                createEffect(`-${spell.damage}`, spell.color, 700, 200);
            }

            if (gameState.enemyHealth <= 0) {
                gameState.enemiesDefeated++;
                gameState.score += 100 * gameState.level;
                gameState.level++;
                gameState.enemyHealth = 100 + (gameState.level - 1) * 20;
            }

            updateDisplays();
            newSpell();
        }

        function enemyAttack() {
            if (!gameState.gameActive) return;
            
            const damage = 10 + Math.floor(gameState.level / 2);
            gameState.playerHealth -= damage;
            createEffect(`-${damage}`, '#ff4444', 150, 300);

            if (gameState.playerHealth <= 0) {
                endGame();
            } else {
                updateDisplays();
            }
        }

        function newSpell() {
            const spell = getRandomSpell();
            elements.spellWord.textContent = spell.word;
            elements.spellWord.style.color = spell.color;
        }

        function endGame() {
            gameState.gameActive = false;
            elements.gameOverScreen.style.display = 'block';
            elements.finalScore.textContent = gameState.score;
            elements.finalEnemies.textContent = gameState.enemiesDefeated;
            elements.finalLevel.textContent = gameState.level;
        }

        function startGame() {
            Object.assign(gameState, {
                playerHealth: 100,
                enemyHealth: 100,
                score: 0,
                level: 1,
                enemiesDefeated: 0,
                gameActive: true
            });

            elements.gameOverScreen.style.display = 'none';
            elements.spellInput.value = '';
            elements.spellInput.focus();
            updateDisplays();
            newSpell();
        }

        elements.spellInput.addEventListener('input', () => {
            if (!gameState.gameActive) return;

            const typed = elements.spellInput.value.toLowerCase();
            const currentSpell = elements.spellWord.textContent;

            if (typed === currentSpell) {
                const spell = spells.find(s => s.word === currentSpell);
                castSpell(spell);
                elements.spellInput.value = '';
            }
        });

        elements.restartButton.addEventListener('click', startGame);

        setInterval(() => {
            if (gameState.gameActive) enemyAttack();
        }, 2000);

        startGame();
    </script>
</body>
</html>