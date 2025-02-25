<?php
session_start();
require_once 'config/database.php';



// Check if story ID is provided
if (!isset($_GET['story']) || !is_numeric($_GET['story'])) {
    header('Location: story_mode.php');
    exit();
}

// Fetch story from database
$stmt = $pdo->prepare("SELECT * FROM stories WHERE id = ?");
$stmt->execute([$_GET['story']]);
$story = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$story) {
    header('Location: story_mode.php');
    exit();
}

// Get next story
$stmt = $pdo->prepare("SELECT id FROM stories WHERE id > ? ORDER BY id ASC LIMIT 1");
$stmt->execute([$_GET['story']]);
$nextStory = $stmt->fetch(PDO::FETCH_ASSOC);

require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($story['title']); ?> - Story Practice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .typing-container {
            max-width: 1400px;
            margin: 50px auto;
            padding: 30px;
            background: rgba(25, 25, 25, 0.95);
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }

        .header-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .stats-container {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin-bottom: 20px;
        }

        .stat-item {
            margin: 0 15px;
            color: #adb5bd;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stat-value {
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }

        .stat-value:hover {
            transform: scale(1.1);
        }

        .story-header {
            margin-top: 20px;
            font-size: 2.5rem;
            font-weight: 700;
          
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }

        .story-header h2 {
            font-size: 2.5rem;
         
            color: #d1d0c5;
        }

        .words {
            font-size: 1.6rem;
            line-height: 2;
            color: #adb5bd;
            margin: 20px auto;
            white-space: pre-wrap;
            min-height: 250px;
            max-height: 350px;
            overflow-y: auto;
            padding: 30px 40px;
            border-radius: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            word-wrap: break-word;
            word-break: normal;
            overflow-wrap: break-word;
            text-align: left;
            background: rgba(0, 0, 0, 0.2);
            width: 95%;
            max-width: 1300px;
        }

        .letter {
            position: relative;
            color: #646669;
            display: inline-block;
            white-space: pre;
            font-size: 1.6rem;
        }

        .letter.space {
            white-space: pre-wrap;
        }

        .letter.newline {
            display: block;
            height: 1.8em;
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

        .input-field {
            width: 100%;
            padding: 15px;
            font-size: 1.1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            margin-bottom: 20px;
        }

        .input-field:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }

        .keyboard {
            /* Keep the keyboard layout from lesson.php */
        }

        .restart-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 8px;
            cursor: pointer;
        }

        .restart-button:hover {
            background-color: #0056b3;
        }

        .restart-button i {
            margin-right: 5px;
        }

        .restart-button.ms-3 {
            margin-left: 10px;
        }

        .restart-button.ms-3:hover {
            background-color: #0056b3;
        }

        .typing-box-container {
            margin-top: 30px;
            margin-bottom: 30px;
            padding: 0 20px;
        }

        .typing-box {
            width: 100%;
            padding: 20px;
            font-size: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #d1d0c5;
            transition: all 0.3s ease;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: normal;
        }

        .outside-buttons {
            max-width: 600px;
            margin: 20px auto;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .outside-button {
            font-size: 1rem;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            min-width: 120px;
            justify-content: center;
        }

        #restart-button {
            background-color: #007bff;
            color: #fff;
        }

        #restart-button:hover {
            background-color: #0056b3;
        }

        .outside-button[href="story_mode.php"] {
            background-color: #6c757d;
            color: #fff;
        }

        .outside-button[href="story_mode.php"]:hover {
            background-color: #5a6268;
        }

        .outside-button i {
            font-size: 0.9rem;
        }

        .outside-button * {
            pointer-events: none;
            user-select: none;
        }

        /* Custom scrollbar styles */
        .words::-webkit-scrollbar {
            width: 10px;
        }

        .words::-webkit-scrollbar-track {
            background: #1e1e1e;
            border-radius: 5px;
        }

        .words::-webkit-scrollbar-thumb {
            background: #2c2c2c;
            border-radius: 5px;
            border: 2px solid #1e1e1e;
        }

        .words::-webkit-scrollbar-thumb:hover {
            background: #363636;
        }

        /* Firefox scrollbar */
        .words {
            scrollbar-width: thin;
            scrollbar-color: #2c2c2c #1e1e1e;
        }

        /* WPM stat styles */
        #wpm {
            color: #00ff88;
            font-size: 1.5rem;
            font-weight: 600;
            text-shadow: 0 0 10px rgba(0, 255, 136, 0.3);
        }

        /* Accuracy stat styles */
        #accuracy {
            color: #007bff;
            font-size: 1.5rem;
            font-weight: 600;
            text-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
        }

        /* Timer stat styles */
        #timer {
            color: #ff9f1c;
            font-size: 1.5rem;
            font-weight: 600;
            text-shadow: 0 0 10px rgba(255, 159, 28, 0.3);
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: #2c2c2c;
            padding: 40px;
            border-radius: 15px;
            min-width: 400px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }

        .modal-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .modal-header h2 {
            color: #d1d0c5;
            font-size: 2.2rem;
            margin: 0;
            font-weight: 600;
        }

        .modal-body {
            display: flex;
            flex-direction: column;
            gap: 25px;
            padding: 0 20px;
        }

        .result-item {
            display: flex;
            align-items: center;
            gap: 20px;
            color: #d1d0c5;
            font-size: 1.4rem;
            padding: 10px 0;
        }

        .result-item i {
            width: 30px;
            text-align: center;
            color: #007bff;
            font-size: 1.3rem;
        }

        #final-wpm {
            color: #00ff88;
            font-weight: bold;
            font-size: 1.6rem;
        }

        #final-accuracy {
            color: #007bff;
            font-weight: bold;
            font-size: 1.6rem;
        }

        #final-time {
            color: #ff9f1c;
            font-weight: bold;
            font-size: 1.6rem;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-button {
            font-size: 1.2rem;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #fff;
        }

        .retry-btn {
            background-color: #6c757d;
        }

        .retry-btn:hover {
            background-color: #5a6268;
        }

        .next-btn {
            background-color: #28a745;
            color: #fff;
        }

        .next-btn:hover {
            background-color: #218838;
        }

        .modal-button i {
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="story-header">
            <h2 class="text-center mb-4"><?php echo htmlspecialchars($story['title']); ?></h2>
        </div>
    <div class="typing-container">
        <div class="header-section">
            <div class="stats-container">
                <div class="stat-item">
                    wpm: <span class="stat-value" id="wpm">0</span>
                </div>
                <div class="stat-item">
                    acc: <span class="stat-value" id="accuracy">100%</span>
                </div>
                <div class="stat-item">
                    time: <span class="stat-value" id="timer">0:00</span>
                </div>
            </div>
        </div>


        <div class="words" id="words"></div>
        <input type="text" class="input-field" id="input-field" style="opacity: 0; position: absolute;">

        <div class="keyboard">
            <!-- Keep the keyboard layout from lesson.php -->
        </div>

        <div class="outside-buttons">
            <button class="outside-button" id="restart-button">
                <i class="fas fa-redo"></i>
                restart story
            </button>
            <a href="story_mode.php" class="outside-button">
                <i class="fas fa-arrow-left"></i>
                back to stories
            </a>
            <?php if ($nextStory): ?>
            <a href="story_lesson.php?story=<?php echo $nextStory['id']; ?>" 
               id="next-btn" class="outside-button" style="display: none;">
                next story
                <i class="fas fa-arrow-right"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Update the Results Modal -->
    <div class="modal" id="resultsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Typing Results</h2>
            </div>
            <div class="modal-body">
                <div class="result-item">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>WPM: <span id="final-wpm">0</span></span>
                </div>
                <div class="result-item">
                    <i class="fas fa-bullseye"></i>
                    <span>Accuracy: <span id="final-accuracy">0%</span></span>
                </div>
                <div class="result-item">
                    <i class="fas fa-clock"></i>
                    <span>Time: <span id="final-time">0:00</span></span>
                </div>
                <div class="modal-buttons">
                    <button class="modal-button retry-btn" onclick="location.reload()">
                        <i class="fas fa-redo"></i>
                        Retry
                    </button>
                    <?php if ($nextStory): ?>
                    <a href="story_lesson.php?story=<?php echo $nextStory['id']; ?>" 
                       class="modal-button next-btn">
                        <span>Next Story</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const wordsContainer = document.getElementById('words');
            const inputField = document.getElementById('input-field');
            const storyText = <?php echo json_encode($story['content']); ?>;
            
            let isTyping = false;
            let currentIndex = 0;
            let startTime;
            let timer;
            let totalChars = 0;
            let mistakes = 0;
            let typedCharCount = 0;

            // Initialize text content with proper word wrapping
            const text = storyText.split('').map(char => {
                const span = document.createElement('span');
                span.textContent = char;
                span.className = 'letter';
                
                if (char === ' ') {
                    span.classList.add('space');
                } else if (char === '\n') {
                    span.classList.add('newline');
                }
                
                return span;
            });
            
            wordsContainer.innerHTML = '';
            text.forEach(span => wordsContainer.appendChild(span));
            
            if (text.length > 0) {
                text[0].classList.add('current');
            }

            // Handle input
            inputField.addEventListener('input', (e) => {
                if (!isTyping) {
                    isTyping = true;
                    startTime = new Date();
                    timer = setInterval(updateTimer, 1000);
                }

                const typed = e.target.value;
                if (typed && currentIndex < text.length) {
                    const currentChar = text[currentIndex];
                    
                    if (typed === currentChar.textContent) {
                        currentChar.classList.add('correct');
                        currentChar.classList.remove('current');
                        totalChars++;
                    } else {
                        currentChar.classList.add('incorrect');
                        currentChar.classList.remove('current');
                        totalChars++;
                        mistakes++;
                    }

                    currentIndex++;
                    if (currentIndex < text.length) {
                        text[currentIndex].classList.add('current');
                    }

                    if (currentIndex % 20 === 0) {
                        const currentLetter = text[currentIndex];
                        currentLetter.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }

                    updateStats();
                    e.target.value = '';

                    // Check if typing is complete
                    if (currentIndex >= text.length) {
                        clearInterval(timer);
                        showResults();
                    }
                }
            });

            function updateTimer() {
                const timeElapsed = Math.round((new Date() - startTime) / 1000);
                const minutes = Math.floor(timeElapsed / 60);
                const seconds = timeElapsed % 60;
                document.getElementById('timer').textContent = 
                    `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }

            function updateStats() {
                if (!startTime) return;
                
                const timeElapsed = (new Date() - startTime) / 1000 / 60; // in minutes
                const wpm = Math.round((totalChars / 5) / timeElapsed) || 0;
                const accuracy = totalChars > 0 
                    ? Math.round(((totalChars - mistakes) / totalChars) * 100) 
                    : 100;
                
                document.getElementById('wpm').textContent = wpm;
                document.getElementById('accuracy').textContent = `${accuracy}%`;
            }

            function showResults() {
                const timeElapsed = (new Date() - startTime) / 1000;
                const wpm = Math.round((totalChars / 5) / (timeElapsed / 60));
                const accuracy = Math.round(((totalChars - mistakes) / totalChars) * 100);

                // Update modal content
                document.getElementById('final-wpm').textContent = wpm;
                document.getElementById('final-accuracy').textContent = `${accuracy}%`;
                document.getElementById('final-time').textContent = 
                    `${Math.floor(timeElapsed / 60)}:${Math.floor(timeElapsed % 60).toString().padStart(2, '0')}`;

                // Show modal
                const modal = document.getElementById('resultsModal');
                modal.style.display = 'flex';

                // Show next button if accuracy is good enough
                const nextBtn = document.querySelector('.next-btn');
                if (nextBtn) {
                    nextBtn.style.display = accuracy >= 80 ? 'flex' : 'none';
                }

                // Add click event to close modal when clicking outside
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            }

            // Handle restart button
            document.getElementById('restart-button').addEventListener('click', () => {
                location.reload();
            });

            // Ensure input field stays focused
            document.addEventListener('click', () => {
                inputField.focus();
            });

            // Initial focus
            inputField.focus();
        });
    </script>
</body>
</html>