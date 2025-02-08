class TypingSounds {
    constructor() {
        this.keyPressSound = new Audio('../assets/sounds/key-press.mp3');
        this.errorSound = new Audio('../assets/sounds/error.mp3');
        this.completionSound = new Audio('../assets/sounds/complete.mp3');

        // Preload sounds
        this.keyPressSound.load();
        this.errorSound.load();
        this.completionSound.load();

        // Set volume
        this.keyPressSound.volume = 0.5;
        this.errorSound.volume = 0.4;
        this.completionSound.volume = 0.6;
    }

    playKeyPress() {
        this.keyPressSound.currentTime = 0;
        this.keyPressSound.play().catch(e => console.log('Sound play failed:', e));
    }

    playError() {
        this.errorSound.currentTime = 0;
        this.errorSound.play().catch(e => console.log('Sound play failed:', e));
    }

    playCompletion() {
        this.completionSound.play().catch(e => console.log('Sound play failed:', e));
    }
}