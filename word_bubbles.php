<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Word Bubbles - Typing Game</title>
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

        .bubble {
            position: absolute;
            background: rgba(0, 123, 255, 0.2);
            border: 2px solid rgba(0, 123, 255, 0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #fff;
            cursor: default;
            user-select: none;
            transition: transform 0.3s ease;
            animation: float 8s linear;
        }

        .bubble.active {
            background: rgba(0, 255, 136, 0.2);
            border-color: rgba(0, 255, 136, 0.5);
            transform: scale(1.1);
        }

        .bubble.pop {
            animation: pop 0.3s ease-out forwards;
        }

        .particle {
            position: absolute;
            pointer-events: none;
            background: rgba(0, 123, 255, 0.5);
            border-radius: 50%;
        }

        #score-container {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(32, 34, 37, 0.9);
            padding: 15px 25px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 1000;
        }

        #score {
            font-size: 1.5rem;
            color: #00ff88;
            font-weight: bold;
        }

        #input-field {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(32, 34, 37, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 1.2rem;
            width: 300px;
            text-align: center;
        }

        @keyframes float {
            0% { transform: translateY(110vh); }
            100% { transform: translateY(-50px); }
        }

        @keyframes pop {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.5); opacity: 0; }
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
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: none;
        }

        .game-over h2 {
            color: #00ff88;
            margin-bottom: 20px;
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
        }

        .btn-restart:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.4);
        }
    </style>
</head>
<body>
    <div id="game-container">
        <div id="score-container">
            Score: <span id="score">0</span>
        </div>
        <input type="text" id="input-field" autocomplete="off" placeholder="Type the words...">
        <div class="game-over">
            <h2>Game Over!</h2>
            <p>Final Score: <span class="final-score">0</span></p>
            <button class="btn-restart">Play Again</button>
        </div>
    </div>

    <script>
        const gameContainer = document.getElementById('game-container');
        const inputField = document.getElementById('input-field');
        const scoreElement = document.getElementById('score');
        const gameOverScreen = document.querySelector('.game-over');
        const finalScoreElement = document.querySelector('.final-score');
        const restartButton = document.querySelector('.btn-restart');

        let score = 0;
        let bubbles = [];
        let gameActive = true;
        let spawnInterval;

        const words = ['the', 'be', 'to', 'of', 'and', 'a', 'in', 'that', 'have', 'i',
                      'it', 'for', 'not', 'on', 'with', 'he', 'as', 'you', 'do', 'at',
                      'this', 'but', 'his', 'by', 'from', 'they', 'we', 'say', 'her', 'she'];

        function createBubble() {
            if (!gameActive) return;

            const bubble = document.createElement('div');
            const word = words[Math.floor(Math.random() * words.length)];
            const size = Math.random() * 40 + 60;
            const startX = Math.random() * (window.innerWidth - size);

            bubble.className = 'bubble';
            bubble.textContent = word;
            bubble.style.width = `${size}px`;
            bubble.style.height = `${size}px`;
            bubble.style.left = `${startX}px`;

            gameContainer.appendChild(bubble);
            bubbles.push({ element: bubble, word: word });

            bubble.addEventListener('animationend', () => {
                if (gameActive && bubble.parentNode === gameContainer) {
                    endGame();
                }
            });
        }

        function createParticles(x, y, color) {
            for (let i = 0; i < 10; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = `${x}px`;
                particle.style.top = `${y}px`;
                particle.style.width = '4px';
                particle.style.height = '4px';
                particle.style.background = color;

                const angle = (Math.PI * 2 * i) / 10;
                const velocity = 5;
                const vx = Math.cos(angle) * velocity;
                const vy = Math.sin(angle) * velocity;

                gameContainer.appendChild(particle);

                let posX = x;
                let posY = y;
                let opacity = 1;

                function animate() {
                    if (opacity <= 0) {
                        particle.remove();
                        return;
                    }

                    posX += vx;
                    posY += vy;
                    opacity -= 0.02;

                    particle.style.left = `${posX}px`;
                    particle.style.top = `${posY}px`;
                    particle.style.opacity = opacity;

                    requestAnimationFrame(animate);
                }

                animate();
            }
        }

        function checkInput() {
            const input = inputField.value.toLowerCase();
            const matchingBubbleIndex = bubbles.findIndex(b => b.word === input);

            if (matchingBubbleIndex !== -1) {
                const { element } = bubbles[matchingBubbleIndex];
                const rect = element.getBoundingClientRect();
                const centerX = rect.left + rect.width / 2;
                const centerY = rect.top + rect.height / 2;

                element.classList.add('pop');
                createParticles(centerX, centerY, '#00ff88');

                setTimeout(() => element.remove(), 300);
                bubbles.splice(matchingBubbleIndex, 1);
                score += 10;
                scoreElement.textContent = score;
                inputField.value = '';
            }
        }

        function endGame() {
            gameActive = false;
            clearInterval(spawnInterval);
            finalScoreElement.textContent = score;
            gameOverScreen.style.display = 'block';
        }

        function startGame() {
            gameActive = true;
            score = 0;
            scoreElement.textContent = score;
            gameOverScreen.style.display = 'none';
            inputField.value = '';
            bubbles.forEach(b => b.element.remove());
            bubbles = [];

            spawnInterval = setInterval(createBubble, 2000);
        }

        inputField.addEventListener('input', checkInput);
        restartButton.addEventListener('click', startGame);

        startGame();
    </script>
</body>
</html>