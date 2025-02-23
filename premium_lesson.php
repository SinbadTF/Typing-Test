<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include database connection first
require_once 'config/database.php';

// Then check premium status
$stmt = $pdo->prepare("SELECT is_premium FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['is_premium'] != 1) {
    header('Location: premium.php');
    exit();
}

// Get parameters from URL
$category = $_GET['category'] ?? '';
$lessonNumber = $_GET['lesson'] ?? '';
$lang = $_GET['lang'] ?? 'en';

// Fetch lesson content based on category
if ($category === 'books') {
    $stmt = $pdo->prepare("SELECT * FROM premium_books WHERE language = ? AND lesson_number = ?");
    $stmt->execute([$lang, $lessonNumber]);
    $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
    $content = $lesson['content'] ?? '';
    $title = $lesson['title'] ?? '';
} elseif ($category === 'lyrics') {
    // Get song content from the songs array
    $songLessons = [
        1 => [
            'title' => 'Perfect - Ed Sheeran',
            'content' => "I found a love for me\nDarling, just dive right in and follow my lead\nWell, I found a girl, beautiful and sweet\nOh, I never knew you were the someone waiting for me",
            'difficulty' => 'Easy'
        ],
        // Add more songs here
    ];
    $content = $songLessons[$lessonNumber]['content'] ?? '';
    $title = $songLessons[$lessonNumber]['title'] ?? '';
}

// Add language-specific titles
$languageTitles = [
    'en' => 'Premium Typing Practice',
    'my' => 'Premium စာရိုက်လေ့ကျင့်ခန်း',
    'jp' => 'プレミアムタイピング練習'
];

