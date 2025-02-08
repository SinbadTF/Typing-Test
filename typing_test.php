<?php
session_start();
require_once 'config/database.php';


$stmt = $pdo->prepare("SELECT content FROM typing_texts ORDER BY RAND() LIMIT 1");
$stmt->execute();
$text = $stmt->fetch();
$typingText = $text['content'] ?? "The quick brown fox jumps over the lazy dog.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Typing Test</title>
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

        .typing-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
        }

        .stats-container {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
            color: #646669;
        }

        .stat-item {
            font-size: 1.5rem;
        }

        .stat-value {
            color: #d1d0c5;
            font-weight: 600;
        }

        .typing-area {
            position: relative;
            font-size: 1.5rem;
            line-height: 1.5;
            height: 200px;
            margin-bottom: 2rem;
            padding: 20px;
            background: rgba(32, 34, 37, 0.95);
            border-radius: 10px;
            border: 1px solid rgba(209, 208, 197, 0.1);
        }

        .words {
            user-select: none;
            height: 120px;
            overflow-y: auto;
            padding: 10px;
            margin-bottom: 1rem;
            scrollbar-width: thin;
            scrollbar-color: #646669 #323437;
        }

        .words::-webkit-scrollbar {
            width: 8px;
        }

        .words::-webkit-scrollbar-track {
            background: #323437;
            border-radius: 4px;
        }

        .words::-webkit-scrollbar-thumb {
            background: #646669;
            border-radius: 4px;
        }

        .letter {
            position: relative;
            color: #646669;
        }

        .letter.correct {
            color: #d1d0c5;
        }

        .letter.incorrect {
            color: #ca4754;
            background: rgba(202, 71, 84, 0.2);
            border-radius: 2px;
        }

        .letter.current {
            color: #d1d0c5;
        }

        .letter.current::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 3px;
            background-color: #d1d0c5;
            animation: blink 1s infinite;
        }

        .restart-button {
            background: none;
            border: 1px solid #646669;
            color: #646669;
            font-size: 1rem;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .restart-button:hover {
            color: #d1d0c5;
            border-color: #d1d0c5;
            background: rgba(209, 208, 197, 0.1);
        }

        .input-field {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .results-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(32, 34, 37, 0.95);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .results-card {
            background: #2c2e31;
            padding: 2rem;
            border-radius: 10px;
            border: 1px solid rgba(209, 208, 197, 0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }

        .result-stat {
            margin: 1rem 0;
            font-size: 1.5rem;
        }

        .result-value {
            color: #d1d0c5;
            font-weight: 600;
            font-size: 2rem;
        }

        @keyframes blink {
            50% { opacity: 0; }
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
        <div class="stats-container">
            <div class="stat-item">wpm: <span class="stat-value" id="wpm">0</span></div>
            <div class="stat-item">acc: <span class="stat-value" id="accuracy">100%</span></div>
            <div class="stat-item">time: <span class="stat-value" id="time">60</span></div>
        </div>

        <div class="typing-area">
            <div class="words" id="words"></div>
            <input type="text" class="input-field" id="input-field" autofocus>
        </div>

        <div class="text-center">
            <button class="restart-button" id="restart-button">
                <i class="fas fa-redo me-2"></i>restart test
            </button>
        </div>
    </div>

    <div class="results-overlay" id="resultsOverlay">
        <div class="results-card">
            <h2>Test Complete!</h2>
            <div class="result-stat">
                WPM: <span class="result-value" id="finalWpm">0</span>
            </div>
            <div class="result-stat">
                Accuracy: <span class="result-value" id="finalAccuracy">0%</span>
            </div>
            <div class="result-stat">
                Time: <span class="result-value" id="finalTime">0s</span>
            </div>
            <div class="d-flex justify-content-center gap-3 mt-4">
                <button class="restart-button" onclick="initTest()">
                    <i class="fas fa-redo me-2"></i>Try Again
                </button>
                <a href="index.php" class="restart-button">
                    <i class="fas fa-home me-2"></i>Home
                </a>
            </div>
        </div>
    </div>

    <script>
        const typingText = <?php echo json_encode($typingText); ?>;
        const words = document.getElementById('words');
        const input = document.getElementById('input-field');
        const wpmDisplay = document.getElementById('wpm');
        const accuracyDisplay = document.getElementById('accuracy');
        const timeDisplay = document.getElementById('time');
        const restartButton = document.getElementById('restart-button');
        const resultsOverlay = document.getElementById('resultsOverlay');
        const keySound = document.getElementById('keySound');
        const errorSound = document.getElementById('errorSound');

        let currentIndex = 0;
        let mistakes = 0;
        let isTyping = false;
        let timeLeft = 60;
        let timer;
        let startTime;
        let totalChars = 0;

        // Add this function after the script starts
        let currentText = typingText;  // Store initial text

        async function getRandomText() {
            try {
                const response = await fetch('get_random_text.php');
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();
                if (data.success) {
                    return data.content;
                }
                throw new Error(data.error || 'Failed to fetch text');
            } catch (error) {
                console.error('Error:', error);
                return currentText; // Fallback to current text if fetch fails
            }
        }
        
        async function initTest() {
            const newText = await getRandomText();
            currentText = newText;
            
            resultsOverlay.style.display = 'none';
            words.innerHTML = currentText.split('').map((char, i) => 
                `<span class="letter ${i === 0 ? 'current' : ''}">${char}</span>`
            ).join('');
            
            currentIndex = 0;
            mistakes = 0;
            isTyping = false;
            timeLeft = 60;
            totalChars = 0;
            
            clearInterval(timer);
            timeDisplay.textContent = timeLeft;
            wpmDisplay.textContent = '0';
            accuracyDisplay.textContent = '100%';
            
            input.value = '';
            input.focus();
        }

        input.addEventListener('input', () => {
            if (!isTyping) {
                startTime = Date.now();
                isTyping = true;
                startTimer();
            }
        
            const letters = words.querySelectorAll('.letter');
            const typed = input.value;
            const current = letters[currentIndex];
        
            if (typed) {
                totalChars++;
                if (typed === letters[currentIndex].textContent) {
                    current.classList.add('correct');
                    keySound.currentTime = 0;
                    keySound.play().catch(e => console.log('Sound play failed:', e));
                } else {
                    mistakes++;
                    current.classList.add('incorrect');
                    errorSound.currentTime = 0;
                    errorSound.play().catch(e => console.log('Sound play failed:', e));
                    
                    // End test if mistake on last character
                    if (currentIndex === currentText.length - 1) {
                        endTest();
                        return;
                    }
                }
        
                current.classList.remove('current');
                if (letters[currentIndex + 1]) {
                    letters[currentIndex + 1].classList.add('current');
                }
        
                currentIndex++;
                updateStats();
                input.value = '';
        
                // Check if reached end of text
                if (currentIndex >= currentText.length) {
                    endTest();
                }
            }
        });

        function startTimer() {
            timer = setInterval(() => {
                timeLeft--;
                timeDisplay.textContent = timeLeft;
                updateStats();

                if (timeLeft <= 0) {
                    endTest();
                }
            }, 1000);
        }

        function updateStats() {
            const timeElapsed = (Date.now() - startTime) / 1000 / 60;
            const wpm = Math.round((currentIndex / 5) / timeElapsed);
            const accuracy = Math.round(((totalChars - mistakes) / totalChars) * 100) || 100;

            wpmDisplay.textContent = wpm;
            accuracyDisplay.textContent = accuracy + '%';
        }

        function endTest() {
            clearInterval(timer);
            input.blur();
            isTyping = false;
            
            const finalWpm = parseInt(wpmDisplay.textContent);
            const finalAccuracy = parseInt(accuracyDisplay.textContent);
            const timeUsed = 60 - timeLeft;
            
            document.getElementById('finalWpm').textContent = finalWpm;
            document.getElementById('finalAccuracy').textContent = finalAccuracy + '%';
            document.getElementById('finalTime').textContent = timeUsed + 's';
            resultsOverlay.style.display = 'flex';
            
            fetch('save_result.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    wpm: finalWpm,
                    accuracy: finalAccuracy,
                    mistakes: mistakes,
                    time_taken: timeUsed
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Failed to save result');
                }
            })
            .catch(error => {
                console.error('Error saving result:', error);
            });
        }

        restartButton.addEventListener('click', async () => {
            const newText = await getRandomText();
            currentText = newText;
            initTest();
        });

        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                initTest();
            }
        });

        initTest();
    </script>
</body>
</html>