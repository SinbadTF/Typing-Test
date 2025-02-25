<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';

$level = $_GET['level'] ?? '';
$lessonNumber = $_GET['lesson'] ?? '';
$lessonId = $_GET['id'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM japanese_lessons WHERE id = ?");
$stmt->execute([$lessonId]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lesson) {
    header('Location: japanese_course.php');
    exit();
}

// Get next lesson
$stmt = $pdo->prepare("SELECT id, lesson_number FROM japanese_lessons WHERE level = ? AND lesson_number > ? ORDER BY lesson_number ASC LIMIT 1");
$stmt->execute([$level, $lessonNumber]);
$nextLesson = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($lesson['title']); ?> - Typing Lesson</title>
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
            justify-content: flex-start;
            gap: 2rem;
            margin-bottom: 2rem;
            color: #646669;
            padding-left: 20px;
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
            min-height: 140px;
            margin-bottom: 2rem;
            padding: 20px;
            background: rgba(32, 34, 37, 0.95);
            border-radius: 10px;
            border: 1px solid rgba(209, 208, 197, 0.1);
        }

        .words {
            user-select: none;
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

        .keyboard {
            margin: 30px auto;
            max-width: 900px;
            background: rgba(25, 25, 25, 0.95);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .keyboard-row {
            display: flex;
            justify-content: center;
            margin-bottom: 8px;
            gap: 6px;
        }

        .key {
            height: 45px;
            padding: 5px;
            margin: 2px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #444;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
            border-radius: 5px;
            font-size: 0.85rem;
        }

        /* Specific key widths */
        .key {
            width: 45px; /* Default width for regular keys */
        }

        .key.line { 
            width: 45px; 
        }

        .key.backspace { 
            width: 100px; 
        }

        .key.tab { 
            width: 80px; 
        }

        .key.caps { 
            width: 90px; 
        }

        .key.enter { 
            width: 90px; 
        }

        .key.shift { 
            width: 115px; 
        }

   
        .key.space { 
            width: 400px; 
        }
        
        .key.backspace { 
            width: 80px; 
        }


        .key.menu { 
            width: 70px; 
        }

        .key .english {
            font-size: 12px;
            color: #888;
        }

        .key .japanese {
            font-size: 16px;
            color: #fff;
        }

        .key:active, .key.active {
            background: #007bff;
            color: #ffffff;
            transform: translateY(2px);
        }

        .restart-button {
            background: #2c2e31;
            color: #646669;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.2s;
            text-decoration: none;
        }

        .restart-button:hover {
            background: #35373a;
            color: #d1d0c5;
        }

        #input-field {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }
        .theme-selector {
    position: absolute;
    right: 1rem;
    top: 0;
}

/* Remove the duplicate theme-selector that's outside the typing-container */
.theme-button {
    background: #3c3c3c;
    color: #d1d0c5;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.theme-button:hover {
    background: #4a4a4a;
}

.theme-options {
    position: absolute;
    right: 0;
    top: calc(100% + 8px);
    background: #2c2c2c;
    border-radius: 8px;
    padding: 8px;
    display: none;
    z-index: 1000;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    min-width: 150px;
}

.theme-options.show {
    display: block;
    animation: fadeIn 0.2s ease;
}

.theme-option {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 10px 15px;
    background: none;
    border: none;
    color: #d1d0c5;
    cursor: pointer;
    text-align: left;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.theme-option::before {
    content: '';
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 10px;
    border: 2px solid currentColor;
}

.theme-option:hover {
    background: #3c3c3c;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.keyboard.theme-light {
    background: #e0e0e0;
}
.keyboard.theme-light .key {
    background: #ffffff;
    color: #333333;
    box-shadow: 0 2px 0 #cccccc;
    border: 1px solid #e0e0e0;
}
.keyboard.theme-light .key.special,
.keyboard.theme-light .key.tab, 
.keyboard.theme-light .key.caps, 
.keyboard.theme-light .key.shift, 
.keyboard.theme-light .key.ctrl, 
.keyboard.theme-light .key.win, 
.keyboard.theme-light .key.alt, 
.keyboard.theme-light .key.menu, 
.keyboard.theme-light .key.enter,
.keyboard.theme-light .key.backspace {
    background: #f0f0f0;
    color: #333333;
}

.keyboard.theme-light .key.active {
    background: #4a9eff;
    color: #ffffff;
    box-shadow: 0 0 0 #cccccc;
    transform: translateY(2px);
}

.keyboard.theme-light .key .english {
    color: #333333;
}

.keyboard.theme-light .key .japanese {
    color: #333333;
}

/* Update blinking animation for light theme */
@keyframes keyBlinkLight {
    0%, 100% {
        background: #4a9eff;
        color: #ffffff;
        transform: translateY(2px);
        box-shadow: 0 0 15px rgba(74, 158, 255, 0.5);
    }
    50% {
        background: #ffffff;
        color: #333333;
        transform: translateY(0);
        box-shadow: 0 2px 0 #cccccc;
    }
}

.keyboard.theme-light .key.blinking {
    animation: keyBlinkLight 1s infinite;
}

.keyboard.theme-light .key.blinking .english,
.keyboard.theme-light .key.blinking .japanese {
    color: inherit;
}

.keyboard.theme-midnight {
    background: #1a1b26;
}
.keyboard.theme-midnight .key {
    background: #24283b;
    color: #7aa2f7;
    box-shadow: 0 2px 0 #16161e;
}

.keyboard.theme-forest {
    background: #2b2f2b;
}
.keyboard.theme-forest .key {
    background: #1e231f;
    color: #95c085;
    box-shadow: 0 2px 0 #161916;
}

.keyboard.theme-sunset {
    background: #2d1b2d;
}
.keyboard.theme-sunset .key {
    background: #1f1520;
    color: #f67e7d;
    box-shadow: 0 2px 0 #170d17;
}

/* Theme transitions */
.keyboard, .key, body {
    transition: all 0.3s ease;
}
/* Add these theme-specific navbar styles */
.navbar.theme-light {
    background-color: #e0e0e0;
}

.navbar.theme-light .nav-link,
.navbar.theme-light .navbar-brand {
    color: #2c2c2c !important;
}

.navbar.theme-light .nav-link:hover {
    color: #4a9eff !important;
}

        .caps-warning {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(202, 71, 84, 0.9);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: none;
            z-index: 1000;
        }

        .caps-warning.show {
            display: block;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            position: relative;
            padding: 0 1rem;
        }

        /* Add these modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999; /* Increased z-index */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: #323437;
            color: #d1d0c5;
            padding: 30px;
            margin: 15% auto;
            width: 90%;
            max-width: 400px;
            text-align: center;
            border-radius: 10px;
            border: 1px solid rgba(209, 208, 197, 0.1);
            position: relative;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-content h2 {
            margin-bottom: 20px;
            color: #d1d0c5;
        }

        .modal-content p {
            margin: 15px 0;
            font-size: 1.1rem;
        }

        .modal-content strong {
            color: #4a9eff;
        }

        .btn-retry, .btn-course, .btn-next {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
        }

        .btn-retry {
            background-color: #646669;
            color: #ffffff;
        }

        .btn-course {
            background-color: #2c2c2c;
            color: #d1d0c5;
            border: 1px solid #646669;
        }

        .btn-next {
            background-color: #4a9eff;
            color: #ffffff;
        }

        .btn-retry:hover, .btn-course:hover, .btn-next:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        .modal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        :root {
            --theme-bg: #323437;
            --theme-text: #d1d0c5;
            --theme-primary: #4a9eff;
        }

        /* Theme button styles */
        .theme-selector {
            position: relative;
            z-index: 1000;
        }

        .theme-button {
            background: #3c3c3c;
            color: var(--theme-text);
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .theme-button:hover {
            background: #4a4a4a;
        }

        .theme-options {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background: #2c2c2c;
            border-radius: 8px;
            padding: 8px;
            display: none;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            min-width: 150px;
        }

        .theme-options.show {
            display: block;
            animation: fadeIn 0.2s ease;
        }

        .theme-option {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 10px 15px;
            background: none;
            border: none;
            color: var(--theme-text);
            cursor: pointer;
            text-align: left;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .theme-option:hover {
            background: #3c3c3c;
        }

        /* Theme-specific styles */
        .theme-light {
            background-color: #ffffff;
            color: #333333;
        }

        .theme-midnight {
            background-color: #1a1b26;
            color: #7aa2f7;
        }

        .theme-forest {
            background-color: #2b2f2b;
            color: #95c085;
        }

        .theme-sunset {
            background-color: #2d1b2d;
            color: #f67e7d;
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Theme-specific keyboard styles */
        .keyboard.theme-light .key {
        
            background: #ffffff;
            color: #333333;
            box-shadow: 0 2px 0 #cccccc;
        }

        .keyboard.theme-midnight .key {
            background: #24283b;
            color: #7aa2f7;
            box-shadow: 0 2px 0 #16161e;
        }

        .keyboard.theme-forest .key {
            background: #1e231f;
            color: #95c085;
            box-shadow: 0 2px 0 #161916;
        }

        .keyboard.theme-sunset .key {
            background: #1f1520;
            color: #f67e7d;
            box-shadow: 0 2px 0 #170d17;
        }

        /* Theme-specific styles for theme selector */
        /* Dark theme (default) */
        .theme-selector .theme-button {
            background: #3c3c3c;
            color: #d1d0c5;
        }

        .theme-selector .theme-options {
            background: #2c2c2c;
            border: 1px solid rgba(209, 208, 197, 0.1);
        }

        .theme-selector .theme-option {
            color: #d1d0c5;
        }

        .theme-selector .theme-option:hover {
            background: #3c3c3c;
        }

        /* Light theme */
        .theme-light .theme-selector .theme-button {
            background: #e0e0e0;
            color: #333333;
            border: 1px solid #cccccc;
        }

        .theme-light .theme-selector .theme-button:hover {
            background: #d4d4d4;
        }

        .theme-light .theme-selector .theme-options {
            background: #ffffff;
            border: 1px solid #cccccc;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .theme-light .theme-selector .theme-option {
            color: #333333;
        }

        .theme-light .theme-selector .theme-option:hover {
            background: #f0f0f0;
        }

        /* Midnight theme */
        .theme-midnight .theme-selector .theme-button {
            background: #24283b;
            color: #7aa2f7;
        }

        .theme-midnight .theme-selector .theme-options {
            background: #1a1b26;
            border: 1px solid rgba(122, 162, 247, 0.2);
        }

        /* Forest theme */
        .theme-forest .theme-selector .theme-button {
            background: #1e231f;
            color: #95c085;
        }

        .theme-forest .theme-selector .theme-options {
            background: #2b2f2b;
            border: 1px solid rgba(149, 192, 133, 0.2);
        }

        /* Sunset theme */
        .theme-sunset .theme-selector .theme-button {
            background: #1f1520;
            color: #f67e7d;
        }

        .theme-sunset .theme-selector .theme-options {
            background: #2d1b2d;
            border: 1px solid rgba(246, 126, 125, 0.2);
        }

        /* Common hover effects for theme options */
        .theme-option::before {
            content: '';
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .theme-option[data-theme="dark"]::before {
            background: #323437;
            border: 1px solid #d1d0c5;
        }

        .theme-option[data-theme="light"]::before {
            background: #ffffff;
            border: 1px solid #333333;
        }

        .theme-option[data-theme="midnight"]::before {
            background: #1a1b26;
            border: 1px solid #7aa2f7;
        }

        .theme-option[data-theme="forest"]::before {
            background: #2b2f2b;
            border: 1px solid #95c085;
        }

        .theme-option[data-theme="sunset"]::before {
            background: #2d1b2d;
            border: 1px solid #f67e7d;
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
         
            padding: 2.5rem;
            border-radius: 15px;
            border: 1px solid rgba(209, 208, 197, 0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .results-card h2 {
            color: #d1d0c5;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .result-stat {
            margin: 1.2rem 0;
            font-size: 1.5rem;
            color: #646669;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 2rem;
            border-radius: 8px;
            background: rgba(32, 34, 37, 0.5);
        }

        .result-value {
            font-weight: 600;
            font-size: 2rem;
        }

        #finalWpm { color: #00ff88; }
        #finalAccuracy { color: #007bff; }
        #finalErrors { color: #ff4444; }
        #finalTime { color: #ffaa00; }

        .restart-button {
            background: linear-gradient(45deg, #007bff, #00ff88);
            border: none;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s;
            text-decoration: none;
            margin: 0 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .restart-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            opacity: 0.9;
            color: white;
            text-decoration: none;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .results-card {
            animation: fadeIn 0.3s ease-out;
        }

        .sound-toggle {
            background: none;
            border: none;
            color: #d1d0c5;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 10px;
            transition: all 0.3s ease;
            margin-left: 1rem;
        }

        .sound-toggle:hover {
            color: #4a9eff;
        }

        .sound-toggle.muted {
            color: #646669;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="typing-container">
        <div class="header-section">
            <!-- Add this after the typing-container div -->
<div class="caps-warning" id="capsWarning">
    <i class="fas fa-exclamation-triangle"></i>
    Caps Lock is ON
</div>
            <div class="stats-container">
                <div class="stat-item">wpm: <span class="stat-value" id="wpm">0</span></div>
                <div class="stat-item">acc: <span class="stat-value" id="accuracy">100%</span></div>
                <div class="stat-item">time: <span class="stat-value" id="time">0:00</span></div>
                <button class="sound-toggle" id="soundToggle">
                    <i class="fas fa-volume-up"></i>
                </button>
            </div>
            <div class="theme-selector">
                <button class="theme-button" id="theme-toggle">
                    <i class="fas fa-palette"></i> Theme
                </button>
                <div class="theme-options" id="theme-options">
                    <button class="theme-option" data-theme="dark">Dark</button>
                    <button class="theme-option" data-theme="light">Light</button>
                    <button class="theme-option" data-theme="midnight">Midnight</button>
                    <button class="theme-option" data-theme="forest">Forest</button>
                    <button class="theme-option" data-theme="sunset">Violet</button>
                </div>
            </div>
        </div>
        <div class="typing-area">
            <div id="words" class="words"></div>
            <input type="text" id="input-field" autocomplete="off">
        </div>

    

        <div class="keyboard"></div>
    </div>

    <div id="capsWarning" class="caps-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>Caps Lock is ON
    </div>
    
    </div>
    

    <div class="results-overlay" id="resultsOverlay">
        <div class="results-card">
            <h2>Typing Test Results</h2>
            <div class="result-stat">
                <span>Speed:</span>
                <span class="result-value" id="finalWpm">0</span> WPM
            </div>
            <div class="result-stat">
                <span>Accuracy:</span>
                <span class="result-value" id="finalAccuracy">0%</span>
            </div>
            <div class="result-stat">
                <span>Error:</span>
                <span class="result-value" id="finalErrors">0</span>
            </div>
            <div class="result-stat">
                <span>Time:</span>
                <span class="result-value" id="finalTime">0s</span>
            </div>
            <div class="d-flex justify-content-center gap-3 mt-4">
                <button class="restart-button" onclick="initializePractice()">
                    <i class="fas fa-redo me-2"></i>Try Again
                </button>
                <a href="japanese_course.php" class="restart-button">
                    <i class="fas fa-book me-2"></i>Back to Course
                </a>
                <?php if ($nextLesson): ?>
                    <a href="japanese_lesson.php?level=<?php echo htmlspecialchars($level); ?>&lesson=<?php echo htmlspecialchars($nextLesson['lesson_number']); ?>&id=<?php echo htmlspecialchars($nextLesson['id']); ?>" 
                       class="restart-button">
                        <i class="fas fa-arrow-right me-2"></i>Next Lesson
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <audio id="keySound" preload="auto">
        <source src="assets/sounds/key-press.mp3" type="audio/mpeg">
    </audio>
    <audio id="errorSound" preload="auto">
        <source src="assets/sounds/error.mp3" type="audio/mpeg">
    </audio>

    <script>
     const japaneseKeyMap = {
    // Direct key mappings (for Japanese keyboard layout)
    'a': 'ち', 'b': 'こ', 'c': 'そ', 'd': 'し', 'e': 'い',
    'f': 'は', 'g': 'き', 'h': 'く', 'i': 'に', 'j': 'ま',
    'k': 'の', 'l': 'り', 'm': 'も', 'n': 'み', 'o': 'ら',
    'p': 'せ', 'q': 'た', 'r': 'す', 's': 'と', 't': 'か',
    'u': 'な', 'v': 'ひ', 'w': 'て', 'x': 'さ', 'y': 'ん',
    'z': 'つ', ';': 'れ', ':': 'け', ']': 'む', '[': '゛',
    ',': 'ね', '.': 'る', '/': 'め', '-': 'ほ', '=': 'へ',
    '1': 'ぬ', '2': 'ふ', '3': 'あ', '4': 'う', '5': 'え',
    '6': 'お', '7': 'や', '8': 'ゆ', '9': 'よ', '0': 'わ',

    // Romaji to Hiragana mappings
    'ka': 'か', 'ki': 'き', 'ku': 'く', 'ke': 'け', 'ko': 'こ',
    'sa': 'さ', 'shi': 'し', 'su': 'す', 'se': 'せ', 'so': 'そ',
    'ta': 'た', 'chi': 'ち', 'tsu': 'つ', 'te': 'て', 'to': 'と',
    'na': 'な', 'ni': 'に', 'nu': 'ぬ', 'ne': 'ね', 'no': 'の',
    'ha': 'は', 'hi': 'ひ', 'fu': 'ふ', 'he': 'へ', 'ho': 'ほ',
    'ma': 'ま', 'mi': 'み', 'mu': 'む', 'me': 'め', 'mo': 'も',
    'ya': 'や', 'yu': 'ゆ', 'yo': 'よ',
    'ra': 'ら', 'ri': 'り', 'ru': 'る', 're': 'れ', 'ro': 'ろ',
    'wa': 'わ', 'wo': 'を', 'nn': 'ん',

    // Dakuten variations
    'ga': 'が', 'gi': 'ぎ', 'gu': 'ぐ', 'ge': 'げ', 'go': 'ご',
    'za': 'ざ', 'ji': 'じ', 'zu': 'ず', 'ze': 'ぜ', 'zo': 'ぞ',
    'da': 'だ', 'di': 'ぢ', 'du': 'づ', 'de': 'で', 'do': 'ど',
    'ba': 'ば', 'bi': 'び', 'bu': 'ぶ', 'be': 'べ', 'bo': 'ぼ',
    'pa': 'ぱ', 'pi': 'ぴ', 'pu': 'ぷ', 'pe': 'ぺ', 'po': 'ぽ'
};

        // Replace the hardcoded practice text with lesson content from database
        const practiceText = <?php echo json_encode($lesson['content']); ?>;

        // Initialize variables
        let currentLetterIndex = 0;
        let mistakes = 0;
        let totalCharacters = 0;
        let startTime = null;
        let isTyping = false;

        function createKeyboard() {
    const keyboard = document.querySelector('.keyboard');
    const layout = [
        // First row - numbers and symbols
        [
            { eng: '`', jpn: 'ろ', shift_jpn: '〜' },
            { eng: '1', jpn: 'ぬ', shift_jpn: '！' },
            { eng: '2', jpn: 'ふ', shift_jpn: '"' },
            { eng: '3', jpn: 'あ', shift_jpn: '＃' },
            { eng: '4', jpn: 'う', shift_jpn: '＄' },
            { eng: '5', jpn: 'え', shift_jpn: '％' },
            { eng: '6', jpn: 'お', shift_jpn: '＆' },
            { eng: '7', jpn: 'や', shift_jpn: '' },
            { eng: '8', jpn: 'ゆ', shift_jpn: '（' },
            { eng: '9', jpn: 'よ', shift_jpn: '）' },
            { eng: '0', jpn: 'わ', shift_jpn: '～' },
            { eng: '-', jpn: 'ほ', shift_jpn: '＝' },
            { eng: '=', jpn: 'へ', shift_jpn: '～' },
            { eng: 'Backspace', jpn: '', class: 'backspace' }
        ],
        // Second row
        [
            { eng: 'Tab', jpn: '', class: 'tab' },
            { eng: 'q', jpn: 'た' }, { eng: 'w', jpn: 'て' }, { eng: 'e', jpn: 'い' },
            { eng: 'r', jpn: 'す' }, { eng: 't', jpn: 'か' }, { eng: 'y', jpn: 'ん' },
            { eng: 'u', jpn: 'な' }, { eng: 'i', jpn: 'に' }, { eng: 'o', jpn: 'ら' },
            { eng: 'p', jpn: 'せ' }, { eng: '[', jpn: '「' }, { eng: ']', jpn: '」' },
            { eng: '\\', jpn: '￥' }
        ],
        // Third row
        [
            { eng: 'Caps', jpn: '', class: 'caps' },
            { eng: 'a', jpn: 'ち' }, { eng: 's', jpn: 'と' }, { eng: 'd', jpn: 'し' },
            { eng: 'f', jpn: 'は' }, { eng: 'g', jpn: 'き' }, { eng: 'h', jpn: 'く' },
            { eng: 'j', jpn: 'ま' }, { eng: 'k', jpn: 'の' }, { eng: 'l', jpn: 'り' },
            { eng: ';', jpn: 'れ' }, { eng: "'", jpn: 'け' },
            { eng: 'Enter', jpn: '', class: 'enter' }
        ],
        // Fourth row
        [
            { eng: 'Shift', jpn: '', class: 'shift' },
            { eng: 'z', jpn: 'つ' }, { eng: 'x', jpn: 'さ' }, { eng: 'c', jpn: 'そ' },
            { eng: 'v', jpn: 'ひ' }, { eng: 'b', jpn: 'こ' }, { eng: 'n', jpn: 'み' },
            { eng: 'm', jpn: 'も' }, { eng: ',', jpn: 'ね' }, { eng: '.', jpn: 'る' },
            { eng: '/', jpn: 'め' },
            { eng: 'Shift', jpn: '', class: 'shift' }
        ],
        // Fifth row
        [
            { eng: 'Space', jpn: '', class: 'space' }
        ]
    ];

    layout.forEach(row => {
        const keyboardRow = document.createElement('div');
        keyboardRow.className = 'keyboard-row';
        
        row.forEach(key => {
            const keyElement = document.createElement('div');
            keyElement.className = `key ${key.class || ''}`;
            keyElement.setAttribute('data-key', key.eng.toLowerCase());
            
            if (key.eng === 'Space' || key.eng === 'Backspace' || key.eng === 'Tab' || 
                key.eng === 'Caps' || key.eng === 'Enter' || key.eng === 'Shift') {
                keyElement.innerHTML = `<div class="english">${key.eng}</div>`;
            } else {
                keyElement.innerHTML = `
                    <div class="english">${key.eng}</div>
                    <div class="japanese">${key.jpn}</div>
                `;
            }
            
            keyboardRow.appendChild(keyElement);
        });
        
        keyboard.appendChild(keyboardRow);
    });
}

        // Initialize the practice
        function initializePractice() {
            const wordsContainer = document.getElementById('words');
            wordsContainer.innerHTML = Array.from(practiceText).map(char => 
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
            document.getElementById('time').textContent = '0:00';
        }

        // Handle typing input
        function handleTyping(event) {
            if (event.key === 'Tab' || event.key === 'Shift') {
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

            if (event.key === 'Backspace') {
                if (currentLetterIndex > 0) {
                    const prevLetter = letters[currentLetterIndex - 1];
                    prevLetter.classList.remove('correct', 'incorrect');
                    currentLetter.classList.remove('current');
                    prevLetter.classList.add('current');
                    currentLetterIndex--;
                }
                return;
            }

            if (event.key.length === 1) {
                const mappedChar = japaneseKeyMap[event.key.toLowerCase()] || event.key;
                const targetChar = currentLetter.textContent;

                currentLetter.classList.remove('current');
                if (mappedChar === targetChar) {
                    currentLetter.classList.add('correct');
                    if (!isMuted) {
                        keySound.currentTime = 0;
                        keySound.play().catch(e => console.log('Sound play failed:', e));
                    }
                } else {
                    currentLetter.classList.add('incorrect');
                    if (!isMuted) {
                        errorSound.currentTime = 0;
                        errorSound.play().catch(e => console.log('Sound play failed:', e));
                    }
                    mistakes++;
                }
                
                // Always move to next character regardless of correctness
                if (currentLetterIndex < letters.length - 1) {
                    letters[currentLetterIndex + 1].classList.add('current');
                    currentLetterIndex++;
                } else {
                    finishPractice();
                }
                
                totalCharacters++;
                updateAccuracy();
            }
        }

        // Timer function
        function startTimer() {
            const timerDisplay = document.getElementById('time');
            
            function updateTimer() {
                if (!startTime || !isTyping) return;
                
                const currentTime = new Date();
                const timeElapsed = Math.floor((currentTime - startTime) / 1000);
                const minutes = Math.floor(timeElapsed / 60);
                const seconds = timeElapsed % 60;
                
                timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                const wpm = Math.round((totalCharacters / 5) / (timeElapsed / 60));
                document.getElementById('wpm').textContent = wpm || 0;
                
                if (isTyping) {
                    requestAnimationFrame(updateTimer);
                }
            }
            
            updateTimer();
        }

        // Update accuracy display
        function updateAccuracy() {
            const accuracy = Math.round(((totalCharacters - mistakes) / totalCharacters) * 100) || 100;
            document.getElementById('accuracy').textContent = `${accuracy}%`;
        }

        // Finish practice
        function finishPractice() {
            isTyping = false;
            const timeElapsed = (new Date() - startTime) / 1000;
            const wpm = Math.round((totalCharacters / 5) / (timeElapsed / 60));
            const accuracy = Math.round(((totalCharacters - mistakes) / totalCharacters) * 100);

            document.getElementById('finalWpm').textContent = wpm;
            document.getElementById('finalAccuracy').textContent = accuracy + '%';
            document.getElementById('finalTime').textContent = timeElapsed.toFixed(1) + 's';
            document.getElementById('finalErrors').textContent = mistakes;
            
            const resultsOverlay = document.getElementById('resultsOverlay');
            if (resultsOverlay) {
                resultsOverlay.style.display = 'flex';
            }

            // Save progress
            fetch('save_japanese_progress.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    wpm: wpm,
                    accuracy: accuracy,
                    mistakes: mistakes,
                    time_taken: timeElapsed
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Failed to save progress');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', () => {
            createKeyboard();
            initializePractice();
            document.addEventListener('keydown', handleTyping);
            
            // Keep input field focused
            const inputField = document.getElementById('input-field');
            document.addEventListener('click', () => {
                inputField.focus();
            });

            const modal = document.getElementById('resultModal');
            if (!modal) {
                console.error('Modal element not found!');
            }
        });

        // Handle keyboard highlighting
        document.addEventListener('keydown', (event) => {
            const key = document.querySelector(`[data-key="${event.key.toLowerCase()}"]`);
            if (key) key.classList.add('active');
        });

        document.addEventListener('keyup', (event) => {
            const key = document.querySelector(`[data-key="${event.key.toLowerCase()}"]`);
            if (key) key.classList.remove('active');
        });

        // Theme handling
        document.addEventListener('DOMContentLoaded', () => {
            const themeToggle = document.getElementById('theme-toggle');
            const themeOptions = document.getElementById('theme-options');
            const keyboard = document.querySelector('.keyboard');
            const body = document.body;
            const navbar = document.querySelector('.navbar');
            const typingContainer = document.querySelector('.typing-container');
            
            // Load saved theme
            const savedTheme = localStorage.getItem('selectedTheme') || 'dark';
            applyTheme(savedTheme);

            // Toggle theme options visibility
            themeToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                themeOptions.classList.toggle('show');
            });

            // Handle theme selection
            document.querySelectorAll('.theme-option').forEach(option => {
                option.addEventListener('click', () => {
                    const theme = option.getAttribute('data-theme');
                    applyTheme(theme);
                    localStorage.setItem('selectedTheme', theme);
                    themeOptions.classList.remove('show');
                });
            });

            // Close theme options when clicking outside
            document.addEventListener('click', (e) => {
                if (!themeOptions.contains(e.target) && !themeToggle.contains(e.target)) {
                    themeOptions.classList.remove('show');
                }
            });

            // Function to apply theme
            function applyTheme(theme) {
                // Remove all existing theme classes
                const elements = [body, keyboard, navbar, typingContainer];
                const themeClasses = ['theme-dark', 'theme-light', 'theme-midnight', 'theme-forest', 'theme-sunset'];
                
                elements.forEach(element => {
                    if (element) {
                        themeClasses.forEach(className => {
                            element.classList.remove(className);
                        });
                        element.classList.add(`theme-${theme}`);
                    }
                });

                // Update theme-specific colors
                updateThemeColors(theme);
            }

            // Function to update theme-specific colors
            function updateThemeColors(theme) {
                const themeColors = {
                    dark: {
                        bg: '#323437',
                        text: '#d1d0c5',
                        primary: '#4a9eff'
                    },
                    light: {
                        bg: '#ffffff',
                        text: '#333333',
                        primary: '#4a9eff'
                    },
                    midnight: {
                        bg: '#1a1b26',
                        text: '#7aa2f7',
                        primary: '#7aa2f7'
                    },
                    forest: {
                        bg: '#2b2f2b',
                        text: '#95c085',
                        primary: '#95c085'
                    },
                    sunset: {
                        bg: '#2d1b2d',
                        text: '#f67e7d',
                        primary: '#f67e7d'
                    }
                };

                const colors = themeColors[theme];
                document.documentElement.style.setProperty('--theme-bg', colors.bg);
                document.documentElement.style.setProperty('--theme-text', colors.text);
                document.documentElement.style.setProperty('--theme-primary', colors.primary);
            }
        });

        // Add these variables with your other declarations
        const keySound = document.getElementById('keySound');
        const errorSound = document.getElementById('errorSound');
        const soundToggle = document.getElementById('soundToggle');
        let isMuted = localStorage.getItem('isMuted') === 'true';

        // Add this function
        function updateSoundIcon() {
            const icon = soundToggle.querySelector('i');
            icon.className = isMuted ? 'fas fa-volume-mute' : 'fas fa-volume-up';
            soundToggle.classList.toggle('muted', isMuted);
            
            // Update audio elements
            keySound.muted = isMuted;
            errorSound.muted = isMuted;
        }

        // Initialize sound state
        updateSoundIcon();

        // Add sound toggle event listener
        soundToggle.addEventListener('click', (e) => {
            e.preventDefault();
            isMuted = !isMuted;
            localStorage.setItem('isMuted', isMuted);
            updateSoundIcon();
            document.getElementById('input-field').focus();
        });
    </script>
</body>
</html> 