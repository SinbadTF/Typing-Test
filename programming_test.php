<?php
session_start();
require_once 'config/database.php';

// Fetch random programming code snippet
$stmt = $pdo->prepare("SELECT content, language FROM programming_snippets ORDER BY RAND() LIMIT 1");
$stmt->execute();
$code = $stmt->fetch();
$typingText = $code['content'] ?? "function helloWorld() {\n    console.log('Hello, World!');\n}";
$language = $code['language'] ?? "javascript";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programming Code Typing Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Use the exact same styles from typing_test.php */
        body {
            background-color: #323437;
            color: #d1d0c5;
            font-family: 'Roboto Mono', monospace;
            min-height: 100vh;
        }

        /* Add all styles from typing_test.php */
        /* Add programming-specific styles */
        .typing-area {
            background: #1e1e1e; /* VS Code-like dark theme */
            font-family: 'Roboto Mono', monospace;
            padding: 30px;
            line-height: 1.6;
        }

        /* Add only these additional styles for code formatting */
        .words {
            white-space: pre;
            tab-size: 4;
        }

        .language-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            background: rgba(97, 175, 239, 0.2);
            color: #61afef;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        .typing-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            position: relative;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 0 10px;
        }

        .stats-container {
            display: flex;
            gap: 20px;
            color: #646669;
            font-size: 1.2rem;
        }

        .stat-value {
            color: #d1d0c5;
        }

        .typing-area {
            background: #2c2e31;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 20px;
            position: relative;
            font-size: 1.5rem;
            line-height: 1.6;
        }

        .words {
            color: #646669;
            letter-spacing: 1px;
            white-space: pre-wrap;
        }

        .letter {
            transition: color 0.1s;
        }

        .letter.current {
            color: #d1d0c5;
            animation: blink 1s infinite;
        }

        .letter.correct {
            color: #d1d0c5;
        }

        .letter.incorrect {
            color: #ca4754;
        }

        .input-field {
            opacity: 0;
            position: absolute;
        }

        .restart-button {
            background: linear-gradient(to right, #2c7fd7, #2cb67d);
            color: #d1d0c5;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            margin: 0 5px;
            transition: opacity 0.2s;
        }

        .restart-button:hover {
            opacity: 0.8;
            color: #d1d0c5;
        }

        @keyframes blink {
            50% {
                border-left-color: transparent;
            }
        }

        .sound-toggle {
            background: none;
            border: none;
            color: #646669;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 5px;
        }

        .sound-toggle:hover {
            color: #d1d0c5;
        }
        .results-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: flex-start;
            padding-top: 100px;
            z-index: 1000;
        }

        .results-card {
            background: #2c2e31;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            min-width: 300px;
        }

        .result-stat {
            margin: 15px 0;
            font-size: 1.2rem;
            color: #646669;
        }

        .result-value {
            color: #d1d0c5;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <audio id="keySound" preload="auto">
        <source src="assets/sounds/key-press.mp3" type="audio/mpeg">
    </audio>
    <audio id="errorSound" preload="auto">
        <source src="assets/sounds/error.mp3" type="audio/mpeg">
    </audio>

    <div class="typing-container">
        <div class="header-section">
            <div class="stats-container">
                <div class="stat-item">wpm: <span class="stat-value" id="wpm">0</span></div>
                <div class="stat-item">acc: <span class="stat-value" id="accuracy">100%</span></div>
                <div class="stat-item">time: <span class="stat-value" id="time">60</span></div>
            </div>
            <button class="sound-toggle" id="soundToggle">
                <i class="fas fa-volume-up"></i>
            </button>
        </div>

        <div class="results-overlay" id="resultsOverlay">
            <div class="results-card">
                <h2>Code Complete!</h2>
                <div class="result-stat">
                    WPM: <span class="result-value" id="finalWpm">0</span>
                </div>
                <div class="result-stat">
                    Accuracy: <span class="result-value" id="finalAccuracy">0%</span>
                </div>
                <div class="result-stat">
                    Errors: <span class="result-value" id="finalErrors">0</span>
                </div>
                <div class="result-stat">
                    Time: <span class="result-value" id="finalTime">0s</span>
                </div>
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button class="restart-button" onclick="initTest()">
                        <i class="fas fa-redo me-2"></i>Try Again
                    </button>
                    <a href="premium_course.php" class="restart-button">
                        <i class="fas fa-home me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>

        <div class="typing-area">
            <div class="language-indicator"><?php echo htmlspecialchars($language); ?></div>
            <div class="words" id="words"></div>
            <input type="text" class="input-field" id="input-field" autofocus>
        </div>

        <div class="text-center">
            <a href="premium_course.php" class="restart-button">
                <i class="fas fa-home me-2"></i>Back
            </a>
            <button class="restart-button" id="restart-button">
                <i class="fas fa-redo me-2"></i>New Code
            </button>
        </div>
    </div>

    <script>
                // Add after the initTest function
                function handleTyping(event) {
            const inputField = document.getElementById('input-field');
            if (!inputField) return;
            
            if (event.key === 'Tab') {
                event.preventDefault();
                return;
            }

            const letters = document.querySelectorAll('.letter');
            const currentLetter = letters[currentLetterIndex];
            
            if (!currentLetter) return;

            if (!startTime && event.key.length === 1) {
                startTime = new Date();
                isTyping = true;
                startTimer();
            }

            // Handle Enter key for new lines
            if (event.key === 'Enter' && currentLetter.textContent === '\n') {
                currentLetter.classList.remove('current');
                currentLetter.classList.add('correct');
                keySound.currentTime = 0;
                keySound.play().catch(() => {});
                
                if (currentLetterIndex < letters.length - 1) {
                    letters[currentLetterIndex + 1].classList.add('current');
                    currentLetterIndex++;
                } else {
                    finishTest();
                }
                
                totalCharacters++;
                updateStats();
                return;
            }

            if (event.key === 'Backspace') {
                if (currentLetterIndex > 0) {
                    letters[currentLetterIndex].classList.remove('current');
                    currentLetterIndex--;
                    letters[currentLetterIndex].classList.remove('correct', 'incorrect');
                    letters[currentLetterIndex].classList.add('current');
                }
                return;
            }

            if (event.key.length === 1) {
                currentLetter.classList.remove('current');
                if (event.key === currentLetter.textContent) {
                    currentLetter.classList.add('correct');
                    keySound.currentTime = 0;
                    keySound.play().catch(() => {});
                } else {
                    currentLetter.classList.add('incorrect');
                    mistakes++;
                    errorSound.currentTime = 0;
                    errorSound.play().catch(() => {});
                }

                if (currentLetterIndex < letters.length - 1) {
                    letters[currentLetterIndex + 1].classList.add('current');
                    currentLetterIndex++;
                } else {
                    finishTest();
                }

                totalCharacters++;
                updateStats();
            }
        }

        // Add event listener for typing
        document.addEventListener('keydown', handleTyping);

        // Add sound toggle functionality
        const soundToggle = document.getElementById('soundToggle');
        const keySound = document.getElementById('keySound');
        const errorSound = document.getElementById('errorSound');
        let soundEnabled = true;

        soundToggle.addEventListener('click', () => {
            soundEnabled = !soundEnabled;
            soundToggle.innerHTML = soundEnabled ? 
                '<i class="fas fa-volume-up"></i>' : 
                '<i class="fas fa-volume-mute"></i>';
            keySound.muted = !soundEnabled;
            errorSound.muted = !soundEnabled;
        });
        const typingText = <?php echo json_encode($typingText); ?>;
        let currentText = typingText;

        async function getRandomText() {
            try {
                const response = await fetch('get_programming_text.php');
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();
                if (data.success) {
                    document.querySelector('.language-indicator').textContent = data.language;
                    return data.content;
                }
                throw new Error(data.error || 'Failed to fetch code');
            } catch (error) {
                console.error('Error:', error);
                return currentText;
            }
        }

        // Copy all other JavaScript functions from typing_test.php
        // ... rest of the JavaScript code ...
        // Add this JavaScript code after your existing script tag
        let currentLetterIndex = 0;
        let mistakes = 0;
        let totalCharacters = 0;
        let startTime = null;
        let isTyping = false;
    
        function initTest() {
            const wordsContainer = document.getElementById('words');
            wordsContainer.innerHTML = Array.from(currentText).map(char => 
                `<span class="letter">${char}</span>`
            ).join('');
    
            currentLetterIndex = 0;
            mistakes = 0;
            totalCharacters = 0;
            startTime = null;
            isTyping = false;
    
            const letters = document.querySelectorAll('.letter');
            if (letters.length > 0) {
                letters[0].classList.add('current');
            }
    
            document.getElementById('wpm').textContent = '0';
            document.getElementById('accuracy').textContent = '100%';
            document.getElementById('time').textContent = '60';
        }
    
        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', () => {
            initTest();
            document.getElementById('restart-button').addEventListener('click', async () => {
                currentText = await getRandomText();
                initTest();
            });
        });
                // Add after handleTyping function
                function startTimer() {
            const timerDisplay = document.getElementById('time');
            let timeLeft = 60;

            function updateTimer() {
                if (!startTime || !isTyping) return;
                
                timeLeft--;
                if (timeLeft <= 0) {
                    finishTest();
                    return;
                }
                
                timerDisplay.textContent = timeLeft;
                setTimeout(updateTimer, 1000);
            }
            
            setTimeout(updateTimer, 1000);
        }

        function updateStats() {
            if (!startTime || !isTyping) return;
            
            const timeElapsed = (new Date() - startTime) / 1000 / 60; // Convert to minutes
            const wpm = Math.round((totalCharacters / 5) / timeElapsed);
            const accuracy = Math.round(((totalCharacters - mistakes) / totalCharacters) * 100) || 100;
            
            document.getElementById('wpm').textContent = wpm;
            document.getElementById('accuracy').textContent = `${accuracy}%`;
        }

        function finishTest() {
            isTyping = false;
            const timeElapsed = (new Date() - startTime) / 1000;
            const wpm = Math.round((totalCharacters / 5) / (timeElapsed / 60));
            const accuracy = Math.round(((totalCharacters - mistakes) / totalCharacters) * 100) || 100;
            
            document.getElementById('finalWpm').textContent = wpm;
            document.getElementById('finalAccuracy').textContent = `${accuracy}%`;
            document.getElementById('finalErrors').textContent = mistakes;
            document.getElementById('finalTime').textContent = `${timeElapsed.toFixed(1)}s`;
            
            // Load new code snippet after a short delay
            setTimeout(async () => {
                currentText = await getRandomText();
                initTest();
            }, 1500);
        }
    </script>
</body>
</html>