$pageTitle = $languageTitles[$lang] ?? $languageTitles['en'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Boku no Typing</title>
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

        .lesson-title {
            color: #e2b714;
            font-size: 1.8rem;
            margin-bottom: 30px;
            text-align: center;
        }

        .typing-text {
            background: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 1.2rem;
            line-height: 1.6;
        }

        .stats-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
            background: rgba(0, 0, 0, 0.2);
            padding: 15px;
            border-radius: 10px;
        }

        .stat-item {
            font-size: 1.2rem;
        }

        .stat-value {
            color: #e2b714;
            font-weight: 600;
        }

        #input-field {
            width: 100%;
            background: rgba(0, 0, 0, 0.2);
            border: none;
            padding: 15px;
            color: #d1d0c5;
            font-family: 'Roboto Mono', monospace;
            font-size: 1.2rem;
            border-radius: 10px;
            margin-top: 20px;
        }

        #input-field:focus {
            outline: none;
            box-shadow: 0 0 0 2px #e2b714;
        }

        .correct {
            color: #98c379;
        }

        .incorrect {
            color: #e06c75;
            text-decoration: underline;
        }

        .current {
            background-color: #e2b714;
            color: #323437;
        }

        .keyboard {
            margin-top: 30px;
            user-select: none;
        }

        .keyboard-row {
            display: flex;
            justify-content: center;
            margin-bottom: 8px;
        }

        .key {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            color: #d1d0c5;
            padding: 8px;
            margin: 0 4px;
            min-width: 30px;
            text-align: center;
            cursor: default;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .key.special {
            min-width: 60px;
            font-size: 12px;
            text-transform: uppercase;
        }

        .key[data-key=" "] {
            width: 400px;
        }

        .key.active {
            background: #e2b714;
            color: #323437;
            border-color: #e2b714;
            transform: translateY(2px);
        }

        .key.wrong {
            background: #e06c75;
            color: #fff;
            border-color: #e06c75;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="typing-container">
        <h2 class="lesson-title"><?php echo htmlspecialchars($title); ?></h2>
        
        <div class="stats-container">
            <div class="stat-item">WPM: <span class="stat-value" id="wpm">0</span></div>
            <div class="stat-item">Accuracy: <span class="stat-value" id="accuracy">100%</span></div>
            <div class="stat-item">Time: <span class="stat-value" id="timer">0:00</span></div>
        </div>

        <div class="typing-text">
            <div id="text-display"><?php echo htmlspecialchars($content); ?></div>
        </div>

        <input type="text" id="input-field" placeholder="Start typing..." autocomplete="off">

        <div class="keyboard">
            <div class="keyboard-row">
                <div class="key" data-key="`">`</div>
                <div class="key" data-key="1">1</div>
                <div class="key" data-key="2">2</div>
                <div class="key" data-key="3">3</div>
                <div class="key" data-key="4">4</div>
                <div class="key" data-key="5">5</div>
                <div class="key" data-key="6">6</div>
                <div class="key" data-key="7">7</div>
                <div class="key" data-key="8">8</div>
                <div class="key" data-key="9">9</div>
                <div class="key" data-key="0">0</div>
                <div class="key" data-key="-">-</div>
                <div class="key" data-key="=">=</div>
                <div class="key special" data-key="Backspace">delete</div>
            </div>
            <div class="keyboard-row">
                <div class="key special" data-key="Tab">tab</div>
                <div class="key" data-key="q">q</div>
                <div class="key" data-key="w">w</div>
                <div class="key" data-key="e">e</div>
                <div class="key" data-key="r">r</div>
                <div class="key" data-key="t">t</div>
                <div class="key" data-key="y">y</div>
                <div class="key" data-key="u">u</div>
                <div class="key" data-key="i">i</div>
                <div class="key" data-key="o">o</div>
                <div class="key" data-key="p">p</div>
                <div class="key" data-key="[">[</div>
                <div class="key" data-key="]">]</div>
                <div class="key" data-key="\">\</div>
            </div>
            <div class="keyboard-row">
                <div class="key special" data-key="CapsLock">caps</div>
                <div class="key" data-key="a">a</div>
                <div class="key" data-key="s">s</div>
                <div class="key" data-key="d">d</div>
                <div class="key" data-key="f">f</div>
                <div class="key" data-key="g">g</div>
                <div class="key" data-key="h">h</div>
                <div class="key" data-key="j">j</div>
                <div class="key" data-key="k">k</div>
                <div class="key" data-key="l">l</div>
                <div class="key" data-key=";">;</div>
                <div class="key" data-key="'">'</div>
                <div class="key special" data-key="Enter">enter</div>
            </div>
            <div class="keyboard-row">
                <div class="key special" data-key="Shift">shift</div>
                <div class="key" data-key="z">z</div>
                <div class="key" data-key="x">x</div>
                <div class="key" data-key="c">c</div>
                <div class="key" data-key="v">v</div>
                <div class="key" data-key="b">b</div>
                <div class="key" data-key="n">n</div>
                <div class="key" data-key="m">m</div>
                <div class="key" data-key=",">,</div>
                <div class="key" data-key=".">.</div>
                <div class="key" data-key="/">/</div>
                <div class="key special" data-key="Shift">shift</div>
            </div>
            <div class="keyboard-row">
                <div class="key" data-key=" ">space</div>
            </div>
        </div>

        <!-- Update the audio elements to use key-press.mp3 for correct sound -->
        <audio id="correctSound" preload="auto">
            <source src="assets/sounds/key-press.mp3" type="audio/mpeg">
        </audio>
        <audio id="wrongSound" preload="auto">
            <source src="assets/sounds/error.mp3" type="audio/mpeg">
        </audio>
    </div>

    <script>
        const textDisplay = document.getElementById('text-display');
        const inputField = document.getElementById('input-field');
        const correctSound = document.getElementById('correctSound');
        const wrongSound = document.getElementById('wrongSound');
        
        let currentIndex = 0;
        let mistakes = 0;
        let startTime = null;
        let timerInterval = null;

        // Split text into spans for individual character tracking
        const text = textDisplay.textContent;
        textDisplay.innerHTML = text.split('').map(char => 
            `<span>${char}</span>`
        ).join('');
        const characters = textDisplay.getElementsByTagName('span');

        inputField.addEventListener('input', (e) => {
            const typedChar = e.target.value;
            const currentChar = text[currentIndex];

            if (!startTime) {
                startTime = new Date();
                startTimer();
            }

            if (typedChar === currentChar) {
                // Correct input
                characters[currentIndex].classList.add('correct');
                characters[currentIndex].classList.remove('current', 'incorrect');
                currentIndex++;
                if (currentIndex < text.length) {
                    characters[currentIndex].classList.add('current');
                }
                correctSound.currentTime = 0;
                correctSound.play();
            } else {
                // Wrong input
                characters[currentIndex].classList.add('incorrect');
                mistakes++;
                wrongSound.currentTime = 0;
                wrongSound.play();
                
                // Highlight wrong key on keyboard
                const keyElement = document.querySelector(`.key[data-key="${typedChar}"]`);
                if (keyElement) {
                    keyElement.classList.add('wrong');
                    setTimeout(() => keyElement.classList.remove('wrong'), 200);
                }
            }

            // Clear input field
            e.target.value = '';

            // Update stats
            updateStats();

            // Check if typing is complete
            if (currentIndex === text.length) {
                finishTyping();
            }
        });

        function updateStats() {
            const timeElapsed = Math.round((new Date() - startTime) / 1000);
            const wpm = Math.round((currentIndex * 60) / (5 * timeElapsed));
            const accuracy = Math.round(((currentIndex - mistakes) / currentIndex) * 100);

            document.getElementById('wpm').textContent = wpm || 0;
            document.getElementById('accuracy').textContent = `${accuracy || 100}%`;
        }

        function startTimer() {
            const timerElement = document.getElementById('timer');
            let seconds = 0;
            timerInterval = setInterval(() => {
                seconds++;
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                timerElement.textContent = `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
            }, 1000);
        }

        function finishTyping() {
            clearInterval(timerInterval);
            inputField.disabled = true;
            // You can add more completion logic here
        }

        // Keyboard highlighting
        const keys = document.querySelectorAll('.key');
        
        document.addEventListener('keydown', (e) => {
            const key = e.key;
            const keyElement = document.querySelector(`.key[data-key="${key}"]`);
            if (keyElement) {
                keyElement.classList.add('active');
            }
        });

        document.addEventListener('keyup', (e) => {
            const key = e.key;
            const keyElement = document.querySelector(`.key[data-key="${key}"]`);
            if (keyElement) {
                keyElement.classList.remove('active');
                keyElement.classList.remove('wrong');
            }
        });

        // Debug sound loading
        correctSound.addEventListener('error', function(e) {
            console.error('Error loading correct sound:', e);
        });

        wrongSound.addEventListener('error', function(e) {
            console.error('Error loading wrong sound:', e);
        });

        // Test sounds on page load
        window.addEventListener('load', function() {
            console.log('Correct sound loaded:', correctSound.readyState);
            console.log('Wrong sound loaded:', wrongSound.readyState);
        });
    </script>
</body>
</html> 