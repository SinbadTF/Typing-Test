<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';

// Update the getLessons function
function getLessons($pdo, $level) {
    $stmt = $pdo->prepare("SELECT l.*, 
        (SELECT lp.status FROM japanese_lesson_progress lp 
         WHERE lp.user_id = ? AND lp.lesson_id = l.id 
         ORDER BY lp.completed_at DESC LIMIT 1) as status,
        (SELECT MAX(lp.wpm) FROM japanese_lesson_progress lp 
         WHERE lp.user_id = ? AND lp.lesson_id = l.id) as best_wpm,
        (SELECT MAX(lp.accuracy) FROM japanese_lesson_progress lp 
         WHERE lp.user_id = ? AND lp.lesson_id = l.id) as best_accuracy
        FROM japanese_lessons l 
        WHERE l.level = ? 
        ORDER BY l.lesson_number");
    $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $level]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function isPreviousLessonCompleted($lessons, $currentLessonNumber) {
    if ($currentLessonNumber <= 1) return true;
    foreach ($lessons as $lesson) {
        if ($lesson['lesson_number'] === ($currentLessonNumber - 1)) {
            return $lesson['status'] === 'completed';
        }
    }
    return false;
};
$basicLessons = getLessons($pdo, 'basic');
$intermediateLessons = getLessons($pdo, 'intermediate');
$advancedLessons = getLessons($pdo, 'advanced');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Japanese Typing Course - 日本語タイピング</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #ffffff;
            min-height: 100vh;
            padding-bottom: 50px;
        }
        .navbar {
            background: rgba(25, 25, 25, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.4);
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .course-section {
            padding: 80px 0;
            max-width: 1000px;
            margin: 0 auto;
        }
        .level-card {
            background: rgba(35, 35, 35, 0.95);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 40px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .level-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .level-icon {
            background: linear-gradient(45deg, #007bff, #00ff88);
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 20px;
            color: white;
            box-shadow: 0 10px 20px rgba(0,123,255,0.2);
        }
        .level-title {
            font-size: 1.8rem;
            margin: 0;
            background: linear-gradient(45deg, #ffffff, #adb5bd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .lesson-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        .lesson-card {
            background: rgba(25, 25, 25, 0.95);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            text-decoration: none;
            color: white;
        }
        .lesson-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,123,255,0.2);
            border-color: rgba(0,123,255,0.3);
        }
        .lesson-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .lesson-name {
            color: #adb5bd;
            font-size: 1rem;
            margin-bottom: 15px;
        }
        .lesson-status {
            font-size: 0.9rem;
            padding: 5px 10px;
            border-radius: 10px;
            background: rgba(0, 123, 255, 0.1);
            color: #007bff;
            display: inline-block;
        }
        .lesson-status.completed {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 3rem;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }
        .lesson-stats {
    margin-bottom: 10px;
    font-size: 0.9rem;
    color: #adb5bd;
}

.lesson-stats span {
    display: inline-block;
    margin: 0 5px;
}

.lesson-stats i {
    margin-right: 5px;
    color: #007bff;
}
/* Add these styles to your existing CSS */
.lesson-card.locked {
    opacity: 0.7;
    cursor: not-allowed;
    position: relative;
}

.lesson-card.locked:hover {
    transform: none;
    box-shadow: none;
}

.lesson-card .lesson-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.lesson-card.locked .lesson-number {
    background: linear-gradient(45deg, #6c757d, #495057);
}

.lesson-card.locked .lesson-status {
    background: rgba(108, 117, 125, 0.1);
    color: #6c757d;
}
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <div class="course-section">
            <h1 class="section-title">日本語タイピングコース</h1>
            
            <!-- Basic Level -->
            <div class="level-card">
                <div class="level-header">
                    <div class="level-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h2 class="level-title">Basic Level</h2>
                </div>
                
                <!-- Modify the lesson card section in each level -->
                <div class="lesson-grid">
                    <?php foreach($basicLessons as $lesson): 
                        $isLocked = !isPreviousLessonCompleted($basicLessons, $lesson['lesson_number']);
                    ?>
                    <div class="lesson-card <?php echo $isLocked ? 'locked' : ''; ?>">
                        <?php if (!$isLocked): ?>
                        <a href="japanese_lesson.php?level=basic&lesson=<?php echo $lesson['lesson_number']; ?>&id=<?php echo $lesson['id']; ?>" 
                           class="lesson-link">
                        <?php endif; ?>
                            <div class="lesson-number">
                                <?php echo $lesson['lesson_number']; ?>
                                <?php if ($isLocked): ?>
                                    <i class="fas fa-lock"></i>
                                <?php endif; ?>
                            </div>
                            <div class="lesson-name"><?php echo htmlspecialchars($lesson['title']); ?></div>
                            <?php if ($lesson['best_wpm']): ?>
                            <div class="lesson-stats">
                                <span><i class="fas fa-tachometer-alt"></i> <?php echo round($lesson['best_wpm']); ?> WPM</span>
                                <span><i class="fas fa-bullseye"></i> <?php echo round($lesson['best_accuracy']); ?>%</span>
                            </div>
                            <?php endif; ?>
                            <div class="lesson-status <?php echo $lesson['status'] === 'completed' ? 'completed' : ''; ?>">
                                <?php echo $isLocked ? 'Locked' : ($lesson['status'] ? ucfirst($lesson['status']) : 'Start'); ?>
                            </div>
                        <?php if (!$isLocked): ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                
            </div>

            <!-- Intermediate Level -->
            <!-- Replace the Intermediate Level section -->
            <div class="level-card">
                <div class="level-header">
                    <div class="level-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h2 class="level-title">Intermediate Level</h2>
                </div>
                <div class="lesson-grid">
                    <?php foreach($intermediateLessons as $lesson): 
                        $isLocked = !isPreviousLessonCompleted($intermediateLessons, $lesson['lesson_number']);
                    ?>
                    <div class="lesson-card <?php echo $isLocked ? 'locked' : ''; ?>">
                        <?php if (!$isLocked): ?>
                        <a href="japanese_lesson.php?level=intermediate&lesson=<?php echo $lesson['lesson_number']; ?>&id=<?php echo $lesson['id']; ?>" 
                           class="lesson-link">
                        <?php endif; ?>
                            <div class="lesson-number">
                                <?php echo $lesson['lesson_number']; ?>
                                <?php if ($isLocked): ?>
                                    <i class="fas fa-lock"></i>
                                <?php endif; ?>
                            </div>
                            <div class="lesson-name"><?php echo htmlspecialchars($lesson['title']); ?></div>
                            <?php if ($lesson['best_wpm']): ?>
                            <div class="lesson-stats">
                                <span><i class="fas fa-tachometer-alt"></i> <?php echo round($lesson['best_wpm']); ?> WPM</span>
                                <span><i class="fas fa-bullseye"></i> <?php echo round($lesson['best_accuracy']); ?>%</span>
                            </div>
                            <?php endif; ?>
                            <div class="lesson-status <?php echo $lesson['status'] === 'completed' ? 'completed' : ''; ?>">
                                <?php echo $isLocked ? 'Locked' : ($lesson['status'] ? ucfirst($lesson['status']) : 'Start'); ?>
                            </div>
                        <?php if (!$isLocked): ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Advanced Level -->
            <div class="level-card">
                <div class="level-header">
                    <div class="level-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h2 class="level-title">Advanced Level</h2>
                </div>
                <div class="lesson-grid">
                    <?php foreach($advancedLessons as $lesson): 
                        $isLocked = !isPreviousLessonCompleted($advancedLessons, $lesson['lesson_number']);
                    ?>
                    <div class="lesson-card <?php echo $isLocked ? 'locked' : ''; ?>">
                        <?php if (!$isLocked): ?>
                        <a href="japanese_lesson.php?level=advanced&lesson=<?php echo $lesson['lesson_number']; ?>&id=<?php echo $lesson['id']; ?>" 
                           class="lesson-link">
                        <?php endif; ?>
                            <div class="lesson-number">
                                <?php echo $lesson['lesson_number']; ?>
                                <?php if ($isLocked): ?>
                                    <i class="fas fa-lock"></i>
                                <?php endif; ?>
                            </div>
                            <div class="lesson-name"><?php echo htmlspecialchars($lesson['title']); ?></div>
                            <?php if ($lesson['best_wpm']): ?>
                            <div class="lesson-stats">
                                <span><i class="fas fa-tachometer-alt"></i> <?php echo round($lesson['best_wpm']); ?> WPM</span>
                                <span><i class="fas fa-bullseye"></i> <?php echo round($lesson['best_accuracy']); ?>%</span>
                            </div>
                            <?php endif; ?>
                            <div class="lesson-status <?php echo $lesson['status'] === 'completed' ? 'completed' : ''; ?>">
                                <?php echo $isLocked ? 'Locked' : ($lesson['status'] ? ucfirst($lesson['status']) : 'Start'); ?>
                            </div>
                        <?php if (!$isLocked): ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>