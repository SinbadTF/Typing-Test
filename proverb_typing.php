<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proverb Typing - Improve Your Typing Skills</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #ffffff;
            min-height: 100vh;
            padding-bottom: 50px;
        }
        
        .typing-container {
            background: rgba(25, 25, 25, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin: 50px auto;
            max-width: 800px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .proverb-text {
            font-size: 24px;
            margin-bottom: 20px;
            line-height: 1.6;
            color: #adb5bd;
        }

        .typing-input {
            width: 100%;
            padding: 15px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.3);
            color: #fff;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .typing-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 10px rgba(0,123,255,0.3);
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-box {
            background: rgba(0, 0, 0, 0.3);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-label {
            font-size: 14px;
            color: #adb5bd;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }

        .controls {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn-control {
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-restart {
            background: linear-gradient(45deg, #007bff, #00ff88);
            border: none;
            color: white;
        }

        .btn-next {
            background: transparent;
            border: 2px solid #007bff;
            color: #fff;
        }

        .btn-next:hover {
            background: linear-gradient(45deg, #007bff, #00ff88);
            border-color: transparent;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <div class="typing-container">
            <div class="stats-container">
                <div class="stat-box">
                    <div class="stat-label">WPM</div>
                    <div class="stat-value" id="wpm">0</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Accuracy</div>
                    <div class="stat-value" id="accuracy">0%</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Time</div>
                    <div class="stat-value" id="time">60s</div>
                </div>
            </div>

            <div class="proverb-text" id="proverb-display">
                Actions speak louder than words.
            </div>

            <textarea 
                class="typing-input" 
                id="typing-input" 
                placeholder="Start typing here..."
                rows="3"
            ></textarea>

            <div class="controls">
                <button class="btn btn-control btn-restart" id="restart-btn">
                    <i class="fas fa-redo me-2"></i>Restart
                </button>
                <button class="btn btn-control btn-next" id="next-btn">
                    <i class="fas fa-forward me-2"></i>Next Proverb
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Basic typing test functionality
        const proverbDisplay = document.getElementById('proverb-display');
        const typingInput = document.getElementById('typing-input');
        const restartBtn = document.getElementById('restart-btn');
        const nextBtn = document.getElementById('next-btn');
        const wpmDisplay = document.getElementById('wpm');
        const accuracyDisplay = document.getElementById('accuracy');
        const timeDisplay = document.getElementById('time');

        let startTime, endTime;
        let timerInterval;
        let timeLeft = 60;

        const proverbs = [
            "Actions speak louder than words.",
            "All's well that ends well.",
            "Better late than never.",
            "Practice makes perfect.",
            "Where there's a will there's a way."
            // Add more proverbs as needed
        ];

        function getRandomProverb() {
            return proverbs[Math.floor(Math.random() * proverbs.length)];
        }

        function startTest() {
            typingInput.value = '';
            proverbDisplay.textContent = getRandomProverb();
            timeLeft = 60;
            startTime = new Date().getTime();
            typingInput.disabled = false;
            updateTimer();
            
            clearInterval(timerInterval);
            timerInterval = setInterval(updateTimer, 1000);
        }

        function updateTimer() {
            timeLeft--;
            timeDisplay.textContent = timeLeft + 's';
            
            if (timeLeft <= 0) {
                endTest();
            }
        }

        function calculateWPM(timeTaken, textLength) {
            const minutes = timeTaken / 60000; // Convert milliseconds to minutes
            const words = textLength / 5; // Approximate word count
            return Math.round(words / minutes);
        }

        function calculateAccuracy(original, typed) {
            let correct = 0;
            const total = original.length;
            
            for (let i = 0; i < typed.length && i < total; i++) {
                if (typed[i] === original[i]) correct++;
            }
            
            return Math.round((correct / total) * 100);
        }

        function endTest() {
            clearInterval(timerInterval);
            typingInput.disabled = true;
            endTime = new Date().getTime();
            
            const timeTaken = endTime - startTime;
            const wpm = calculateWPM(timeTaken, typingInput.value.length);
            const accuracy = calculateAccuracy(proverbDisplay.textContent, typingInput.value);
            
            wpmDisplay.textContent = wpm;
            accuracyDisplay.textContent = accuracy + '%';
        }

        typingInput.addEventListener('input', () => {
            if (!startTime) {
                startTest();
            }
            
            if (typingInput.value === proverbDisplay.textContent) {
                endTest();
            }
        });

        restartBtn.addEventListener('click', startTest);
        nextBtn.addEventListener('click', startTest);

        // Initialize the test
        startTest();
    </script>
</body>
</html>
