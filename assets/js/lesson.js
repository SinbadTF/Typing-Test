document.addEventListener('DOMContentLoaded', function() {
    const words = document.getElementById('words');
    const input = document.getElementById('input-field');
    const wpmDisplay = document.getElementById('wpm');
    const accuracyDisplay = document.getElementById('accuracy');
    const timeDisplay = document.getElementById('time');
    const restartButton = document.getElementById('restart-button');
    const nextBtn = document.getElementById('next-btn');
    const keys = document.querySelectorAll('.key');

    let currentIndex = 0;
    let mistakes = 0;
    let isTyping = false;
    let startTime;
    let timer;
    let totalChars = 0;

    function initLesson() {
        words.innerHTML = lessonText.split('').map((char, i) => 
            `<span class="letter ${i === 0 ? 'current' : ''}">${char}</span>`
        ).join('');
        
        currentIndex = 0;
        mistakes = 0;
        isTyping = false;
        totalChars = 0;
        
        clearInterval(timer);
        timeDisplay.textContent = '0:00';
        
        // Load previous stats instead of resetting
        loadPreviousStats();
        
        input.value = '';
        input.focus();

        if (nextBtn) nextBtn.style.display = 'none';
    }

    function highlightKey(key) {
        const keyElement = document.querySelector(`[data-key="${key.toLowerCase()}"]`);
        if (keyElement) {
            keyElement.classList.add('active');
            setTimeout(() => keyElement.classList.remove('active'), 100);
        }
    }

    function updateStats() {
        if (!startTime) return;
        
        const timeElapsed = (Date.now() - startTime) / 1000 / 60;
        const wpm = Math.round((currentIndex / 5) / timeElapsed);
        const accuracy = Math.round(((totalChars - mistakes) / totalChars) * 100) || 100;

        wpmDisplay.textContent = wpm;
        accuracyDisplay.textContent = accuracy + '%';

        // Store current stats in localStorage
        localStorage.setItem('currentWPM', wpm);
        localStorage.setItem('currentAccuracy', accuracy);

        return { wpm, accuracy };
    }

    // Add this function to load previous stats
    function loadPreviousStats() {
        const previousWPM = localStorage.getItem('currentWPM') || '0';
        const previousAccuracy = localStorage.getItem('currentAccuracy') || '100';
        
        wpmDisplay.textContent = previousWPM;
        accuracyDisplay.textContent = previousAccuracy + '%';
    }

    // Update initLesson function
    function initLesson() {
        words.innerHTML = lessonText.split('').map((char, i) => 
            `<span class="letter ${i === 0 ? 'current' : ''}">${char}</span>`
        ).join('');
        
        currentIndex = 0;
        mistakes = 0;
        isTyping = false;
        totalChars = 0;
        
        clearInterval(timer);
        timeDisplay.textContent = '0:00';
        
        // Load previous stats instead of resetting
        loadPreviousStats();
        
        input.value = '';
        input.focus();

        if (nextBtn) nextBtn.style.display = 'none';
    }

    function saveLessonProgress() {
        const stats = updateStats();
        
        fetch('save_progress.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                lessonId: lessonId,
                userId: userId,
                wpm: stats.wpm,
                accuracy: stats.accuracy,
                status: 'completed'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && nextBtn) {
                nextBtn.style.display = 'inline-block';
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Remove the duplicate input event listener and combine the logic
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && currentIndex > 0) {
            e.preventDefault();
            currentIndex--;
            
            const letters = words.querySelectorAll('.letter');
            if (letters[currentIndex].classList.contains('incorrect')) {
                mistakes--;
            }
            totalChars--;
            
            letters[currentIndex].classList.remove('correct', 'incorrect');
            letters[currentIndex].classList.add('current');
            if (letters[currentIndex + 1]) {
                letters[currentIndex + 1].classList.remove('current');
            }
            
            highlightKey('Backspace');
            updateStats();
            input.value = '';
        }
    });

    // Add at the beginning with other constants
        const correctSound = new Audio('/assets/sounds/key-press.mp3');
        const wrongSound = new Audio('/assets/sounds/error.mp3');
        correctSound.volume = 0.3;
        wrongSound.volume = 0.2;
    
        // Update the input event listener
        input.addEventListener('input', (e) => {
            if (!isTyping) {
                startTime = Date.now();
                isTyping = true;
                startTimer();
            }
    
            if (e.inputType === 'deleteContentBackward') return;
            if (!e.data) return;
    
            const letters = words.querySelectorAll('.letter');
            const current = letters[currentIndex];
            const typedChar = e.data;
            const correctChar = letters[currentIndex].textContent;
    
            highlightKey(typedChar);
            totalChars++;
    
            if (typedChar === correctChar) {
                current.classList.add('correct');
                correctSound.currentTime = 0;
                correctSound.play();
            } else {
                current.classList.add('incorrect');
                mistakes++;
                wrongSound.currentTime = 0;
                wrongSound.play();
            }
    
            current.classList.remove('current');
            if (letters[currentIndex + 1]) {
                letters[currentIndex + 1].classList.add('current');
            }
    
            currentIndex++;
            input.value = '';
            updateStats();
    
            if (currentIndex >= lessonText.length) {
                clearInterval(timer);
                saveLessonProgress();
            }
        });

    // Update key highlight handlers
    document.addEventListener('keydown', (e) => {
        const key = e.key === ' ' ? ' ' : e.key;
        const keyElement = document.querySelector(`[data-key="${key}"]`);
        if (keyElement) {
            keyElement.classList.add('active');
        }
    });

    document.addEventListener('keyup', (e) => {
        const key = e.key === ' ' ? ' ' : e.key;
        const keyElement = document.querySelector(`[data-key="${key}"]`);
        if (keyElement) {
            keyElement.classList.remove('active');
        }
    });

    function startTimer() {
        timer = setInterval(() => {
            const timeElapsed = Math.floor((Date.now() - startTime) / 1000);
            const minutes = Math.floor(timeElapsed / 60);
            const seconds = timeElapsed % 60;
            timeDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }, 1000);
    }

    restartButton.addEventListener('click', initLesson);
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && e.key === 'k') {
            e.preventDefault();
            initLesson();
        }
    });

    initLesson();
});
// Add after the existing DOM elements
    const themeToggle = document.getElementById('theme-toggle');
    const themeOptions = document.getElementById('theme-options');
    const keyboard = document.querySelector('.keyboard');

    // Add theme handling
    themeToggle.addEventListener('click', () => {
        themeOptions.classList.toggle('show');
    });

    document.addEventListener('click', (e) => {
        if (!themeToggle.contains(e.target) && !themeOptions.contains(e.target)) {
            themeOptions.classList.remove('show');
        }
    });

    document.querySelectorAll('.theme-option').forEach(option => {
        option.addEventListener('click', () => {
            const theme = option.dataset.theme;
            const color = option.dataset.color;
            
            // Update keyboard theme
            keyboard.className = 'keyboard theme-' + theme;
            document.body.style.backgroundColor = color;
            
            // Update navbar colors based on theme
            const navbar = document.querySelector('.navbar');
            const navLinks = document.querySelectorAll('.navbar .nav-link, .navbar .navbar-brand');
            
            switch(theme) {
                case 'light':
                    navbar.style.backgroundColor = '#e0e0e0';
                    navLinks.forEach(link => link.style.color = '#2c2c2c');
                    break;
                case 'midnight':
                    navbar.style.backgroundColor = '#1a1b26';
                    navLinks.forEach(link => link.style.color = '#7aa2f7');
                    break;
                case 'forest':
                    navbar.style.backgroundColor = '#2b2f2b';
                    navLinks.forEach(link => link.style.color = '#95c085');
                    break;
                case 'sunset':
                    navbar.style.backgroundColor = '#2d1b2d';
                    navLinks.forEach(link => link.style.color = '#f67e7d');
                    break;
                default: // dark theme
                    navbar.style.backgroundColor = '#323437';
                    navLinks.forEach(link => link.style.color = '#d1d0c5');
            }
            
            // Update typing area colors
            document.querySelector('.typing-area').style.backgroundColor = 
                theme === 'light' ? '#f0f0f0' : 'rgba(25, 25, 25, 0.95)';
            
            themeOptions.classList.remove('show');
            localStorage.setItem('keyboard-theme', theme);
            localStorage.setItem('page-color', color);
        });
    });

    // Update the theme loading code
    const savedTheme = localStorage.getItem('keyboard-theme');
    const savedColor = localStorage.getItem('page-color');
    if (savedTheme && savedColor) {
        const navbar = document.querySelector('.navbar');
        const navLinks = document.querySelectorAll('.navbar .nav-link, .navbar .navbar-brand');
        
        keyboard.className = 'keyboard theme-' + savedTheme;
        document.body.style.backgroundColor = savedColor;
        
        // Apply saved navbar theme
        switch(savedTheme) {
            case 'light':
                navbar.style.backgroundColor = '#e0e0e0';
                navLinks.forEach(link => link.style.color = '#2c2c2c');
                break;
            case 'midnight':
                navbar.style.backgroundColor = '#1a1b26';
                navLinks.forEach(link => link.style.color = '#7aa2f7');
                break;
            case 'forest':
                navbar.style.backgroundColor = '#2b2f2b';
                navLinks.forEach(link => link.style.color = '#95c085');
                break;
            case 'sunset':
                navbar.style.backgroundColor = '#2d1b2d';
                navLinks.forEach(link => link.style.color = '#f67e7d');
                break;
            default:
                navbar.style.backgroundColor = '#323437';
                navLinks.forEach(link => link.style.color = '#d1d0c5');
        }
    }
    themeToggle.style.backgroundColor = savedColor;