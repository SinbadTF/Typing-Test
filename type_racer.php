<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Type Racer - Typing Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #323437;
            color: #d1d0c5;
            margin: 0;
            font-family: 'Roboto Mono', monospace;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .game-container {
            width: 100%;
            max-width: 1000px;
            background: rgba(32, 34, 37, 0.95);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            position: relative;
        }

        .race-track {
            background: #1a1b1e;
            height: 300px;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .lane {
            height: 100px;
            border-bottom: 2px dashed rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .car {
            position: absolute;
            left: 0;
            width: 60px;
            height: 40px;
            transition: left 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .car i {
            font-size: 2rem;
            color: #007bff;
        }

        .car.player i {
            color: #00ff88;
        }

        .typing-area {
            background: rgba(26, 27, 30, 0.9);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .word-display {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #adb5bd;
            text-align: center;
        }

        .input-field {
            width: 100%;
            background: rgba(38, 40, 43, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 10px 15px;
            font-size: 1.2rem;
            border-radius: 8px;
            text-align: center;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
            padding: 15px;
            background: rgba(26, 27, 30, 0.9);
            border-radius: 10px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-label {
            color: #adb5bd;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 1.2rem;
            color: #00ff88;
            font-weight: bold;
        }

        .finish-line {
            position: absolute;
            right: 10px;
            top: 0;
            bottom: 0;
            width: 5px;
            background: repeating-linear-gradient(
                45deg,
                #000,
                #000 10px,
                #ff0 10px,
                #ff0 20px
            );
        }

        .game-over {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(32, 34, 37, 0.95);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: none;
            z-index: 1000;
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
            margin-top: 15px;
        }

        .btn-restart:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.4);
        }
    </style>
</head>
<body>
    <div class="game-container">
        <div class="race-track">
            <div class="finish-line"></div>
            <div class="lane">
                <div class="car player">
                    <i class="fas fa-car-side"></i>
                </div>
            </div>
            <div class="lane">
                <div class="car ai">
                    <i class="fas fa-car-side"></i>
                </div>
            </div>
            <div class="lane">
                <div class="car ai">
                    <i class="fas fa-car-side"></i>
                </div>
            </div>
        </div>

        <div class="typing-area">
            <div class="word-display">Loading...</div>
            <input type="text" class="input-field" placeholder="Type here..." autocomplete="off">
        </div>

        <div class="stats">
            <div class="stat-item">
                <div class="stat-label">WPM</div>
                <div class="stat-value" id="wpm">0</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Accuracy</div>
                <div class="stat-value" id="accuracy">100%</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Progress</div>
                <div class="stat-value" id="progress">0%</div>
            </div>
        </div>

        <div class="game-over">
            <h2>Race Complete!</h2>
            <p>Your Time: <span class="final-time">0:00</span></p>
            <p>Position: <span class="final-position">1st</span></p>
            <button class="btn-restart">Race Again</button>
        </div>
    </div>

    <script>
        const words = ['the', 'be', 'to', 'of', 'and', 'a', 'in', 'that', 'have', 'I',
                      'it', 'for', 'not', 'on', 'with', 'he', 'as', 'you', 'do', 'at'];
        const wordDisplay = document.querySelector('.word-display');
        const inputField = document.querySelector('.input-field');
        const playerCar = document.querySelector('.car.player');
        const aiCars = document.querySelectorAll('.car.ai');
        const gameOverScreen = document.querySelector('.game-over');
        const restartButton = document.querySelector('.btn-restart');
        const wpmElement = document.getElementById('wpm');
        const accuracyElement = document.getElementById('accuracy');
        const progressElement = document.getElementById('progress');

        let currentWord = '';
        let wordsTyped = 0;
        let startTime;
        let gameActive = false;
        let mistakes = 0;
        let totalCharacters = 0;

        function getRandomWord() {
            return words[Math.floor(Math.random() * words.length)];
        }

        function updateWord() {
            currentWord = getRandomWord();
            wordDisplay.textContent = currentWord;
        }

        function calculateProgress() {
            const progress = (wordsTyped / 20) * 100;
            return Math.min(progress, 100);
        }

        function updateCarPositions() {
            const progress = calculateProgress();
            playerCar.style.left = `${progress}%`;
            progressElement.textContent = `${Math.round(progress)}%`;

            if (progress >= 100) {
                endGame();
            }
        }

        function moveAICars() {
            aiCars.forEach(car => {
                const currentLeft = parseFloat(car.style.left) || 0;
                const increment = Math.random() * 2;
                const newLeft = Math.min(currentLeft + increment, 100);
                car.style.left = `${newLeft}%`;
            });
        }

        function calculateWPM() {
            const timeElapsed = (Date.now() - startTime) / 1000 / 60;
            const wpm = Math.round((wordsTyped / timeElapsed) || 0);
            wpmElement.textContent = wpm;
        }

        function calculateAccuracy() {
            const accuracy = Math.round(((totalCharacters - mistakes) / totalCharacters) * 100) || 100;
            accuracyElement.textContent = `${accuracy}%`;
        }

        function endGame() {
            gameActive = false;
            const timeElapsed = ((Date.now() - startTime) / 1000).toFixed(2);
            const minutes = Math.floor(timeElapsed / 60);
            const seconds = Math.floor(timeElapsed % 60);
            document.querySelector('.final-time').textContent = 
                `${minutes}:${seconds.toString().padStart(2, '0')}`;

            let position = 1;
            aiCars.forEach(car => {
                if (parseFloat(car.style.left) > parseFloat(playerCar.style.left)) {
                    position++;
                }
            });

            document.querySelector('.final-position').textContent = 
                position === 1 ? '1st' : position === 2 ? '2nd' : '3rd';

            gameOverScreen.style.display = 'block';
            clearInterval(aiInterval);
        }

        function startGame() {
            gameActive = true;
            wordsTyped = 0;
            mistakes = 0;
            totalCharacters = 0;
            startTime = Date.now();
            gameOverScreen.style.display = 'none';
            playerCar.style.left = '0';
            aiCars.forEach(car => car.style.left = '0');
            updateWord();
            inputField.value = '';
            inputField.focus();

            if (aiInterval) clearInterval(aiInterval);
            aiInterval = setInterval(() => {
                if (gameActive) {
                    moveAICars();
                    calculateWPM();
                }
            }, 100);
        }

        let aiInterval;

        inputField.addEventListener('input', () => {
            if (!gameActive) return;

            const typed = inputField.value;
            if (typed === currentWord) {
                wordsTyped++;
                totalCharacters += currentWord.length;
                updateCarPositions();
                calculateAccuracy();
                updateWord();
                inputField.value = '';
            } else if (!currentWord.startsWith(typed)) {
                mistakes++;
                calculateAccuracy();
            }
        });

        restartButton.addEventListener('click', startGame);

        startGame();
    </script>
</body>
</html>