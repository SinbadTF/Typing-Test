document.addEventListener('DOMContentLoaded', function() {
    const typingArea = document.querySelector('.typing-area');
    const exerciseText = document.querySelector('.exercise-text');
    const progressFill = document.querySelector('.progress-fill');
    const keys = document.querySelectorAll('.key');
    const wpmValue = document.querySelector('.stat-value');
    const accuracyValue = document.querySelectorAll('.stat-value')[1];
    const errorsValue = document.querySelectorAll('.stat-value')[2];

    let startTime;
    let isTyping = false;
    let currentIndex = 0;
    let errors = 0;
    let totalChars = 0;

    // Initialize the typing text
    const text = exerciseText.textContent;
    exerciseText.innerHTML = text.split('').map(char => 
        `<span class="letter">${char}</span>`
    ).join('');

    const letters = exerciseText.querySelectorAll('.letter');
    letters[0].classList.add('current');

    // Create hidden input for typing
    const hiddenInput = document.createElement('input');
    hiddenInput.className = 'input-field';
    typingArea.appendChild(hiddenInput);

    // Focus the hidden input when clicking on the typing area
    typingArea.addEventListener('click', () => hiddenInput.focus());

    hiddenInput.addEventListener('input', (e) => {
        if (!isTyping) {
            startTime = new Date();
            isTyping = true;
        }

        const typed = e.target.value;
        const current = text[currentIndex];

        // Update keyboard visual
        keys.forEach(key => {
            if (key.textContent.toLowerCase() === typed.slice(-1).toLowerCase()) {
                key.classList.add('active');
                setTimeout(() => key.classList.remove('active'), 100);
            }
        });

        if (typed.slice(-1) === current) {
            // Correct input
            letters[currentIndex].classList.add('correct');
            letters[currentIndex].classList.remove('current');
            currentIndex++;
            totalChars++;

            if (currentIndex < text.length) {
                letters[currentIndex].classList.add('current');
            } else {
                // Exercise completed
                finishExercise();
            }
        } else {
            // Incorrect input
            letters[currentIndex].classList.add('incorrect');
            errors++;
            totalChars++;
        }

        // Update progress bar
        const progress = (currentIndex / text.length) * 100;
        progressFill.style.width = `${progress}%`;

        // Update stats
        updateStats();
    });

    function updateStats() {
        if (!startTime) return;

        const currentTime = new Date();
        const timeElapsed = (currentTime - startTime) / 1000 / 60; // in minutes
        const wordsTyped = currentIndex / 5; // assuming average word length of 5 characters
        const wpm = Math.round(wordsTyped / timeElapsed);
        const accuracy = Math.round(((totalChars - errors) / totalChars) * 100);

        wpmValue.textContent = wpm;
        accuracyValue.textContent = `${accuracy}%`;
        errorsValue.textContent = errors;
    }

    function finishExercise() {
        hiddenInput.disabled = true;
        // Add any completion logic here
    }

    // Initial focus
    hiddenInput.focus();
});