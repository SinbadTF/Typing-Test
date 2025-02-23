<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Typing Lesson</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #111;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        .typing-container {
            border: 2px solid #555;
            padding: 20px;
            margin: auto;
            max-width: 600px;
            background: #222;
            border-radius: 10px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background: #222;
            color: #fff;
            padding: 20px;
            margin: 10% auto;
            width: 300px;
            text-align: center;
            border-radius: 10px;
        }
        button {
            background: #555;
            color: white;
            padding: 10px;
            margin: 5px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background: #777;
        }
    </style>
</head>
<body>

    <h1>Typing Practice</h1>
    <div class="typing-container">
        <p id="lesson-text">The quick brown fox jumps over the lazy dog.</p>
        <input type="text" id="user-input" oninput="checkTyping()" autofocus>
        <button onclick="finishLesson()">Finish</button>
    </div>

    <div id="resultModal" class="modal">
        <div class="modal-content">
            <h2>Oh oh, You might want to repeat this lesson.</h2>
            <p><strong>Accuracy:</strong> <span id="accuracy">0%</span></p>
            <p><strong>Speed:</strong> <span id="speed">0 WPM</span></p>
            <p><strong>Errors:</strong> <span id="errors">0</span></p>
            <p><strong>Time:</strong> <span id="time">0 s</span></p>
            <button onclick="closeModal()">Back to List</button>
            <button onclick="repeatLesson()">Repeat</button>
            <button onclick="saveProgress()">Save Progress</button>
        </div>
    </div>

    <script>
        let startTime, errors = 0, typedChars = 0;
        const lessonText = document.getElementById("lesson-text").innerText;
        const inputField = document.getElementById("user-input");

        function checkTyping() {
            if (!startTime) startTime = new Date();
            let userText = inputField.value;
            typedChars = userText.length;

            // Count errors
            errors = 0;
            for (let i = 0; i < userText.length; i++) {
                if (userText[i] !== lessonText[i]) errors++;
            }
        }

        function finishLesson() {
            if (!startTime) return alert("Start typing first!");
            let timeTaken = (new Date() - startTime) / 1000; // in seconds
            let accuracy = Math.max(0, ((typedChars - errors) / typedChars) * 100).toFixed(2);
            let wordsPerMinute = Math.round((typedChars / 5) / (timeTaken / 60));

            showResults(accuracy, wordsPerMinute, errors, timeTaken.toFixed(1));
        }

        function showResults(accuracy, speed, errors, time) {
            document.getElementById("accuracy").textContent = accuracy + "%";
            document.getElementById("speed").textContent = speed + " WPM";
            document.getElementById("errors").textContent = errors;
            document.getElementById("time").textContent = time + " s";
            document.getElementById("resultModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("resultModal").style.display = "none";
        }

        function repeatLesson() {
            location.reload();
        }

        function saveProgress() {
            alert("Progress saved! (You can implement database storage here)");
        }
    </script>

</body>
</html>
