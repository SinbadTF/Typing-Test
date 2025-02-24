<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Falling Letters - Typing Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #323437;
            color: #d1d0c5;
            margin: 0;
            overflow: hidden;
            font-family: 'Roboto Mono', monospace;
        }

        #game-container {
            width: 100vw;
            height: 100vh;
            position: relative;
        }

        .letter {
            position: absolute;
            font-size: 24px;
            color: #fff;
            text-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
            animation: glow 1.5s ease-in-out infinite alternate;
            transition: all 0.3s ease;
        }

        .letter.hit {
            transform: scale(1.5);
            color: #00ff88;
            text-shadow: 0 0 20px rgba(0, 255, 136, 0.8);
            animation: hit 0.3s ease-out forwards;
        }

        .letter.missed {
            color: #ff4444;
            text-shadow: 0 0 20px rgba(255, 68, 68, 0.8);
            animation: missed 0.3s ease-out forwards;
        }

        #score {
            position: fixed;
            top: 20px;
            right: 20px;
            font-size: 24px;
            color: #00ff88;
            text-shadow: 0 0 10px rgba(0, 255, 136, 0.5);
            z-index: 100;
        }

        #health {
            position: fixed;
            top: 60px;
            right: 20px;
            font-size: 24px;
            color: #ff4444;
            text-shadow: 0 0 10px rgba(255, 68, 68, 0.5);
            z-index: 100;
        }

        .particle {
            position: absolute;
            pointer-events: none;
            animation: particle 1s ease-out forwards;
        }

        @keyframes glow {
            from { opacity: 0.8; }
            to { opacity: 1; }
        }

        @keyframes hit {
            0% { transform: scale(1); }
            50% { transform: scale(1.5); }
            100% { transform: scale(0); opacity: 0; }
        }

        @keyframes missed {
            0% { transform: scale(1); }
            100% { transform: scale(0); opacity: 0; }
        }

        @keyframes particle {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(0); opacity: 0; }
        }

        #game-over {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(32, 34, 37, 0.95);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            z-index: 1000;
            border: 2px solid #00ff88;
            box-shadow: 0 0 30px rgba(0, 255, 136, 0.3);
        }

        .btn-restart {
            background: linear-gradient(45deg, #007bff, #00ff88);
            border: none;
            padding: 12px 30px;
            font-size: 18px;
            border-radius: 10px;
            color: #fff;
            margin-top: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-restart:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 255, 136, 0.4);
        }
    </style>
</head>
<body>
    <div id="game-container">
        <div id="score">Score: <span id="score-value">0</span></div>
        <div id="health">Health: <span id="health-value">100</span></div>
    </div>

    <div id="game-over">
        <h2>Game Over!</h2>
        <p>Final Score: <span id="final-score">0</span></p>
        <button class="btn-restart" onclick="restartGame()">Play Again</button>
    </div>

    <script>
        let score = 0;
        let health = 100;
        let gameActive = true;
        let letters = [];
        const container = document.getElementById('game-container');
        const scoreValue = document.getElementById('score-value');
        const healthValue = document.getElementById('health-value');
        const gameOver = document.getElementById('game-over');
        const finalScore = document.getElementById('final-score');

        function createLetter() {
            if (!gameActive) return;

            const letter = document.createElement('div');
            const char = String.fromCharCode(65 + Math.floor(Math.random() * 26));
            letter.textContent = char;
            letter.className = 'letter';
            letter.style.left = Math.random() * (window.innerWidth - 30) + 'px';
            letter.style.top = '-30px';
            container.appendChild(letter);

            const speed = 1 + Math.random() * 2;
            letters.push({ element: letter, char: char, speed: speed });
        }

        function createParticle(x, y, color) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = x + 'px';
            particle.style.top = y + 'px';
            particle.style.backgroundColor = color;
            particle.style.width = '4px';
            particle.style.height = '4px';
            container.appendChild(particle);

            setTimeout(() => particle.remove(), 1000);
        }

        function createParticleExplosion(x, y, color) {
            for (let i = 0; i < 10; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = (x + (Math.random() - 0.5) * 40) + 'px';
                particle.style.top = (y + (Math.random() - 0.5) * 40) + 'px';
                particle.style.backgroundColor = color;
                particle.style.width = '4px';
                particle.style.height = '4px';
                container.appendChild(particle);

                setTimeout(() => particle.remove(), 1000);
            }
        }

        function updateGame() {
            if (!gameActive) return;

            letters.forEach((letterObj, index) => {
                const { element, speed } = letterObj;
                const top = parseFloat(element.style.top) + speed;
                element.style.top = top + 'px';

                if (top > window.innerHeight) {
                    element.remove();
                    letters.splice(index, 1);
                    health -= 10;
                    healthValue.textContent = health;

                    if (health <= 0) {
                        endGame();
                    }
                }
            });

            requestAnimationFrame(updateGame);
        }

        function endGame() {
            gameActive = false;
            gameOver.style.display = 'block';
            finalScore.textContent = score;
        }

        function restartGame() {
            letters.forEach(letterObj => letterObj.element.remove());
            letters = [];
            score = 0;
            health = 100;
            scoreValue.textContent = score;
            healthValue.textContent = health;
            gameOver.style.display = 'none';
            gameActive = true;
            updateGame();
        }

        document.addEventListener('keypress', (e) => {
            if (!gameActive) return;

            const key = e.key.toUpperCase();
            const letterIndex = letters.findIndex(letterObj => letterObj.char === key);

            if (letterIndex !== -1) {
                const letterObj = letters[letterIndex];
                const rect = letterObj.element.getBoundingClientRect();
                createParticleExplosion(rect.left, rect.top, '#00ff88');
                letterObj.element.classList.add('hit');
                setTimeout(() => letterObj.element.remove(), 300);
                letters.splice(letterIndex, 1);
                score += 10;
                scoreValue.textContent = score;
            }
        });

        setInterval(createLetter, 1000);
        updateGame();
    </script>
</body>
</html>