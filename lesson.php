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

$stmt = $pdo->prepare("SELECT * FROM lessons WHERE id = ?");
$stmt->execute([$lessonId]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lesson) {
    header('Location: course.php');
    exit();
}

// Get next lesson
$stmt = $pdo->prepare("SELECT id, lesson_number FROM lessons WHERE level = ? AND lesson_number > ? ORDER BY lesson_number ASC LIMIT 1");
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
            background: transparent;
            white-space: pre-wrap;
            word-wrap: break-word;
            word-break: break-all;
            overflow-wrap: break-word;
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
        .key.tab { width: 60px; }
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
        .key.line { width: 70px; }
        .key.backspace { width: 85px; }
 
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

        .results-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .results-content {
            background: #2c2c2c;
            border-radius: 10px;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            animation: slideIn 0.3s ease;
            border: 1px solid rgba(209, 208, 197, 0.1);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .results-header {
            text-align: center;
            margin-bottom: 2rem;
            color: #d1d0c5;
        }

        .results-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(25, 25, 25, 0.95);
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
        }

        .stat-icon {
            color: #4a9eff;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 600;
            color: #d1d0c5;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #646669;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        .feedback-message {
            text-align: center;
            padding: 1rem;
            margin: 1.5rem 0;
            border-radius: 8px;
        }

        .feedback-message.warning {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .feedback-message.success {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .results-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn-retry, .btn-course, .btn-next {
            background: linear-gradient(45deg, #007bff, #00ff88);
            border: none;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-retry {
            background: #646669;
            color: #d1d0c5;
        }

        .btn-course {
            background: #2c2c2c;
            color: #d1d0c5;
            border: 1px solid #646669;
        }

        .btn-next {
            background: #4a9eff;
            color: #ffffff;
        }

        .btn-retry:hover, .btn-course:hover, .btn-next:hover {
            transform: translateY(-2px);
            opacity: 0.9;
            color: #ffffff;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        }
       
        .modal-content {
            background-color: #323437;
          
            padding: 30px;
            margin: 10% auto;
            width: 400px;
            text-align: center;
            border-radius: 10px;
            border: 1px solid rgba(209, 208, 197, 0.1);
        }

        .modal-content h2 {
            
            margin-bottom: 20px;
            color: #007bff;
            font-size: 1.7rem;
        }

        .modal-content p {
            margin: 8px 0;
            font-size: 1.4rem;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 50px;
        }

        .modal-content strong {
            color: #4a9eff;
            width: 100px;
            font-size: 1.4rem;
        }

        .modal-content span {
            color: #d1d0c5;
            font-size: 1.4rem;
        }

     

        .modal-content button,
        .modal-content a {
            background: #323437;
            color: #d1d0c5;
            padding: 10px 20px;
            margin: 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .modal-content button:hover,
        .modal-content a:hover {
            background: #4a4a4a;
            transform: translateY(-2px);
        }

        /* Add these new styles */
        .modal.show {
            display: block !important;
        }

        .sound-toggle {
            background: none;
            border: none;
            color: #d1d0c5;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 10px;
            transition: all 0.3s ease;
            margin-left: 1rem; /* Add spacing between time and sound icon */
        }

        .sound-toggle:hover {
            color: #4a9eff;
        }

        .sound-toggle.muted {
            color: #646669;
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
                    <button class="theme-option" data-theme="dark" data-color="#323437">Dark</button>
                    <button class="theme-option" data-theme="light" data-color="#f0f0f0">Light</button>
                    <button class="theme-option" data-theme="midnight" data-color="#1a1b26">Midnight</button>
                    <button class="theme-option" data-theme="forest" data-color="#2b2f2b">Forest</button>
                    <button class="theme-option" data-theme="sunset" data-color="#2d1b2d">Violet</button>
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
                <div class="key line" data-key="\">\</div>
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

    <div class="results-overlay" id="resultsOverlay">
        <div class="results-card">
            <h2>Typing Test Results</h2>
            <div class="result-stat">
                <span>Speed[WPM]:</span>
                <span class="result-value" id="finalWpm">0</span>
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
                <button class="restart-button" onclick="location.reload()">
                    <i class="fas fa-redo me-2"></i>Again
                </button>
                <a href="course.php" class="restart-button">
                    <i class="fas fa-book me-2"></i>Back to Course
                </a>
                <?php if ($nextLesson): ?>
                    <a href="lesson.php?level=<?php echo htmlspecialchars($level); ?>&lesson=<?php echo htmlspecialchars($nextLesson['lesson_number']); ?>&id=<?php echo htmlspecialchars($nextLesson['id']); ?>" 
                       class="restart-button">
                        <i class="fas fa-arrow-right me-2"></i>Next
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="resultModal" class="modal">
        <div class="modal-content">
            <h2>Typing Results</h2>
            <p><strong>Accuracy:</strong> <span id="accuracy-result">0%</span></p>
            <p><strong>Speed[WPM]:</strong> <span id="speed-result">0</span></p>
            <p><strong>Errors:</strong> <span id="errors-result">0</span></p>
            <p><strong>Time:</strong> <span id="time-result">0 s</span></p>
            <button onclick="location.reload()" class="btn-retry">
                <i class="fas fa-redo"></i> Try Again
            </button>
            <a href="course.php" class="btn-course">
                <i class="fas fa-book"></i> Back to Course
            </a>
            <?php if ($nextLesson): ?>
            <a href="lesson.php?level=<?php echo $level; ?>&lesson=<?php echo $nextLesson['lesson_number']; ?>&id=<?php echo $nextLesson['id']; ?>" 
               id="next-btn" class="btn-next">
                Next Lesson <i class="fas fa-arrow-right"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <audio id="keySound" preload="auto">
        <source src="assets/sounds/key-press.mp3" type="audio/mpeg">
    </audio>
    <audio id="errorSound" preload="auto">
        <source src="assets/sounds/error.mp3" type="audio/mpeg">
    </audio>

    <script>
        document.addEventListener('keydown', (e) => {
            if (e.target === document.getElementById('input-field') || 
                e.ctrlKey || e.altKey || e.metaKey) {
                return;
            }
            
            document.getElementById('input-field').focus();
        });

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
        const CHARS_BEFORE_SCROLL = 55; // Scroll every 20 characters typed

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

        // Replace your existing showResults function with this updated version
        function showResults() {
            const timeElapsed = (new Date() - startTime) / 1000;
            const wpm = Math.round((currentIndex / 5) / (timeElapsed / 60));
            const accuracy = Math.round(((totalChars - mistakes) / totalChars) * 100);

            document.getElementById('finalWpm').textContent = wpm;
            document.getElementById('finalAccuracy').textContent = accuracy + '%';
            document.getElementById('finalTime').textContent = timeElapsed.toFixed(1) + 's';
            document.getElementById('finalErrors').textContent = mistakes;
            
            const resultsOverlay = document.getElementById('resultsOverlay');
            if (resultsOverlay) {
                resultsOverlay.style.display = 'flex';
            }

            // Save progress to database
            fetch('save_progress.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    lesson_id: <?php echo json_encode($lessonId); ?>,
                    wpm: wpm,
                    accuracy: accuracy,
                    mistakes: mistakes,
                    time_taken: timeElapsed
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Failed to save progress:', data.message);
                }
            })
            .catch(error => {
                console.error('Error saving progress:', error);
            });
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
                    keySound.currentTime = 0;
                    keySound.play().catch(e => console.log('Sound play failed:', e));
                } else {
                    mistakes++;
                    current.classList.add('incorrect');
                    errorSound.currentTime = 0;
                    errorSound.play().catch(e => console.log('Sound play failed:', e));
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
                    showResults();
                }
            }
        });

        // Handle focus when clicking anywhere on the typing area
        document.querySelector('.typing-area').addEventListener('click', () => {
            document.getElementById('input-field').focus();
        });

        // Add event listener for the restart button
        document.getElementById('restart-button').addEventListener('click', () => {
            location.reload();
        });

        // Add event listener to close modal when clicking outside
        document.getElementById('resultsOverlay').addEventListener('click', (e) => {
            if (e.target === document.getElementById('resultsOverlay')) {
                document.getElementById('resultsOverlay').style.display = 'none';
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
    <script src="assets/js/lesson.js"></script>
</body>

