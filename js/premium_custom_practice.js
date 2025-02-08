// ... existing code ...

// Initialize audio objects with correct paths
const keyPressSound = new Audio('../assets/sounds/key-press.mp3');
const errorSound = new Audio('../assets/sounds/error.mp3');
const completionSound = new Audio('../assets/sounds/complete.mp3');

// Get text content
const textDisplay = document.getElementById('textDisplay');
const textInput = document.getElementById('textInput');
const text = textDisplay.textContent;

function startTyping() {
    isTypingStarted = true;
    startTime = new Date();
    currentChar = text[currentIndex];
}

function updateText() {
    let displayText = '';
    for (let i = 0; i < text.length; i++) {
        if (i === currentIndex) {
            displayText += `<span class="current">${text[i]}</span>`;
        } else if (i < currentIndex) {
            displayText += `<span class="correct">${text[i]}</span>`;
        } else {
            displayText += text[i];
        }
    }
    textDisplay.innerHTML = displayText;
}

document.addEventListener('keydown', function(event) {
    if (!isTypingStarted && validKeys.includes(event.key)) {
        startTyping();
    }

    if (isTypingStarted && !isTypingEnded) {
        if (event.key === currentChar) {
            // Play sound for correct keypress
            keyPressSound.currentTime = 0;
            keyPressSound.volume = 0.5;
            keyPressSound.play().catch(e => console.log('Error playing sound:', e));

            currentIndex++;
            if (currentIndex === text.length) {
                endTyping();
            } else {
                currentChar = text[currentIndex];
            }
        } else if (validKeys.includes(event.key)) {
            // Play sound for error
            errorSound.currentTime = 0;
            errorSound.volume = 0.5;
            errorSound.play().catch(e => console.log('Error playing sound:', e));

            errors++;
        }
        updateText();
    }
});

function endTyping() {
    isTypingEnded = true;
    const endTime = new Date();
    const timeElapsed = (endTime - startTime) / 1000; // in seconds
    const wpm = Math.round((text.length / 5) / (timeElapsed / 60));
    const accuracy = Math.round(((text.length - errors) / text.length) * 100);

    // Play completion sound
    completionSound.currentTime = 0;
    completionSound.volume = 0.5;
    completionSound.play().catch(e => console.log('Error playing sound:', e));

    // Display results
    document.getElementById('wpm').textContent = wpm;
    document.getElementById('accuracy').textContent = accuracy;
    document.getElementById('results').style.display = 'block';
}

// Initial text update
updateText();