<?php
        include("database.php");

    // Fetch a random lesson
        $sql = "SELECT text FROM lesson WHERE difficulty = 'medium' ORDER BY RAND() LIMIT 1";
        $result = $conn->query($sql);

    if (!$result) {
        die("Query failed: " . $conn->error); // Output the error if the query fails
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lessonText = $row['text'];
        echo "<p id='test-text'> $lessonText</p>"; // Output the lesson text
    } else {
        die("No lessons found in the database."); // Handle the case where no lessons are found
    }
    // Close the connection
    $conn->close()
    ?>
<!DOCTYPE html>
<link rel="stylesheet" href="assets/css/style.css">
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Typing Practice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Add these new styles */
        .navbar {
            
            background: rgba(45, 45, 45, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin: 0; /* Add this */
        }
        body {
            background-color: #1a1a1a;
            color: #ffffff;
            min-height: 100vh;
            margin: 0; /* Add this */
            padding: 0; /* Add this */
        }
        .nav-link {
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            background: rgba(0, 123, 255, 0.1);
            transform: translateY(-2px);
        }
        .nav-link.active {
            background: rgba(0, 123, 255, 0.2);
            color: #007bff !important;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #007bff;
        }
        .nav-link {
            color: #ffffff;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #007bff;
            transform: translateY(-2px);
        }
        body {
            background-color: #1a1a1a;
            color: #ffffff;
            min-height: 100vh;
        }
        .typing-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: rgba(45, 45, 45, 0.95);
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0,0,0,0.5);
        }
        .text-display {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 20px;
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            white-space: pre-wrap;
        }
        .input-field {
            width: 100%;
            padding: 15px;
            background: #363636;
            border: 2px solid #404040;
            color: #fff;
            border-radius: 8px;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
        .input-field:focus {
            outline: none;
            border-color: #007bff;
        }
        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .stat-box {
            background: rgba(0, 123, 255, 0.1);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            flex: 1;
            margin: 0 10px;
        }
        .stat-box h4 {
            color: #007bff;
            margin: 0;
            font-size: 0.9rem;
        }
        .stat-box p {
            font-size: 1.5rem;
            margin: 5px 0 0;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .correct {
            color: #28a745;
        }
        .incorrect {
            color: #dc3545;
            text-decoration: underline;
        }
        .current {
            background-color: rgba(0, 123, 255, 0.2);
        }
        .keyboard-container {
            margin-top: 30px;
            text-align: center;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }
        .main-keyboard {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .keyboard-row {
            display: flex;
            justify-content: center;
            gap: 4px;
        }
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

.key.line{
    width: 22px;
}
.key.backspace{
    width: 89px;
    height: 45px;
}
.key.tab{
    width: 60px;
}
.key.enter{
    width: 85px;
}
.key.caps{
    width:80px;
}

.key.shift{
    width: 110px;
}

.space{
    width: 700px;
    height: 45px;

}

.key:hover {
    background-color: #666;
}
.key.function {
            font-size: 0.8rem;
            background: #2d2d2d;
        }
        .key.active {
            background: #007bff;
            border-color: #0056b3;
            transform: scale(0.95);
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.5);
        }

        /*.key {
            background: #363636;
            border: 1px solid #404040;
            border-radius: 4px;
            color: #fff;
            padding: 8px;
            min-width: 40px;
            height: 40px;
            text-align: center;
            transition: all 0.1s;
            user-select: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }
        .key.active {
            background: #007bff;
            border-color: #0056b3;
            transform: scale(0.95);
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.5);
        }
        .key.space {
            width: 200px;
        }
        .key.wide {
            min-width: 60px;
        }
        .key.extra-wide {
            min-width: 80px;
        }
        .key.function {
            font-size: 0.8rem;
            background: #2d2d2d;
        }
        .numpad {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
            padding-left: 20px;
            border-left: 1px solid #404040;
        }
        .key.num-wide {
            grid-column: span 2;
        }*/
        .keyboard-theme-dark .key {
            background: #363636;
            border-color: #404040;
            color: #fff;
        }
        .keyboard-theme-dark .key.function {
            background: #2d2d2d;
        }
        
        .keyboard-theme-light .key {
            background: #f8f9fa;
            border-color: #dee2e6;
            color: #212529;
        }
        .keyboard-theme-light .key.function {
            background: #e9ecef;
        }
        .keyboard-theme-light .key.active {
            background: #007bff;
            color: white;
        }
        
        .keyboard-theme-neon .key {
            background: #1a1a1a;
            border-color: #00ff88;
            color: #00ff88;
            box-shadow: 0 0 5px rgba(0, 255, 136, 0.3);
        }
        .keyboard-theme-neon .key.function {
            border-color: #007bff;
            color: #007bff;
        }
        .keyboard-theme-neon .key.active {
            background: #00ff88;
            color: #1a1a1a;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.8);
        }
    </style>
    
    <!-- Add this audio element before closing </head> tag -->
    <audio id="keySound" preload="auto">
        <source src="sounds/key-press.mp3" type="audio/mpeg">
    </audio>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-keyboard me-2"></i>Typing Guru</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="themeToggle">
                            <i class="fas fa-palette me-2"></i>Change Theme
                        </a>
                    </li>
                </ul>
                <!--<div class="navbar-nav">
                        <a class="nav-link d-flex align-items-center" href="profile.php">
                            <?php if (isset($_SESSION['profile_image']) && $_SESSION['profile_image']): ?>
                                <img src="uploads/profile_images/<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" 
                                     class="rounded-circle me-2" 
                                     width="32" 
                                     height="32" 
                                     alt="Profile"
                                     style="object-fit: cover; border: 2px solid #007bff;">
                            <?php else: ?>
                                <i class="fas fa-user-circle me-2 fs-4"></i>
                            <?php endif; ?>
                            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        </a>
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </div>-->
                </div>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 80px;">
        <div class="typing-container">
            <h2 class="text-center mb-4">Typing Practice</h2>
            
            <div class="stats-container">
                <div class="stat-box">
                    <h4>Time</h4>
                    <p id="timer">60s</p>
                </div>
                <div class="stat-box">
                    <h4>WPM</h4>
                    <p id="wpm">0</p>
                </div>
                <div class="stat-box">
                    <h4>Accuracy</h4>
                    <p id="accuracy">0%</p>
                </div>
                <div class="stat-box">
                    <h4>Errors</h4>
                    <p id="errors">0</p>
                </div>
            </div>

            <div id="text-display" class="text-display"></div>
            <textarea id="input-field" class="input-field" placeholder="Start typing here..." disabled></textarea>
            
            <div class="text-center">
                <button id="start-btn" class="btn btn-primary">Start Test</button>
            </div>
            
            <div class="keyboard-container">
                <div class="main-keyboard">
                  
                    <div class="keyboard-row">
            <div class="key line" data-key="`">`</div>
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
            <div class="key backspace" data-key="Backspace">Backspace</div>
        </div>
        <div class="keyboard-row">
            <div class="key tab" data-key="Tab">Tab</div>
            <div class="key" data-key="q">Q</div>
            <div class="key" data-key="w">W</div>
            <div class="key" data-key="e">E</div>
            <div class="key" data-key="r">R</div>
            <div class="key" data-key="t">T</div>
            <div class="key" data-key="y">Y</div>
            <div class ="key" data-key="u">U</div>
            <div class="key" data-key="i">I</div>
            <div class="key" data-key="o">O</div>
            <div class="key" data-key="p">P</div>
            <div class="key" data-key="[">[</div>
            <div class="key" data-key="]">]</div>
            <div class="key" data-key="&gt;\">\</div>
        </div>
        <div class="keyboard-row">
            <div class="key caps" data-key="CapsLock">Caps Lock</div>
            <div class="key" data-key="a">A</div>
            <div class="key" data-key="s">S</div>
            <div class="key" data-key="d">D</div>
            <div class="key" data-key="f">F</div>
            <div class="key" data-key="g">G</div>
            <div class="key" data-key="h">H</div>
            <div class="key" data-key="j">J</div>
            <div class="key" data-key="k">K</div>
            <div class="key" data-key="l">L</div>
            <div class="key" data-key=";">;</div>
            <div class="key" data-key="'">'</div>
            <div class="key enter" data-key="Enter">Enter</div>
        </div>
            <div class="keyboard-row">
            <div class="key shift left" data-key="Shift">Shift</div>
            <div class="key" data-key="z">Z</div>
            <div class="key" data-key="x">X</div>
            <div class="key" data-key="c">C</div>
            <div class="key" data-key="v">V</div>
            <div class="key" data-key="b">B</div>
            <div class="key" data-key="n">N</div>
            <div class="key" data-key="m">M</div>
            <div class="key" data-key=",">,</div>
            <div class="key" data-key=".">.</div>
            <div class="key" data-key="\">/</div>
            <div  class="key shift right" data-key="Shift">Shift</div>
        </div>
    
        <div class="keyboard-row">
             <div class="key space" data-key=' '> </div>
       </div>

                   <!-- <div class="keyboard-row">
                        <div class="key">~</div>
                        <div class="key">1</div>
                        <div class="key">2</div>
                        <div class="key">3</div>
                        <div class="key">4</div>
                        <div class="key">5</div>
                        <div class="key">6</div>
                        <div class="key">7</div>
                        <div class="key">8</div>
                        <div class="key">9</div>
                        <div class="key">0</div>
                        <div class="key">-</div>
                        <div class="key">=</div>
                        <div class="key wide">⌫</div>
                    </div>
                    <div class="keyboard-row">
                        <div class="key wide">Tab</div>
                        <div class="key">Q</div>
                        <div class="key">W</div>
                        <div class="key">E</div>
                        <div class="key">R</div>
                        <div class="key">T</div>
                        <div class="key">Y</div>
                        <div class="key">U</div>
                        <div class="key">I</div>
                        <div class="key">O</div>
                        <div class="key">P</div>
                        <div class="key">[</div>
                        <div class="key">]</div>
                        <div class="key">\</div>
                    </div>
                    <div class="keyboard-row">
                        <div class="key extra-wide">Caps</div>
                        <div class="key">A</div>
                        <div class="key">S</div>
                        <div class="key">D</div>
                        <div class="key">F</div>
                        <div class="key">G</div>
                        <div class="key">H</div>
                        <div class="key">J</div>
                        <div class="key">K</div>
                        <div class="key">L</div>
                        <div class="key">;</div>
                        <div class="key">'</div>
                        <div class="key extra-wide">Enter</div>
                    </div>
                    <div class="keyboard-row">
                        <div class="key extra-wide">Shift</div>
                        <div class="key">Z</div>
                        <div class="key">X</div>
                        <div class="key">C</div>
                        <div class="key">V</div>
                        <div class="key">B</div>
                        <div class="key">N</div>
                        <div class="key">M</div>
                        <div class="key">,</div>
                        <div class="key">.</div>
                        <div class="key">/</div>
                        <div class="key extra-wide">Shift</div>
                    </div>
                    <div class="keyboard-row">
                        <div class="key wide">Ctrl</div>
                        <div class="key wide">Alt</div>
                        <div class="key space">Space</div>
                        <div class="key wide">Alt</div>
                        <div class="key wide">Ctrl</div>
                    </div>-->
                </div>
            </div>
        </div>
    </div>

    <script>
        const textDisplay = document.getElementById('text-display');
        const inputField = document.getElementById('input-field');
        const startButton = document.getElementById('start-btn');
        const timerDisplay = document.getElementById('timer');
        const wpmDisplay = document.getElementById('wpm');
        const accuracyDisplay = document.getElementById('accuracy');
        const errorsDisplay = document.getElementById('errors');

        const sampleTexts = [
            "The quick brown fox jumps over the lazy dog. This pangram contains every letter of the English alphabet at least once. Pangrams are often used to display font samples and test keyboards.",
            "Programming is the art of telling another human what one wants the computer to do. It requires logical thinking, problem-solving skills, and attention to detail.",
            "Technology continues to evolve at an unprecedented rate, transforming the way we live, work, and communicate with one another in the digital age."
        ];


        let timeLeft = 60;
        let timer = null;
        let errors = 0;
        let totalTyped = 0;
        let testActive = false;
        let currentText = '';

        function startTest() {
            testActive = true;
            errors = 0;
            totalTyped = 0;
            timeLeft = 60;
            currentText = sampleTexts[Math.floor(Math.random() * sampleTexts.length)];
            textDisplay.textContent = currentText;
            inputField.value = '';
            inputField.disabled = false;
            inputField.focus();
            startButton.disabled = true;

            timer = setInterval(() => {
                timeLeft--;
                timerDisplay.textContent = timeLeft + 's';
                updateStats();

                if (timeLeft <= 0) {
                    endTest();
                }
            }, 1000);
        }

        function endTest() {
            clearInterval(timer);
            testActive = false;
            inputField.disabled = true;
            startButton.disabled = false;
            startButton.textContent = 'Restart Test';
            
            // Calculate final statistics
            const finalWpm = parseInt(wpmDisplay.textContent);
            const finalAccuracy = parseInt(accuracyDisplay.textContent);
            
            // Save test results to database
            fetch('save_result.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    wpm: finalWpm,
                    accuracy: finalAccuracy,
                    errors: errors
                })
            });

            if (finalWpm >= 30 && finalAccuracy >= 90) {
                window.location.href = 'generate_certificate.php';
            }
        }

        function updateStats() {
            const words = inputField.value.trim().split(/\s+/).length;
            const minutes = (60 - timeLeft) / 60;
            const wpm = Math.round(words / minutes);
            const accuracy = Math.round(((totalTyped - errors) / totalTyped) * 100) || 0;

            wpmDisplay.textContent = wpm;
            accuracyDisplay.textContent = accuracy + '%';
            errorsDisplay.textContent = errors;
        }

        inputField.addEventListener('input', () => {
            if (!testActive) return;

            const currentInput = inputField.value;
            totalTyped = currentInput.length;

            // Check for errors
            errors = 0;
            for (let i = 0; i < currentInput.length; i++) {
                if (currentInput[i] !== currentText[i]) {
                    errors++;
                }
            }

            updateStats();
        });

        startButton.addEventListener('click', startTest);

        // Keyboard visualization
        const keySound = document.getElementById('keySound');
        
        document.addEventListener('keydown', (e) => {
            if (!testActive) return;
            const key = e.key.toUpperCase();
            const keyElement = Array.from(document.querySelectorAll('.key')).find(el => 
                el.textContent.toUpperCase() === key || 
                (key === ' ' && el.classList.contains('space'))
            );
            
            if (keyElement) {
                keyElement.classList.add('active');
                // Play sound
                keySound.currentTime = 0; // Reset sound to start
                keySound.play();
                setTimeout(() => keyElement.classList.remove('active'), 100);
            }
        });

        // Remove the jQuery contains function as it's no longer needed
        jQuery.expr[':'].contains = function(a, i, m) {
            return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
        };
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Add navbar scroll effect
        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) {
                document.querySelector('.navbar').classList.add('scrolled');
            } else {
                document.querySelector('.navbar').classList.remove('scrolled');
            }
        });
    </script>
    <script>
        // Theme switcher
        const themes = ['dark', 'light', 'neon'];
        let currentThemeIndex = 0;
        const keyboard = document.querySelector('.main-keyboard');
        
        document.getElementById('themeToggle').addEventListener('click', (e) => {
            e.preventDefault();
            currentThemeIndex = (currentThemeIndex + 1) % themes.length;
            const newTheme = themes[currentThemeIndex];
            
            // Remove all theme classes
            themes.forEach(theme => {
                keyboard.classList.remove(`keyboard-theme-${theme}`);
            });
            
            // Add new theme class
            keyboard.classList.add(`keyboard-theme-${newTheme}`);
            
            // Update button text
            const themeNames = { dark: 'Dark', light: 'Light', neon: 'Neon' };
            e.target.innerHTML = `<i class="fas fa-palette me-2"></i>Theme: ${themeNames[newTheme]}`;
            
            // Save theme preference
            localStorage.setItem('keyboardTheme', newTheme);
        });

        // Load saved theme
        const savedTheme = localStorage.getItem('keyboardTheme') || 'dark';
        keyboard.classList.add(`keyboard-theme-${savedTheme}`);
        currentThemeIndex = themes.indexOf(savedTheme);
        
        // Set initial button text
        const themeNames = { dark: 'Dark', light: 'Light', neon: 'Neon' };
        document.getElementById('themeToggle').innerHTML = `<i class="fas fa-palette me-2"></i>Theme: ${themeNames[savedTheme]}`;
    </script>
</body>
</html>