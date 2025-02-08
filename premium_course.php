<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is premium
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check premium status from database
$stmt = $pdo->prepare("SELECT is_premium FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['is_premium'] != 1) {
    header('Location: transaction/index.php');
    exit();
}

// Get premium lessons
$stmt = $pdo->prepare("SELECT * FROM premium_lessons ORDER BY level ASC");
$stmt->execute();
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Typing Course</title>
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

        .premium-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .level-section {
            background: rgba(32, 34, 37, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 40px;
            border: 1px solid rgba(209, 208, 197, 0.1);
        }

        .level-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(209, 208, 197, 0.1);
        }

        .level-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #e2b714, #e28c14);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 24px;
            color: #323437;
        }

        .level-title {
            font-size: 24px;
            color: #e2b714;
            margin: 0;
        }

        .lessons-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .lesson-card {
            background: rgba(38, 40, 43, 0.95);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(209, 208, 197, 0.1);
            transition: all 0.3s ease;
            text-decoration: none;
            color: #d1d0c5;
        }

        .lesson-card:hover {
            transform: translateY(-5px);
            border-color: #e2b714;
            box-shadow: 0 5px 15px rgba(226, 183, 20, 0.2);
        }

        .lesson-number {
            font-size: 32px;
            color: #e2b714;
            margin-bottom: 10px;
        }

        .lesson-info {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(209, 208, 197, 0.1);
        }

        .lesson-stat {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #646669;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .lesson-stat i {
            color: #e2b714;
        }

        .premium-badge {
            background: linear-gradient(135deg, #e2b714, #e28c14);
            color: #323437;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }

        .section-title {
            color: #e2b714;
            font-size: 32px;
            margin-bottom: 40px;
            text-align: center;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .level-icon {
            animation: float 3s ease-in-out infinite;
        }
        .action-button {
            background: none;
            border: 1px solid #e2b714;
            color: #e2b714;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
        }

        .action-button:hover {
            background: rgba(226, 183, 20, 0.1);
            transform: translateY(-2px);
            color: #e2b714;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="premium-container">
        <h1 class="section-title">Premium Typing Course <span class="premium-badge">PREMIUM</span></h1>

        <!-- Add custom practice button -->
        <div class="text-center mb-4">
            <a href="premium_custom_practice.php" class="action-button">
                <i class="fas fa-keyboard me-2"></i>Custom Practice
            </a>
        </div>

        <!-- Basic Level -->
        <div class="level-section">
            <div class="level-header">
                <div class="level-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h2 class="level-title">Basic Level</h2>
            </div>
            <div class="lessons-grid">
                <?php foreach($lessons as $lesson): ?>
                    <?php if($lesson['level'] === 'basic'): ?>
                        <a href="premium_lesson.php?id=<?php echo $lesson['id']; ?>" class="lesson-card">
                            <div class="lesson-number">Lesson <?php echo $lesson['lesson_number']; ?></div>
                            <h3><?php echo htmlspecialchars($lesson['title']); ?></h3>
                            <div class="lesson-info">
                                <div class="lesson-stat">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Target: <?php echo $lesson['target_wpm']; ?> WPM</span>
                                </div>
                                <div class="lesson-stat">
                                    <i class="fas fa-clock"></i>
                                    <span>Duration: 5 mins</span>
                                </div>
                            </div>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Advanced Level -->
        <div class="level-section">
            <div class="level-header">
                <div class="level-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <h2 class="level-title">Advanced Level</h2>
            </div>
            <div class="lessons-grid">
                <?php foreach($lessons as $lesson): ?>
                    <?php if($lesson['level'] === 'advanced'): ?>
                        <!-- Add this card to your lessons-grid -->
                        <a href="premium_custom_practice.php" class="lesson-card">
                            <div class="lesson-number"><i class="fas fa-edit"></i></div>
                            <h3>Custom Practice</h3>
                            <div class="lesson-info">
                                <div class="lesson-stat">
                                    <i class="fas fa-keyboard"></i>
                                    <span>Practice with your own text</span>
                                </div>
                            </div>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>