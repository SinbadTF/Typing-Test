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
$lang = $_GET['lang'] ?? 'en';

$stmt = $pdo->prepare("SELECT * FROM lessons WHERE language = ? AND level = ? AND lesson_number = ?");
$stmt->execute([$lang, $level, $lessonNumber]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lesson) {
    header('Location: course.php');
    exit();
}

// Get next lesson
$stmt = $pdo->prepare("SELECT id, lesson_number FROM lessons WHERE level = ? AND lesson_number > ? ORDER BY lesson_number ASC LIMIT 1");
$stmt->execute([$level, $lessonNumber]);
$nextLesson = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user's completed lessons
$stmt = $pdo->prepare("SELECT completed_lessons FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$completedLessons = isset($user['completed_lessons']) ? explode(',', $user['completed_lessons']) : [];

// Define lessons
$lessons = [
    1 => [
        'title' => 'Home Row Keys',
        'description' => 'Learn the basic home row keys position',
        'link' => 'lesson1.php'
    ],
    2 => [
        'title' => 'Upper Row Keys',
        'description' => 'Practice typing with upper row keys',
        'link' => 'lesson2.php'
    ],
    3 => [
        'title' => 'Lower Row Keys',
        'description' => 'Master the lower row keys',
        'link' => 'lesson3.php'
    ],
    // Add more lessons as needed
];

// Add language-specific titles
$languageTitles = [
    'en' => 'English Typing Lesson',
    'my' => 'မြန်မာစာ စာရိုက်လေ့ကျင့်ခန်း',
    'jp' => '日本語タイピング練習'
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
            max-height: 150px !important;
            overflow-y: auto !important;
            position: relative;
            padding: 10px;
            border-radius: 8px;
            background: rgba(38, 40, 43, 0.5);
            white-space: pre-wrap;
            word-wrap: break-word;
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
            max-width: 900px; /* Slightly increased max-width */
            background: rgba(25, 25, 25, 0.95);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .keyboard-row {
            display: flex;
            justify-content: center;
            margin-bottom: 8px; /* Slightly increased gap between rows */
            gap: 6px; /* Increased gap between keys */
        }

        /* Adjust key sizes */
        .key.tab { width: 82px; }
        .key.caps { width: 92px; }
        .key.enter { width: 98px; }
        .key.shift { width: 120px; }
        .key.ctrl, .key.win, .key.alt { width: 65px; }
        .key.menu { width: 65px; }
        .key.space { width: 350px; }
        .key.delete { width: 98px; }

        /* Add this for first row alignment */
        .keyboard-row:first-child {
            padding-left: 0;
        }

        /* Add this for better key proportions */
        .key {
            width: 45px;
            height: 45px;
            padding: 5px;
            margin: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #444;
            color: white;
            cursor: pointer;
            transition: background-color 0.2s;
            border-radius: 5px;
        }

        .key.line { width: 22px; }
        .key.backspace { width: 89px; }
        .key.tab { width: 60px; }
        .key.enter { width: 85px; }
        .key.caps { width: 80px; }
        .key.shift { width: 110px; }
        .key.space { width: 700px; }

        .key:hover {
            background-color: #666;
        }

        .key:active, .key.active {
            transform: translateY(2px);
            box-shadow: 0 0 0 #262626;
            background: #4a9eff;
            color: #ffffff;
        }

        .keyboard {
            margin: 30px auto;
            max-width: 800px;
            background: rgba(45, 45, 45, 0.95);
            padding: 20px;
            border-radius: 8px;
        }

        .keyboard-row {
            display: flex;
            justify-content: center;
            gap: 4px;
            margin-bottom: 4px;
        }

        .key {
            width: 40px;
            height: 40px;
            background: #2c2c2c;
            border: none;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #d1d0c5;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .key:active, .key.active {
            background: #007bff;
            color: #ffffff;
        }

        /* Special key sizes */
        .key.line { width: 40px; }
        .key.backspace { width: 85px; }
        .key.tab { width: 85px; }
        .key.caps { width: 85px; }
        .key.enter { width: 90px; }
        .key.shift { width: 110px; }
        .key.ctrl, .key.win, .key.alt { width: 60px; }
        .key.space { width: 350px; }

        /* Special key colors */
        .key.special,
        .key.tab,
        .key.caps,
        .key.shift,
        .key.ctrl,
        .key.win,
        .key.alt,
        .key.menu,
        .key.enter {
            background: #262626;
            color: #808080;
            font-size: 0.75rem;
        }

        .key.delete {
            width: 85px;
            background: #333333;
            color: #a0a0a0;
            font-size: 0.7rem;
            text-transform: uppercase;
        }

        .typing-area {
            margin-bottom: 3rem;
            background: rgba(25, 25, 25, 0.95);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        /* Add special key styling */
        .key.tab, .key.caps, .key.shift, .key.ctrl, 
        .key.win, .key.alt, .key.menu, .key.enter {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #868686;
            background: rgba(39, 41, 44, 0.95);
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
            text-decoration: none;
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

        @keyframes blink {
            50% { opacity: 0; }
        }
        .header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    position: relative;
    padding: 0 1rem;
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
    z-index: 100;
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
                        
                    .keyboard.theme-light .key {
                        background: #ffffff;
                        color: #333333;
                        box-shadow: 0 2px 0 #cccccc;
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
            margin-top: 50px;
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(202, 71, 84, 0.9);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: none;
            animation: fadeIn 0.3s ease;
            z-index: 1000;
        }

        .caps-warning.show {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Add scrollbar styling */
        .words::-webkit-scrollbar {
            width: 8px !important;
            display: block !important;
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
                    <button class="theme-option" data-theme="dark" data-color="#323437">Dark</button>
                    <button class="theme-option" data-theme="light" data-color="#f0f0f0">Light</button>
                    <button class="theme-option" data-theme="midnight" data-color="#1a1b26">Midnight</button>
                    <button class="theme-option" data-theme="forest" data-color="#2b2f2b">Forest</button>
                    <button class="theme-option" data-theme="sunset" data-color="#2d1b2d">Sunset</button>
                </div>
            </div>
        </div>

        <div class="typing-area">
            <div class="words" id="words"></div>
            <input type="text" class="input-field" id="input-field">
        </div>

        <div class="keyboard">
            <div class="keyboard-row">
                
                <div class="key " data-key="`">`</div>
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
                <div class="key " data-key="-">-</div>
                <div class="key " data-key="=">=</div>
                <div class="key delete special" data-key="Backspace">delete</div>
            </div>
            <div class="keyboard-row">
                <div class="key tab " data-key="tab">  tab</div>
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
                <div class="key" data-key="\">]\</div>
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
                <div class="key " data-key=";">;</div>
                <div class="key " data-key="'">'</div>
                <div class="key enter special" data-key="Enter">enter</div>
            </div>
            <div class="keyboard-row">
                <div class="key shift" data-key="Shift">Shift</div>
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
                <div class="key shift" data-key="Shift">Shift</div>
            </div>
            <div class="keyboard-row">
                
                <div class="key space" data-key=" ">space</div>
                
            </div>
        </div>

        <div class="text-center">
            <button class="restart-button" id="restart-button">
                <i class="fas fa-redo me-2"></i>restart lesson
            </button>
            <?php if ($nextLesson): ?>
            <a href="lesson.php?level=<?php echo $level; ?>&lesson=<?php echo $nextLesson['lesson_number']; ?>&id=<?php echo $nextLesson['id']; ?>" 
               id="next-btn" class="restart-button ms-3" style="display: none;">
                next lesson <i class="fas fa-arrow-right ms-2"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('keydown', function(event) {
        if (event.getModifierState('CapsLock')) {
            document.getElementById('capsWarning').classList.add('show');
        }
    });

    document.addEventListener('keyup', function(event) {
        if (!event.getModifierState('CapsLock')) {
            document.getElementById('capsWarning').classList.remove('show');
        }
    });
        // Get the lesson text from the database as a plain string
        const lessonText = <?php echo json_encode($lesson['content']); ?>;
        
        // Initialize variables
        let isTyping = false;
        let currentIndex = 0;
        let startTime;
        let timer;
        let totalChars = 0;
        let mistakes = 0;
        let lastScrollPosition = 0;
        let typedCharCount = 0; // New counter for typed characters
        const CHARS_BEFORE_SCROLL = 40; // Scroll every 20 characters typed

        // Initialize the words container with the lesson text
        document.addEventListener('DOMContentLoaded', () => {
            const wordsContainer = document.getElementById('words');
            const inputField = document.getElementById('input-field');
            
            // Initialize text content
            const text = lessonText.split('').map(char => {
                const span = document.createElement('span');
                span.textContent = char;
                span.className = 'letter';
                return span;
            });
            
            wordsContainer.innerHTML = '';
            text.forEach(span => wordsContainer.appendChild(span));
            
            // Set initial current letter
            if (text.length > 0) {
                text[0].classList.add('current');
            }
            
            // Handle backspace key
            inputField.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && currentIndex > 0) {
                    e.preventDefault();
                    currentIndex--;
                    
                    const letters = document.querySelectorAll('.letter');
                    const current = letters[currentIndex];
                    const next = letters[currentIndex + 1];
                    
                    // Remove classes from current letter
                    current.classList.remove('correct', 'incorrect');
                    current.classList.add('current');
                    
                    // Remove current class from next letter
                    if (next) {
                        next.classList.remove('current');
                    }
                    
                    // Update stats
                    if (current.classList.contains('incorrect')) {
                        mistakes--;
                    }
                    totalChars--;
                    updateStats();
                }
            });
            
            wordsContainer.style.overflowY = 'auto';
            wordsContainer.style.maxHeight = '150px';
            
            setTimeout(() => {
                inputField.focus();
            }, 100);
        });

        // Add updateTimer function
        function updateTimer() {
            const timeElapsed = Math.round((new Date() - startTime) / 1000);
            const minutes = Math.floor(timeElapsed / 60);
            const seconds = timeElapsed % 60;
            document.getElementById('time').textContent = 
                `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }

        // Add updateStats function
        function updateStats() {
            const timeElapsed = (new Date() - startTime) / 1000 / 60; // in minutes
            const wpm = Math.round((currentIndex / 5) / timeElapsed);
            const accuracy = Math.round(((totalChars - mistakes) / totalChars) * 100);
            
            document.getElementById('wpm').textContent = wpm || 0;
            document.getElementById('accuracy').textContent = `${accuracy || 100}%`;
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
                typedCharCount++; // Increment typed character count
                const current = letters[currentIndex];
                
                if (typed === current.textContent) {
                    current.classList.add('correct');
                } else {
                    mistakes++;
                    current.classList.add('incorrect');
                }



                current.classList.remove('current');
                
                if (letters[currentIndex + 1]) {
                    letters[currentIndex + 1].classList.add('current');
                    
                    // Scroll based on number of characters typed
                    if (typedCharCount % CHARS_BEFORE_SCROLL === 0) {
                        const wordsContainer = document.getElementById('words');
                        wordsContainer.scrollBy({
                            top: 25, // Small scroll amount
                            behavior: 'smooth'
                        });
                    }
                }

                currentIndex++;
                updateStats();
                e.target.value = '';

                if (currentIndex >= letters.length) {
                    clearInterval(timer);
                    const nextBtn = document.getElementById('next-btn');
                    if (nextBtn) nextBtn.style.display = 'inline-block';
                }
            }
        });

        // Handle focus when clicking anywhere on the typing area
        document.querySelector('.typing-area').addEventListener('click', () => {
            document.getElementById('input-field').focus();
        });
    </script>
    <script src="assets/js/lesson.js"></script>
</body>
</html>