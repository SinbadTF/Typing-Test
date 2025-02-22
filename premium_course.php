<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is premium
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check premium status
$stmt = $pdo->prepare("SELECT is_premium FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['is_premium'] != 1) {
    header('Location: premium.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Courses - Boku no Typing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }

        .course-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .course-card {
            background: rgba(15, 23, 42, 0.7);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .course-title {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #f0b232;
        }

        .course-description {
            color: #adb5bd;
            margin-bottom: 20px;
        }

        .course-button {
            background: linear-gradient(45deg, #f0b232, #f7c157);
            color: #0f172a;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .course-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(240, 178, 50, 0.3);
            color: #0f172a;
        }

        .course-stats {
            color: #adb5bd;
            font-size: 0.9rem;
            margin-top: 15px;
        }

        .course-icon {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #f0b232;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="course-container">
        <h1 class="text-center mb-5">Premium Typing Courses</h1>
        
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="course-card">
                    <div class="course-icon">
                        <i class="fas fa-keyboard"></i>
                    </div>
                    <h2 class="course-title">Custom Practice</h2>
                    <p class="course-description">Create your own custom typing exercises with any text you want.</p>
                    <a href="premium_custom_practice.php" class="course-button">Start Practice</a>
                    <div class="course-stats">
                        <i class="fas fa-clock me-2"></i>Flexible Duration
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="course-card">
                    <div class="course-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h2 class="course-title">Certification Course</h2>
                    <p class="course-description">Complete exercises to earn your official typing certification.</p>
                    <a href="certification_course.php" class="course-button">Start Course</a>
                    <div class="course-stats">
                        <i class="fas fa-star me-2"></i>Earn Certificate
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="course-card">
                    <div class="course-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h2 class="course-title">Advanced Techniques</h2>
                    <p class="course-description">Learn advanced typing techniques and shortcuts.</p>
                    <a href="advanced_course.php" class="course-button">Start Learning</a>
                    <div class="course-stats">
                        <i class="fas fa-bolt me-2"></i>Advanced Level
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="course-card">
                    <div class="course-icon">
                        <i class="fas fa-code"></i>
                    </div>
                    <h2 class="course-title">Programming Practice</h2>
                    <p class="course-description">Practice typing code in various programming languages.</p>
                    <a href="programming_practice.php" class="course-button">Start Coding</a>
                    <div class="course-stats">
                        <i class="fas fa-code-branch me-2"></i>Multiple Languages
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>