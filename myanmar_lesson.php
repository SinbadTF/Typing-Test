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

// Modified query to get Myanmar lessons
$stmt = $pdo->prepare("SELECT * FROM myanmar_lessons WHERE id = ?");
$stmt->execute([$lessonId]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$lesson) {
    header('Location: myanmar_course.php');
    exit();
}

// Get next lesson
$stmt = $pdo->prepare("SELECT id, lesson_number FROM myanmar_lessons WHERE level = ? AND lesson_number > ? ORDER BY lesson_number ASC LIMIT 1");
$stmt->execute([$level, $lessonNumber]);
$nextLesson = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="my">
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
            text-align: center;
        }

        .stat-item div:first-child {
            color: #646669;
            font-size: 1rem;
            text-transform: lowercase;
        }

        .stat-value {
            color: #d1d0c5;
            font-weight: 600;
            font-size: 1.5rem;
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

        .key .myanmar {
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

.keyboard.theme-light .key .myanmar {
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
.keyboard.theme-light .key.blinking .myanmar {
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
                    <button class="theme-option" data-theme="sunset">Sunset</button>
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
       <div id="resultModal" class="modal">
        <div class="modal-content">
            <h2>Typing Test Results</h2>
            <p><strong>Accuracy:</strong> <span id="accuracy-result">0%</span></p>
            <p><strong>Speed:</strong> <span id="speed-result">0 WPM</span></p>
            <p><strong>Errors:</strong> <span id="errors-result">0</span></p>
            <p><strong>Time:</strong> <span id="time-result">0 s</span></p>
            <button onclick="location.reload()" class="btn-retry">
                <i class="fas fa-redo"></i> Try Again
            </button>
            <a href="myanmar_course.php" class="btn-course">
                <i class="fas fa-book"></i> Back to Course
            </a>
            <?php if ($nextLesson): ?>
                <a href="myanmar_lesson.php?level=<?php echo htmlspecialchars($level); ?>&lesson=<?php echo htmlspecialchars($nextLesson['lesson_number']); ?>&id=<?php echo htmlspecialchars($nextLesson['id']); ?>" 
                   id="next-btn" class="btn-next">
                    Next Lesson <i class="fas fa-arrow-right"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Character mapping for Myanmar input
        const myanmarKeyMap = {
            'q': 'ဆ', 'w': 'တ', 'e': 'န', 'r': 'မ', 't': 'အ', 'y': 'ပ', 'u': 'က',
            'i': 'င', 'o': 'သ', 'p': 'စ', '[': 'ဟ', ']': 'ဩ', '\\': '၏',
            'a': 'ေ', 's': 'ျ', 'd': 'ိ', 'f': '်', 'g': 'ါ', 'h': '့', 'j': 'ြ',
            'k': 'ု', 'l': 'ူ', ';': 'း', "'": '"',
            'z': 'ဖ', 'x': 'ထ', 'c': 'ခ', 'v': 'လ', 'b': 'ဘ', 'n': 'ည', 'm': 'ာ',
            ',': '၊', '.': '။', '/': '/',
            '1': '၁', '2': '၂', '3': '၃', '4': '၄', '5': '၅',
            '6': '၆', '7': '၇', '8': '၈', '9': '၉', '0': '၀'
        };

        // Replace the hardcoded practice text with lesson content from database
        const practiceText = <?php echo json_encode($lesson['content']); ?>;

        // Initialize variables
        let currentLetterIndex = 0;
        let mistakes = 0;
        let totalCharacters = 0;
        let startTime = null;
        let isTyping = false;

        // Create keyboard layout function
        function createKeyboard() {
            const keyboard = document.querySelector('.keyboard');
            const layout = [
                // First row
                [
                    { eng: '`', myan: '`', class: 'line' },
                    { eng: '1', myan: '၁' }, { eng: '2', myan: '၂' }, { eng: '3', myan: '၃' }, 
                    { eng: '4', myan: '၄' }, { eng: '5', myan: '၅' }, { eng: '6', myan: '၆' }, 
                    { eng: '7', myan: '၇' }, { eng: '8', myan: '၈' }, { eng: '9', myan: '၉' }, 
                    { eng: '0', myan: '၀' }, { eng: '-', myan: '-' }, { eng: '=', myan: '=' },
                    { eng: 'Backspace', myan: '', class: 'backspace' }
                ],
                // Second row
                [
                    { eng: 'Tab', myan: '', class: 'tab' },
                    { eng: 'q', myan: 'ဆ' }, { eng: 'w', myan: 'တ' }, { eng: 'e', myan: 'န' }, 
                    { eng: 'r', myan: 'မ' }, { eng: 't', myan: 'အ' }, { eng: 'y', myan: 'ပ' }, 
                    { eng: 'u', myan: 'က' }, { eng: 'i', myan: 'င' }, { eng: 'o', myan: 'သ' }, 
                    { eng: 'p', myan: 'စ' }, { eng: '[', myan: 'ဟ' }, { eng: ']', myan: 'ဩ' },
                    { eng: '\\', myan: '၏' }
                ],
                // Third row
                [
                    { eng: 'Caps', myan: '', class: 'caps' },
                    { eng: 'a', myan: 'ေ' }, { eng: 's', myan: 'ျ' }, { eng: 'd', myan: 'ိ' }, 
                    { eng: 'f', myan: '်' }, { eng: 'g', myan: 'ါ' }, { eng: 'h', myan: '့' }, 
                    { eng: 'j', myan: 'ြ' }, { eng: 'k', myan: 'ု' }, { eng: 'l', myan: 'ူ' }, 
                    { eng: ';', myan: 'း' }, { eng: "'", myan: '"' },
                    { eng: 'Enter', myan: '', class: 'enter' }
                ],
                // Fourth row
                [
                    { eng: 'Shift', myan: '', class: 'shift' },
                    { eng: 'z', myan: 'ဖ' }, { eng: 'x', myan: 'ထ' }, { eng: 'c', myan: 'ခ' }, 
                    { eng: 'v', myan: 'လ' }, { eng: 'b', myan: 'ဘ' }, { eng: 'n', myan: 'ည' }, 
                    { eng: 'm', myan: 'ာ' }, { eng: ',', myan: '၊' }, { eng: '.', myan: '။' }, 
                    { eng: '/', myan: '/' },
                    { eng: 'Shift', myan: '', class: 'shift' }
                ],
                // Fifth row
                [
                    
                    { eng: 'Space', myan: '', class: 'space' },
                  
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
                        key.eng === 'Caps' || key.eng === 'Enter' || key.eng === 'Shift' || 
                        key.eng === 'Ctrl' || key.eng === 'Win' || key.eng === 'Alt' || key.eng === 'Menu') {
                        keyElement.innerHTML = `<div class="english">${key.eng}</div>`;
                    } else {
                        keyElement.innerHTML = `
                            <div class="english">${key.eng}</div>
                            <div class="myanmar">${key.myan}</div>
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
                const mappedChar = myanmarKeyMap[event.key.toLowerCase()] || event.key;
                const targetChar = currentLetter.textContent;

                currentLetter.classList.remove('current');
                if (mappedChar === targetChar) {
                    currentLetter.classList.add('correct');
                } else {
                    currentLetter.classList.add('incorrect');
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
        }        // Finish practice
        function finishPractice() {
            isTyping = false;
            showResults();
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', () => {
            createKeyboard();
            initializePractice();
            
            const inputField = document.getElementById('input-field');
            document.addEventListener('keydown', handleTyping);
            
            document.getElementById('restart-button').addEventListener('click', initializePractice);

            // Keep input field focused
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

        // Update the input event listener
        document.getElementById('input-field').addEventListener('input', (e) => {
            if (!isTyping) {
                startTime = new Date();
                isTyping = true;
                startTimer();
            }

            const letters = document.querySelectorAll('.letter');
            const typed = e.target.value;

            if (typed && currentLetterIndex < letters.length) {
                totalCharacters++;
                const current = letters[currentLetterIndex];
                
                if (typed === current.textContent) {
                    current.classList.add('correct');
                    keySound.currentTime = 0;
                    keySound.play().catch(e => console.log('Sound play failed:', e));
                } else {
                    mistakes++;
                    current.classList.add('incorrect');
                    errorSound.currentTime = 0;
                    errorSound.play().catch(e => console.log('Sound play failed:', e));
                }

                current.classList.remove('current');
                
                if (letters[currentLetterIndex + 1]) {
                    letters[currentLetterIndex + 1].classList.add('current');
                }

                currentLetterIndex++;
                updateStats();
                e.target.value = '';

                // Check if typing is complete
                if (currentLetterIndex >= letters.length) {
                    finishPractice();
                }
            }
        });

        function showResults() {
            const timeElapsed = (new Date() - startTime) / 1000;
            const wpm = Math.round((totalCharacters / 5) / (timeElapsed / 60));
            const accuracy = Math.round(((totalCharacters - mistakes) / totalCharacters) * 100);

            // Update results in modal
            document.getElementById('accuracy-result').textContent = `${accuracy}%`;
            document.getElementById('speed-result').textContent = `${wpm} WPM`;
            document.getElementById('errors-result').textContent = mistakes;
            document.getElementById('time-result').textContent = `${timeElapsed.toFixed(1)} s`;

            // Show next button if accuracy is good enough
            const nextBtn = document.getElementById('next-btn');
            if (nextBtn && accuracy >= 80) {
                nextBtn.style.display = 'inline-block';
            }

            // Show modal
            const modal = document.getElementById('resultModal');
            if (modal) {
                modal.style.display = 'flex';
                modal.classList.add('show');
            }

            // Save progress to database via AJAX
            saveProgress(wpm, accuracy);
        }

        function saveProgress(wpm, accuracy) {
            const lessonId = <?php echo json_encode($lessonId); ?>;
            
            fetch('save_progress.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `lesson_id=${lessonId}&wpm=${wpm}&accuracy=${accuracy}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Progress saved successfully');
                }
            })
            .catch(error => console.error('Error saving progress:', error));
        }

        // Add this to check if clicking outside modal should close it
        window.addEventListener('click', (e) => {
            const modal = document.getElementById('resultModal');
            if (e.target === modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
            }
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
    </script>
</body>
</html> 