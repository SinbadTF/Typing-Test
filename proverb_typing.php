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
            background: linear-gradient(45deg, #007bff, #00ff88);
            border: none;
            color: white;
          
        }

        .btn-next:hover {
            background: linear-gradient(45deg, #007bff, #00ff88);
            border-color: transparent;
        }
        .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: rgba(30, 30, 30, 0.95);
    padding: 30px;
    border-radius: 15px;
    text-align: center;
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
    max-width: 400px;
    width: 90%;
}

.modal-content h2 {
    margin-bottom: 20px;
    color: #007bff;
}

.modal-content p {
    margin: 10px 0;
    font-size: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
}

.modal-content span {
    font-weight: bold;
    color: #007bff;
}

.modal-content .btn {
    margin-top: 20px;
}
.modal-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 25px;
}

.modal-content .btn {
    padding: 10px 25px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.modal-content .btn-primary {
    background: linear-gradient(45deg, #007bff, #00ff88);
    border: none;
    color: white;
}

.modal-content .btn-secondary {
    background: transparent;
    border: 2px solid #007bff;
    color: #fff;
}

.modal-content .btn-secondary:hover {
    background: rgba(0, 123, 255, 0.1);
}

.modal-content .btn-primary:hover {
    opacity: 0.9;
}
.proverb-text span {
    position: relative;
    font-size: 24px;
}

.proverb-text span.correct {
    color: #28a745;
}

.proverb-text span.incorrect {
    color: #dc3545;
    background-color: rgba(220, 53, 69, 0.2);
}

.proverb-text span.current {
    color: #007bff;
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
            <a href="index.php" class="btn btn-outline-primary btn-sm">Close</a>
               <!-- <button class="btn btn-control btn-restart" id="restart-btn">
                    <i class="fas fa-redo me-2"></i>Restart
                </button>-->
                <button class="btn btn-control btn-next" id="next-btn">
                    <i class="fas fa-forward me-2"></i>Next Proverb
                </button>
            </div>
        </div>
    </div>
    <!-- Add this right before the closing </body> tag -->
<!-- Update the modal content div -->
<div class="modal" id="resultsModal">
    <div class="modal-content">
        <h2>Results</h2>
        <p>WPM: <span id="finalWpm">0</span></p>
        <p>Accuracy: <span id="finalAccuracy">0%</span></p>
        <p>Time: <span id="finalTime">0:00</span></p>
        <div class="modal-buttons">
            <button class="btn btn-secondary" onclick="closeModal()">Close</button>
            <button class="btn btn-primary" onclick="nextTest()">Next Proverb</button>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    const proverbDisplay = document.getElementById('proverb-display');
    const typingInput = document.getElementById('typing-input');
    const restartBtn = document.getElementById('restart-btn');
    const nextBtn = document.getElementById('next-btn');
    const wpmDisplay = document.getElementById('wpm');
    const accuracyDisplay = document.getElementById('accuracy');
    const timeDisplay = document.getElementById('time');

    let startTime = null;
    let timerInterval;
    let currentProverb = '';

    const proverbs = [
        "Give credit where credit is due",
        "Laughter is the best medicine",
        "If life deals you lemons, make lemonade",
        "Life is just a bowl of cherries",
        "Love makes the world go round",
        "Empty vessels make the most noise",
        "Every cloud has a silver lining",
        "Every picture tells a story",
        "Everything comes to him who waits",
        "Don't bite the hand that feeds you",
        "God helps those who help themselves",
        "Good fences make good neighbours",
        "Children should be seen and not heard",
        "Good things come in small packages",
        "Failing to plan is planning to fail",
        "Feed a cold and starve a fever",
        "Fire is a good servant but a bad master",
        "First impressions are the most lasting",
        "Actions speak louder than words",
        "Better late than never",
        "Practice makes perfect",
        "Time is money"
    ];

    function getRandomProverb() {
        return proverbs[Math.floor(Math.random() * proverbs.length)];
    }

    function calculateWPM(text, timeInSeconds) {
        const words = text.length / 5;
        const minutes = timeInSeconds / 60;
        return Math.round(words / minutes);
    }

    function calculateAccuracy(typed, target) {
        let correct = 0;
        for (let i = 0; i < typed.length; i++) {
            if (typed[i] === target[i]) correct++;
        }
        return Math.round((correct / target.length) * 100);
    }

    function updateTimer() {
        const currentTime = Math.floor((Date.now() - startTime) / 1000);
        timeDisplay.textContent = (60 - currentTime) + 's';
        
        if (currentTime >= 60) {
            endTest();
        }
        return currentTime;
    }

    function startTest() {
        typingInput.value = '';
        currentProverb = getRandomProverb();
        proverbDisplay.innerHTML = currentProverb.split('').map(char => 
            `<span>${char}</span>`
        ).join('');
        typingInput.disabled = false;
        startTime = null;
        
        // Reset stats
        wpmDisplay.textContent = '0';
        accuracyDisplay.textContent = '0%';
        timeDisplay.textContent = '60s';
        
        clearInterval(timerInterval);
        typingInput.classList.remove('correct', 'incorrect');
    }
    function showResults() {
    const finalWpm = document.getElementById('finalWpm');
    const finalAccuracy = document.getElementById('finalAccuracy');
    const finalTime = document.getElementById('finalTime');
    const resultsModal = document.getElementById('resultsModal');

    finalWpm.textContent = wpmDisplay.textContent;
    finalAccuracy.textContent = accuracyDisplay.textContent;
    finalTime.textContent = timeDisplay.textContent;

    resultsModal.style.display = 'block';
}
function closeModal() {
    const resultsModal = document.getElementById('resultsModal');
    resultsModal.style.display = 'none';
}

    // Update your endTest function to show results:
function endTest() {
    clearInterval(timerInterval);
    typingInput.disabled = true;
    
    const timeTaken = (Date.now() - startTime) / 1000;
    const wpm = calculateWPM(typingInput.value, timeTaken);
    const accuracy = calculateAccuracy(typingInput.value, currentProverb);
    
    wpmDisplay.textContent = wpm;
    accuracyDisplay.textContent = accuracy + '%';
    
    // Show results modal
    showResults();
}

// Update your typingInput event listener to show results when typing is complete:
typingInput.addEventListener('input', (e) => {
    if (!startTime && typingInput.value.length === 1) {
        startTime = Date.now();
        timerInterval = setInterval(updateTimer, 1000);
    }

    const characters = proverbDisplay.querySelectorAll('span');
    const typedValue = typingInput.value;
    let allCorrect = true;

    characters.forEach((char, index) => {
        if (index < typedValue.length) {
            char.classList.remove('current');
            if (char.innerText === typedValue[index]) {
                char.classList.add('correct');
                char.classList.remove('incorrect');
            } else {
                char.classList.add('incorrect');
                char.classList.remove('correct');
                allCorrect = false;
            }
        } else if (index === typedValue.length) {
            char.classList.add('current');
            char.classList.remove('correct', 'incorrect');
        } else {
            char.classList.remove('current', 'correct', 'incorrect');
        }
    });

    // If there's an error, prevent further typing
    if (!allCorrect) {
        typingInput.value = typedValue.slice(0, -1);
        return;
    }

    const accuracy = calculateAccuracy(typingInput.value, currentProverb);
    accuracyDisplay.textContent = accuracy + '%';

    if (typingInput.value === currentProverb) {
        endTest();
    }

    // Update WPM in real-time
    if (startTime) {
        const currentTime = (Date.now() - startTime) / 1000;
        const wpm = calculateWPM(typingInput.value, currentTime);
        wpmDisplay.textContent = wpm;
    }
});

   // restartBtn.addEventListener('click', startTest);
    nextBtn.addEventListener('click', startTest);
    function nextTest() {
    // Close the modal
    closeModal();
    // Start a new test
    startTest();
}

    // Initialize the test
    startTest();
</script>
</body>
</html>