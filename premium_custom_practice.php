<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is premium
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check premium status
$stmt = $pdo->prepare("SELECT is_premium FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['is_premium'] != 1) {
    header('Location: transaction/index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Practice - Premium Feature</title>
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
            min-height: 140px;
            margin-bottom: 2rem;
            padding: 20px;
            background: rgba(32, 34, 37, 0.95);
            border-radius: 10px;
            border: 1px solid rgba(209, 208, 197, 0.1);
        }

        .words {
            user-select: none;
            font-size: 1.5rem;
            line-height: 1.5;
            margin-bottom: 1rem;
            min-height: 120px;
            max-height: 150px; /* Reduced max height */
            overflow-y: auto;
            position: relative;
            padding: 10px;
            border-radius: 8px;
            background: rgba(38, 40, 43, 0.5);
            white-space: pre-wrap; /* Preserve line breaks */
            word-wrap: break-word; /* Break long words */
        }

        .words::-webkit-scrollbar {
            width: 8px;
        }

        .words::-webkit-scrollbar-track {
            background: rgba(38, 40, 43, 0.95);
            border-radius: 4px;
        }

        .words::-webkit-scrollbar-thumb {
            background: #646669;
            border-radius: 4px;
        }

        .words::-webkit-scrollbar-thumb:hover {
            background: #4a4a4a;
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
            max-width: 850px;
            background: rgba(25, 25, 25, 0.95);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .keyboard-row {
            display: flex;
            justify-content: center;
            margin-bottom: 7px;
            gap: 5px;
        }

        .key {
            width: 44px;
            height: 44px;
            background: #3c3c3c;
            border: none;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.1s ease;
            position: relative;
            box-shadow: 0 2px 0 #262626;
        }

        .key:active, .key.active {
            transform: translateY(2px);
            box-shadow: 0 0 0 #262626;
            background: #4a9eff;
            color: #ffffff;
        }

        .key.tab { width: 75px; }
        .key.caps { width: 85px; }
        .key.enter { width: 90px; }
        .key.shift { width: 105px; }
        .key.ctrl, .key.win, .key.alt { width: 60px; }
        .key.menu { width: 60px; }
        .key.space { width: 320px; }
        .key.delete { width: 85px; }

        .key.special {
            color: #a0a0a0;
            font-size: 0.75rem;
        }

        .key.tab, .key.caps, .key.shift, .key.ctrl, 
        .key.win, .key.alt, .key.menu, .key.enter,
        .key.delete {
            font-size: 0.7rem;
            text-transform: uppercase;
            background: #333333;
            color: #a0a0a0;
        }

        .input-field {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .custom-textarea {
            width: 100%;
            min-height: 150px;
            background: rgba(38, 40, 43, 0.95);
            border: 1px solid rgba(209, 208, 197, 0.1);
            border-radius: 10px;
            color: #d1d0c5;
            padding: 15px;
            font-family: 'Roboto Mono', monospace;
            margin-bottom: 20px;
            font-size: 1.2rem;
            resize: vertical;
        }

        .action-button {
            background: none;
            border: 1px solid #e2b714;
            color: #e2b714;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .action-button:hover {
            background: rgba(226, 183, 20, 0.1);
            transform: translateY(-2px);
        }

        .setup-area {
            background: rgba(32, 34, 37, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(209, 208, 197, 0.1);
        }

        .theme-selector-practice {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: none;
        }

        .theme-button {

            margin-top: 50px;
            background: #2c2e31;
            color: #d1d0c5;
            border: 2px solid #4a4a4a;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .theme-button:hover {
            background: #3c3c3c;
            border-color: #5c5c5c;
            transform: translateY(-1px);
        }

        .theme-button i {
            font-size: 1rem;
            color: #e2b714;
        }

        .theme-options {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background: #2c2e31;
            border: 2px solid #4a4a4a;
            border-radius: 8px;
            padding: 8px;
            display: none;
            min-width: 150px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .theme-options.show {
            display: block;
            animation: fadeIn 0.2s ease;
        }

        .theme-option {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 8px 16px;
            background: none;
            border: none;
            color: #d1d0c5;
            cursor: pointer;
            text-align: left;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .theme-option:hover {
            background: #3c3c3c;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes blink {
            50% { opacity: 0; }
        }

        /* Theme styles */
        /* Add these theme styles for body and navbar */
        body.theme-light {
            background-color: #e0e0e0;
        }
        body.theme-midnight {
            background-color: #1a1b26;
        }
        body.theme-forest {
            background-color: #2b2f2b;
        }
        body.theme-sunset {
            background-color: #2d1b2d;
        }

        /* Navbar theme styles */
        .navbar.theme-light {
            background-color: #f0f0f0 !important;
        }
        .navbar.theme-light .nav-link,
        .navbar.theme-light .navbar-brand {
            color: #2c2c2c !important;
        }
        
        .navbar.theme-midnight {
            background-color: #24283b !important;
        }
        .navbar.theme-midnight .nav-link,
        .navbar.theme-midnight .navbar-brand {
            color: #7aa2f7 !important;
        }
        
        .navbar.theme-forest {
            background-color: #1e231f !important;
        }
        .navbar.theme-forest .nav-link,
        .navbar.theme-forest .navbar-brand {
            color: #95c085 !important;
        }
        
        .navbar.theme-sunset {
            background-color: #1f1520 !important;
        }
        .navbar.theme-sunset .nav-link,
        .navbar.theme-sunset .navbar-brand {
            color: #f67e7d !important;
        }
        .keyboard.theme-light {
            background: #e0e0e0;
        }
        .keyboard.theme-light .key {
            background: #f0f0f0;
            color: #2c2c2c;
            box-shadow: 0 2px 0 #c0c0c0;
        }
        .keyboard.theme-light .key:active, 
        .keyboard.theme-light .key.active {
            background: #4a9eff;
            color: #ffffff;
            box-shadow: 0 0 0 #c0c0c0;
        }
        .keyboard.theme-light .key.special,
        .keyboard.theme-light .key.tab, 
        .keyboard.theme-light .key.caps, 
        .keyboard.theme-light .key.shift, 
        .keyboard.theme-light .key.ctrl, 
        .keyboard.theme-light .key.win, 
        .keyboard.theme-light .key.alt, 
        .keyboard.theme-light .key.menu, 
        .keyboard.theme-light .key.enter {
            background: #d8d8d8;
            color: #505050;
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
        <div id="setup-area" class="setup-area">
            <h2 class="text-center mb-4">Custom Practice Text</h2>
            <textarea id="customText" class="custom-textarea" placeholder="Enter your practice text here..."></textarea>
            <button class="action-button w-100" onclick="startPractice()">Start Practice</button>
        </div>

        <div class="theme-selector-practice" id="theme-selector-practice">
            <button class="theme-button" id="theme-toggle">
                <i class="fas fa-palette"></i>
                Theme
            </button>
            <div class="theme-options" id="theme-options">
                <button class="theme-option" data-theme="dark">Dark</button>
                <button class="theme-option" data-theme="light">Light</button>
                <button class="theme-option" data-theme="midnight">Midnight</button>
                <button class="theme-option" data-theme="forest">Forest</button>
                <button class="theme-option" data-theme="sunset">Sunset</button>
            </div>
        </div>

        <div id="practice-area" class="typing-area" style="display: none;">
            <div class="stats-container">
                <div class="stat-item">wpm: <span class="stat-value" id="wpm">0</span></div>
                <div class="stat-item">acc: <span class="stat-value" id="accuracy">100%</span></div>
                <div class="stat-item">time: <span class="stat-value" id="time">0</span></div>
            </div>

            <div class="words" id="words"></div>
            <input type="text" class="input-field" id="input-field" autocomplete="off">

            <div class="text-center mt-4">
                <button class="action-button" onclick="resetPractice()">
                    <i class="fas fa-redo me-2"></i>Reset
                </button>
            </div>
        </div>

        <div class="keyboard" id="keyboard" style="display: none;">
            <div class="keyboard-row">
                <div class="key special" data-key="`">`</div>
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
                <div class="key special" data-key="-                <div class="key special" data-key="-">-</div>
                <div class="key special" data-key="=">=</div>
                <div class="key delete special" data-key="Backspace">delete</div>
            </div>
            <div class="keyboard-row">
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
                <div class="key caps special" data-key="CapsLock">caps</div>
                <div class="key" data-key="a">a</div>
                <div class="key" data-key="s">s</div>
                <div class="key" data-key="d">d</div>
                <div class="key" data-key="f">f</div>
                <div class="key" data-key="g">g</div>
                <div class="key" data-key="h">h</div>
                <div class="key" data-key="j">j</div>
                <div class="key" data-key="k">k</div>
                <div class="key" data-key="l">l</div>
                <div class="key special" data-key=";">;</div>
                <div class="key special" data-key="'">'</div>
                <div class="key enter special" data-key="Enter">enter</div>
            </div>
            <div class="keyboard-row">
                <div class="key shift" data-key="Shift">shift</div>
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
                <div class="key shift" data-key="Shift">shift</div>
            </div>
            <div class="keyboard-row">
                <div class="key ctrl" data-key="Control">ctrl</div>
                <div class="key win" data-key="Meta">win</div>
                <div class="key alt" data-key="Alt">alt</div>
                <div class="key space" data-key=" ">space</div>
                <div class="key alt" data-key="Alt">alt</div>
                <div class="key win" data-key="Meta">win</div>
                <div class="key menu" data-key="ContextMenu">menu</div>
                <div class="key ctrl" data-key="Control">ctrl</div>
            </div>
        </div>
    </div>

    <!-- Add this before closing </head> tag -->
    <script src="js/typing-sounds.js"></script>

    <!-- Update the script section -->
    <script>
        const typingSounds = new TypingSounds();
        let startTime, timer;
        let currentIndex = 0;
        let mistakes = 0;
        let isTyping = false;
        let totalChars = 0;

        function startPractice() {
            const text = document.getElementById('customText').value.trim();
            if (!text) {
                alert('Please enter some text to practice');
                return;
            }

            document.getElementById('setup-area').style.display = 'none';
            document.getElementById('practice-area').style.display = 'block';
            document.getElementById('keyboard').style.display = 'block';
            document.getElementById('theme-selector-practice').style.display = 'block';
            
            const words = document.getElementById('words');
            words.innerHTML = text.split('').map((char, i) => 
                `<span class="letter ${i === 0 ? 'current' : ''}">${char}</span>`
            ).join('');

            document.getElementById('input-field').focus();
        }

        function resetPractice() {
            document.getElementById('setup-area').style.display = 'block';
            document.getElementById('practice-area').style.display = 'none';
            document.getElementById('keyboard').style.display = 'none';
            document.getElementById('theme-selector-practice').style.display = 'none';
            document.getElementById('wpm').textContent = '0';
            document.getElementById('accuracy').textContent = '100%';
            document.getElementById('time').textContent = '0';
            document.getElementById('customText').value = '';
            
            currentIndex = 0;
            mistakes = 0;
            isTyping = false;
            totalChars = 0;
            clearInterval(timer);
        }

        document.getElementById('input-field').addEventListener('input', (e) => {
            if (!isTyping) {
                startTime = new Date();
                isTyping = true;
                timer = setInterval(updateTimer, 1000);
            }
    
            const letters = document.querySelectorAll('.letter');
            const typed = e.target.value;
    
            if (typed && currentIndex < letters.length) {
                totalChars++;
                const current = letters[currentIndex];
                
                if (typed === current.textContent) {
                    current.classList.add('correct');
                    typingSounds.playKeyPress();
                } else {
                    mistakes++;
                    current.classList.add('incorrect');
                    typingSounds.playError();
                }
    
                current.classList.remove('current');
                if (letters[currentIndex + 1]) {
                    letters[currentIndex + 1].classList.add('current');
                    // Auto-scroll to keep the current letter in view
                    const wordsContainer = document.getElementById('words');
                    const nextLetter = letters[currentIndex + 1];
                    const containerRect = wordsContainer.getBoundingClientRect();
                    const letterRect = nextLetter.getBoundingClientRect();
                    
                    // Scroll when the letter is in the bottom third of the container
                    if (letterRect.bottom > containerRect.bottom - containerRect.height / 3) {
                        wordsContainer.scrollTop += containerRect.height / 2;
                    }
                }
    
                currentIndex++;
                updateStats();
                e.target.value = '';
    
                if (currentIndex >= letters.length) {
                    clearInterval(timer);
                    typingSounds.playCompletion();
                }
            }
        });
    
        function updateStats() {
            const timeElapsed = (new Date() - startTime) / 1000 / 60;
            const wpm = Math.round((currentIndex / 5) / timeElapsed);
            const accuracy = Math.round(((totalChars - mistakes) / totalChars) * 100);
    
            document.getElementById('wpm').textContent = wpm || 0;
            document.getElementById('accuracy').textContent = accuracy || 100 + '%';
        }
    
        function updateTimer() {
            const timeElapsed = Math.round((new Date() - startTime) / 1000);
            document.getElementById('time').textContent = timeElapsed;
        }
    
        function changeTheme(theme) {
            // Update body class
            document.body.className = theme === 'dark' ? '' : `theme-${theme}`;
            
            // Update keyboard class
            document.querySelector('.keyboard').className = `keyboard ${theme === 'dark' ? '' : `theme-${theme}`}`;
            
            // Update navbar class
            const navbar = document.querySelector('.navbar');
            navbar.className = `navbar navbar-expand-lg ${theme === 'dark' ? '' : `theme-${theme}`}`;
        }
    
        // Theme toggle functionality
        document.getElementById('theme-toggle').addEventListener('click', () => {
            document.getElementById('theme-options').classList.toggle('show');
        });
    
        // Close theme options when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.theme-selector-practice')) {
                document.getElementById('theme-options').classList.remove('show');
            }
        });
    
        // Theme option click handlers
        document.querySelectorAll('.theme-option').forEach(option => {
            option.addEventListener('click', () => {
                const theme = option.dataset.theme;
                changeTheme(theme);
                document.getElementById('theme-options').classList.remove('show');
            });
        });
        

        // Add keyboard highlighting
        document.addEventListener('keydown', (e) => {
            const key = document.querySelector(`.key[data-key="${e.key}"]`);
            if (key) key.classList.add('active');
        });

        document.addEventListener('keyup', (e) => {
            const key = document.querySelector(`.key[data-key="${e.key}"]`);
            if (key) key.classList.remove('active');
        });
        
        
    </script>
</body>
</html>