<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if user has already passed the exam
$stmt = $pdo->prepare("SELECT * FROM certificate_exams WHERE user_id = ? AND passed = 1");
$stmt->execute([$_SESSION['user_id']]);
$existingPass = $stmt->fetch();

// Fetch exam texts from database
$stmt = $pdo->prepare("SELECT * FROM exam_texts ORDER BY section");
$stmt->execute();
$examTexts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Exam - Typing Test</title>
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

        .exam-instructions {
            background: rgba(32, 34, 37, 0.95);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(209, 208, 197, 0.1);
            color: #d1d0c5;
        }

        .exam-instructions h4 {
            color: #f0b232;
            margin-bottom: 1rem;
        }

        .exam-instructions ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .exam-instructions li {
            margin-bottom: 0.5rem;
            color: #adb5bd;
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
        <?php if ($existingPass): ?>
            <div class="exam-instructions">
                <h4>Certification Status</h4>
                <p>You have already passed the certification exam!</p>
                <a href="certificate/generate_certificate.php" class="restart-button">View Certificate</a>
            </div>
        <?php else: ?>
            <div class="exam-instructions">
                <h4>Exam Requirements:</h4>
                <ul>
                    <li>Minimum WPM: 40</li>
                    <li>Overall Accuracy: 95%</li>
                    
                </ul>
            </div>

            <div class="stats-container">
                <div class="stat-item">wpm: <span class="stat-value" id="wpm">0</span></div>
                <div class="stat-item">acc: <span class="stat-value" id="accuracy">100%</span></div>
                <div class="stat-item">time: <span class="stat-value" id="time">5:00</span></div>
                <div class="stat-item">progress: <span class="stat-value" id="progress">0%</span></div>
            </div>

            <div class="typing-area">
                <div class="words" id="exam-text"></div>
                <input type="text" class="input-field" id="user-input" disabled>
            </div>

            <div class="text-center">
                <button class="restart-button" id="start-exam">
                    <i class="fas fa-play me-2"></i>start exam
                </button>
            </div>
        <?php endif; ?>
    </div>

    <div class="results-overlay" id="resultsOverlay">
        <div class="results-card">
            <h2>Exam Complete!</h2>
            <div class="result-stat">
                WPM: <span class="result-value" id="finalWpm">0</span>
            </div>
            <div class="result-stat">
                Accuracy: <span class="result-value" id="finalAccuracy">0%</span>
            </div>
            <!-- Remove the special char and programming accuracy divs -->
            <div class="d-flex justify-content-center gap-3 mt-4">
                <button class="restart-button" onclick="location.reload()">
                    <i class="fas fa-redo me-2"></i>Try Again
                </button>
                <a href="index.php" class="restart-button">
                    <i class="fas fa-home me-2"></i>Home
                </a>
            </div>
        </div>
    </div>

    <!-- Keep your existing JavaScript with the exam-specific modifications -->
    <script>
        const examTexts = <?php echo json_encode(array_map(function($text) {
            return $text['content'];
        }, $examTexts)); ?>;

        // Single declaration of variables
        let currentText = '';
        let timer;
        let timeLeft = 300;
        let started = false;
        let currentIndex = 0;
        let mistakes = 0;
        let totalChars = 0;
        let typedText = ''; // Add this new variable

        document.getElementById('start-exam').addEventListener('click', startExam);

        function startExam() {
            document.getElementById('user-input').disabled = false;
            document.getElementById('start-exam').style.display = 'none';
            currentText = examTexts.join('\n\n');
            typedText = ''; // Reset typed text
            document.getElementById('exam-text').innerHTML = currentText.split('').map((char, i) => 
                `<span class="letter ${i === 0 ? 'current' : ''}">${char}</span>`
            ).join('');
            document.getElementById('user-input').focus();
            started = true;
            currentIndex = 0;
            mistakes = 0;
            totalChars = 0;
            startTimer();
        }

        function startTimer() {
            timer = setInterval(() => {
                timeLeft--;
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                document.getElementById('time').textContent = 
                    `${minutes}:${seconds.toString().padStart(2, '0')}`;

                if (timeLeft <= 0) {
                    endExam();
                }
            }, 1000);
        }

        function calculateStats() {
            const timeElapsed = (300 - timeLeft) / 60;
            const wpm = Math.round((currentIndex / 5) / timeElapsed) || 0;
            const accuracy = calculateAccuracy();
            const progress = Math.round((currentIndex / currentText.length) * 100);

            document.getElementById('wpm').textContent = wpm;
            document.getElementById('accuracy').textContent = `${accuracy}%`;
            document.getElementById('progress').textContent = `${progress}%`;

            return { wpm, accuracy, progress };
        }

        function calculateAccuracy() {
            if (currentIndex === 0) return 100;
            return Math.round(((currentIndex - mistakes) / currentIndex) * 100);
        }

        // Add this function to check exam requirements
        function checkExamRequirements(stats) {
            const requirements = {
                wpm: 40,
                accuracy: 95
            };

            return {
                passed: stats.wpm >= requirements.wpm && 
                        stats.accuracy >= requirements.accuracy,
                details: {
                    wpm: { value: stats.wpm, required: requirements.wpm },
                    accuracy: { value: stats.accuracy, required: requirements.accuracy }
                }
            };
        }

        // Remove calculateSectionAccuracy function as it's no longer needed

        // Modify endExam function to remove special char and programming accuracy displays
        function endExam() {
            clearInterval(timer);
            document.getElementById('user-input').disabled = true;
            const stats = calculateStats();
            const examResults = checkExamRequirements(stats);
            
            // Show results overlay
            const resultsOverlay = document.getElementById('resultsOverlay');
            resultsOverlay.style.display = 'flex';
            
            // Update final stats with appropriate styling
            const finalWpm = document.getElementById('finalWpm');
            finalWpm.textContent = stats.wpm;
            finalWpm.style.color = stats.wpm >= 40 ? '#4ade80' : '#ef4444';

            const finalAcc = document.getElementById('finalAccuracy');
            finalAcc.textContent = `${stats.accuracy}%`;
            finalAcc.style.color = stats.accuracy >= 95 ? '#4ade80' : '#ef4444';

            // Prepare data for server
            const finalData = {
                wpm: stats.wpm,
                accuracy: stats.accuracy,
                passed: examResults.passed,
                input: typedText,
                target: currentText
            };

            fetch('process_exam.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(finalData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                }
                throw new TypeError('Response was not JSON');
            })
            .then(data => {
                const buttonContainer = document.querySelector('.results-card .d-flex');
                buttonContainer.innerHTML = '';

                // Remove any existing messages
                const existingMessage = document.querySelector('.results-card .alert');
                if (existingMessage) {
                    existingMessage.remove();
                }

                if (examResults.passed) {
                    const message = document.createElement('div');
                    message.className = 'alert alert-success mb-3';
                    message.innerHTML = `
                        <h4 class="text-success mb-2">🎉 Congratulations!</h4>
                        <p>You have successfully passed the certification exam.</p>
                    `;
                    document.querySelector('.results-card h2').after(message);

                    const certificateButton = document.createElement('a');
                    certificateButton.href = 'certificate/generate_certificate.php';
                    certificateButton.className = 'restart-button';
                    certificateButton.style.borderColor = '#4ade80';
                    certificateButton.style.color = '#4ade80';
                    certificateButton.innerHTML = '<i class="fas fa-certificate me-2"></i>View Certificate';
                    buttonContainer.appendChild(certificateButton);
                } else {
                    const message = document.createElement('div');
                    message.className = 'alert alert-danger mb-3';
                    message.innerHTML = `
                        <div class="text-center">
                            <h4 class="text-danger mb-3">
                                <i class="fas fa-times-circle fa-2x mb-2"></i><br>
                                Exam Failed
                            </h4>
                            <p class="mb-3">To obtain your certificate, you need to pass all requirements.</p>
                            <div class="requirements-list text-start mx-auto" style="max-width: 300px;">
                                <p class="mb-2"><strong>Your Results:</strong></p>
                                <ul class="list-unstyled">
                    `;
                    
                    Object.entries(examResults.details).forEach(([key, detail]) => {
                        const isPassing = detail.value >= detail.required;
                        const icon = isPassing ? 
                            '<i class="fas fa-check text-success"></i>' : 
                            '<i class="fas fa-times text-danger"></i>';
                        message.innerHTML += `
                            <li class="mb-2">
                                ${icon} ${key}: 
                                <span class="${isPassing ? 'text-success' : 'text-danger'}">
                                    ${detail.value}
                                </span> 
                                (required: ${detail.required})
                            </li>
                        `;
                    });
                    
                    message.innerHTML += `
                                </ul>
                                <p class="mt-3 text-center">Please try again to achieve all requirements.</p>
                            </div>
                        </div>
                    `;
                    document.querySelector('.results-card h2').after(message);

                    const tryAgainButton = document.createElement('button');
                    tryAgainButton.className = 'restart-button';
                    tryAgainButton.style.cssText = 'border-color: #ef4444; color: #ef4444; font-weight: 600;';
                    tryAgainButton.innerHTML = '<i class="fas fa-redo me-2"></i>Take Exam Again';
                    tryAgainButton.onclick = () => location.reload();
                    buttonContainer.appendChild(tryAgainButton);
                }

                const homeButton = document.createElement('a');
                homeButton.href = 'index.php';
                homeButton.className = 'restart-button';
                homeButton.innerHTML = '<i class="fas fa-home me-2"></i>Home';
                buttonContainer.appendChild(homeButton);
            });
        }

        function calculateSectionAccuracy(sectionName) {
            if (currentIndex === 0) return 0;
            
            // Find the section text from examTexts array
            const sectionText = examTexts.find(text => text.includes(sectionName));
            if (!sectionText) return 0;

            // Find the starting position of this section in the combined text
            const sectionStart = currentText.indexOf(sectionText);
            const sectionEnd = sectionStart + sectionText.length;
            if (sectionStart === -1) return 0;

            let sectionMistakes = 0;
            let sectionChars = 0;

            // Count mistakes only within this section's range
            document.querySelectorAll('.letter').forEach((letter, index) => {
                if (index >= sectionStart && index < sectionEnd) {
                    sectionChars++;
                    if (letter.classList.contains('incorrect')) {
                        sectionMistakes++;
                    }
                }
            });

            return Math.round(((sectionChars - sectionMistakes) / sectionChars) * 100) || 0;
        }
        document.getElementById('user-input').addEventListener('input', () => {
            if (!started) return;

            const letters = document.querySelectorAll('.letter');
            const typed = document.getElementById('user-input').value;
            const current = letters[currentIndex];

            if (typed) {
                typedText += typed;
                if (typed === current.textContent) {
                    current.classList.add('correct');
                    keySound.currentTime = 0;
                    keySound.play().catch(e => console.log('Sound play failed:', e));
                } else {
                    current.classList.add('incorrect');
                    mistakes++;
                    errorSound.currentTime = 0;
                    errorSound.play().catch(e => console.log('Sound play failed:', e));
                }

                current.classList.remove('current');
                if (letters[currentIndex + 1]) {
                    letters[currentIndex + 1].classList.add('current');
                    
                    // Add auto-scrolling
                    const wordsContainer = document.querySelector('.words');
                    const nextLetter = letters[currentIndex + 1];
                    const containerHeight = wordsContainer.offsetHeight;
                    const letterTop = nextLetter.offsetTop;
                    
                    // If the next letter is below the visible area, scroll to it
                    if (letterTop > wordsContainer.scrollTop + containerHeight - 50) {
                        wordsContainer.scrollTop = letterTop - containerHeight + 50;
                    }
                }

                currentIndex++;
                calculateStats();
                document.getElementById('user-input').value = '';

                if (currentIndex >= currentText.length) {
                    endExam();
                }
            }
        });
    </script>
</body>
</html>