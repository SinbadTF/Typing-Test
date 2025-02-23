<?php
session_start();
require_once 'config/database.php';



// Fetch Japanese lessons from database
$stmt = $pdo->prepare("SELECT * FROM japanese_lessons ORDER BY level, lesson_number");
$stmt->execute();
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group lessons by level
$groupedLessons = [];
foreach ($lessons as $lesson) {
    $groupedLessons[$lesson['level']][] = $lesson;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proverb Typing Course </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #323437;
            color: #d1d0c5;
            font-family: 'Hiragino Kaku Gothic Pro', 'メイリオ', sans-serif;
        }

        .course-container {
            max-width: 1200px;
            margin: 80px auto;
            padding: 20px;
        }

        .level-card {
            background: rgba(35, 35, 35, 0.95);
            border-radius: 15px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }

        .level-header {
            background: linear-gradient(45deg, #007bff, #00ff88);
            padding: 20px;
            color: white;
        }

        .lesson-list {
            padding: 20px;
        }

        .lesson-item {
            background: rgba(25, 25, 25, 0.95);
            border-radius: 10px;
            margin-bottom: 15px;
            padding: 20px;
            transition: transform 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .lesson-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .lesson-title {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #fff;
        }

        .lesson-description {
            color: #adb5bd;
            margin-bottom: 15px;
        }

        .lesson-number {
            background: linear-gradient(45deg, #007bff, #00ff88);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="course-container">
     
        
        <?php foreach ($groupedLessons as $level => $levelLessons): ?>
        <div class="level-card">
            <div class="level-header">
                <h2>Level <?php echo htmlspecialchars($level); ?></h2>
            </div>
            <div class="lesson-list">
                <?php foreach ($levelLessons as $lesson): ?>
                <a href="proverb_lesson.php?level=<?php echo urlencode($level); ?>&lesson=<?php echo $lesson['lesson_number']; ?>&id=<?php echo $lesson['id']; ?>" 
                   class="text-decoration-none">
                    <div class="lesson-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="lesson-title"><?php echo htmlspecialchars($lesson['title']); ?></h3>
                                <p class="lesson-description"><?php echo htmlspecialchars($lesson['description']); ?></p>
                            </div>
                            <span class="lesson-number">Lesson <?php echo htmlspecialchars($lesson['lesson_number']); ?></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>