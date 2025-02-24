<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Attack - Typing Game</title>
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

        .game-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }

        .game-area {
            background: rgba(32, 34, 37, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(209, 208, 197, 0.1);
        }

        .timer {
            font-size: 3rem;
            text-align: center;
            margin-bottom: 20px;
            color: #dc3545;
            font-weight: 700;
            text-shadow: 0 0 10px rgba(220, 53, 69, 0.3);
        }

        .word-display {
            font-size: 2rem;
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .input-area {
            text-align: center;
            margin-bottom: 30px;
        }

        .word-input {
            background: rgba(25, 25, 25, 0.8);
            border: 2px solid #646669;
            border-radius: 10px;
            color: #d1d0c5;
            font-size: 1.5rem;
            padding: 10px 20px;
            text-align: center;
            width: 300px;
            transition: all 0.3s ease;
        }

        .word-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.3);
        }

        .game-stats {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 30px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 600;
            color: #007bff;
        }

        .stat-label {
            color: #adb5bd;
            font-size: 0.9rem;
        }

        .next-words {
            text-align: center;
            color: #646669;
            font-size: 1.2rem;
            margin-top: 20px;
            min-height: 40px;
        }

        @keyframes correct {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        @keyframes incorrect {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        .correct {
            animation: correct 0.3s ease;
            color: #28a745 !important;
        }

        .incorrect {
            animation: incorrect 0.3s ease;
            color: #dc3545 !important;
        }

        .combo-indicator {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 5rem;
            color: #ffc107;
            opacity: 0;
            pointer-events: none;
            text-shadow: 0 0 20px rgba(255, 193, 7, 0.5);
            transition: all 0.3s ease;
        }

        .combo-indicator.show {
            opacity: 1;
            transform: translate(-50%, -100%);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="game-container">
        <div class="game-area">
            <div class="timer" id="timer">60</div>
            
            <div class="game-stats">
                <div class="stat-item">
                    <div class="stat-value" id="score">0</div>
                    <div class="stat-label">Score</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="wpm">0</div>
                    <div class="stat-label">WPM</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="combo">0x</div>
                    <div class="stat-label">Combo</div>
                </div>
            </div>

            <div class="word-display" id="currentWord"></div>
            <div class="input-area">
                <input type="text" class="word-input" id="wordInput" placeholder="Type the word..." autocomplete="off">
            </div>
            <div class="next-words" id="nextWords"></div>
        </div>
    </div>

    <div class="combo-indicator" id="comboIndicator">COMBO!</div>

    <script>
        const words = [
            'the', 'be', 'to', 'of', 'and', 'a', 'in', 'that', 'have', 'I',
            'it', 'for', 'not', 'on', 'with', 'he', 'as', 'you', 'do', 'at',
            'this', 'but', 'his', 'by', 'from', 'they', 'we', 'say', 'her', 'she',
            'or', 'an', 'will', 'my', 'one', 'all', 'would', 'there', 'their', 'what',
            'so', 'up', 'out', 'if', 'about', 'who', 'get', 'which', 'go', 'me'
        ];

        let currentWordQueue = [];
        let score = 0;
        let combo = 0;
        let timeLeft = 60;
        let wordsTyped = 0;
        let gameInterval;
        let startTime;

        function getRandomWord() {
            return words[Math.floor(Math.random() * words.length)];
        }

        function updateWordQueue() {
            while (currentWordQueue.length < 3) {
                currentWordQueue.push(getRandomWord());
            }
            document.getElementById('currentWord').textContent = currentWordQueue[0];
            document.getElementById('nextWords').textContent = currentWordQueue.slice(1).join(' → ');
        }

        function updateStats() {
            document.getElementById('score').textContent = score;
            document.getElementById('combo').textContent = combo + 'x';
            const elapsedMinutes = (Date.now() - startTime) / 60000;
            const wpm = Math.round(wordsTyped / elapsedMinutes);
            document.getElementById('wpm').textContent = wpm;
        }

        function showComboIndicator() {
            const indicator = document.getElementById('comboIndicator');
            indicator.textContent = combo + 'x COMBO!';
            indicator.classList.add('show');
            setTimeout(() => indicator.classList.remove('show'), 500);
        }

        function startTimer() {
            startTime = Date.now();
            gameInterval = setInterval(() => {
                timeLeft--;
                document.getElementById('timer').textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(gameInterval);
                    endGame();
                }
            }, 1000);
        }

        function endGame() {
            document.getElementById('wordInput').disabled = true;
            const wpm = Math.round(wordsTyped / ((60 - timeLeft) / 60));
            alert(`Game Over!\nFinal Score: ${score}\nWords Per Minute: ${wpm}\nMax Combo: ${combo}`);
        }

        document.getElementById('wordInput').addEventListener('input', (e) => {
            const input = e.target.value;
            const currentWord = currentWordQueue[0];

            if (input === currentWord) {
                e.target.value = '';
                combo++;
                score += 10 * combo;
                wordsTyped++;
                
                if (combo > 1) showComboIndicator();
                
                document.getElementById('currentWord').classList.add('correct');
                setTimeout(() => {
                    document.getElementById('currentWord').classList.remove('correct');
                    currentWordQueue.shift();
                    updateWordQueue();
                    updateStats();
                }, 100);
            } else if (input && !currentWord.startsWith(input)) {
                combo = 0;
                document.getElementById('currentWord').classList.add('incorrect');
                setTimeout(() => {
                    document.getElementById('currentWord').classList.remove('incorrect');
                }, 100);
                updateStats();
            }
        });

        // Start the game
        updateWordQueue();
        startTimer();
        document.getElementById('wordInput').focus();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>