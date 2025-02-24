<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Word Puzzle - Typing Game</title>
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

        .puzzle-area {
            background: rgba(32, 34, 37, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(209, 208, 197, 0.1);
        }

        .scrambled-word {
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
            letter-spacing: 5px;
        }

        .word-hint {
            text-align: center;
            color: #adb5bd;
            margin-bottom: 30px;
            font-style: italic;
        }

        .input-area {
            text-align: center;
            margin-bottom: 30px;
        }

        .answer-input {
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

        .answer-input:focus {
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

        .timer {
            font-size: 2rem;
            text-align: center;
            margin-bottom: 20px;
            color: #28a745;
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
            animation: correct 0.5s ease;
            color: #28a745 !important;
        }

        .incorrect {
            animation: incorrect 0.5s ease;
            color: #dc3545 !important;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="game-container">
        <div class="puzzle-area">
            <div class="timer" id="timer">60</div>
            
            <div class="game-stats">
                <div class="stat-item">
                    <div class="stat-value" id="score">0</div>
                    <div class="stat-label">Score</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="solved">0</div>
                    <div class="stat-label">Words Solved</div>
                </div>
            </div>

            <div class="scrambled-word" id="scrambledWord"></div>
            <div class="word-hint" id="wordHint"></div>

            <div class="input-area">
                <input type="text" class="answer-input" id="answerInput" placeholder="Type your answer..." autocomplete="off">
            </div>
        </div>
    </div>

    <script>
        const words = [
            { word: 'KEYBOARD', hint: 'You type on this' },
            { word: 'COMPUTER', hint: 'Electronic device for processing data' },
            { word: 'ALGORITHM', hint: 'Step-by-step procedure for calculations' },
            { word: 'PROGRAMMING', hint: 'Writing code for software' },
            { word: 'DATABASE', hint: 'Organized collection of data' },
            { word: 'INTERFACE', hint: 'Point of interaction between components' },
            { word: 'NETWORK', hint: 'Connected computers sharing resources' },
            { word: 'SOFTWARE', hint: 'Programs and operating information' },
            { word: 'INTERNET', hint: 'Global computer network' },
            { word: 'SECURITY', hint: 'Protection against threats' }
        ];

        let currentWord = '';
        let score = 0;
        let solved = 0;
        let timeLeft = 60;
        let gameInterval;

        function scrambleWord(word) {
            let scrambled = word.split('');
            for (let i = scrambled.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [scrambled[i], scrambled[j]] = [scrambled[j], scrambled[i]];
            }
            return scrambled.join('');
        }

        function newWord() {
            const randomIndex = Math.floor(Math.random() * words.length);
            currentWord = words[randomIndex].word;
            document.getElementById('scrambledWord').textContent = scrambleWord(currentWord);
            document.getElementById('wordHint').textContent = words[randomIndex].hint;
            document.getElementById('answerInput').value = '';
        }

        function updateStats() {
            document.getElementById('score').textContent = score;
            document.getElementById('solved').textContent = solved;
        }

        function startTimer() {
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
            document.getElementById('answerInput').disabled = true;
            alert(`Game Over!\nFinal Score: ${score}\nWords Solved: ${solved}`);
        }

        document.getElementById('answerInput').addEventListener('input', (e) => {
            const answer = e.target.value.toUpperCase();
            if (answer === currentWord) {
                score += 100;
                solved++;
                updateStats();
                document.getElementById('scrambledWord').classList.add('correct');
                setTimeout(() => {
                    document.getElementById('scrambledWord').classList.remove('correct');
                    newWord();
                }, 500);
            }
        });

        // Start the game
        newWord();
        startTimer();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>