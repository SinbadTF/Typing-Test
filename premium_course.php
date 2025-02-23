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

// Add at the top of the file
$lang = $_GET['lang'] ?? 'en';

// Add language-specific titles
$languageTitles = [
    'en' => 'English Premium Courses',
    'my' => 'Myanmar Premium Courses',
    'jp' => 'Japanese Premium Courses'
];

$title = $languageTitles[$lang] ?? $languageTitles['en'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Boku no Typing</title>
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
        <h1 class="text-center mb-5">
            <?php if ($lang === 'my'): ?>
                မြန်မာဘာသာ Premium သင်ခန်းစာများ
            <?php elseif ($lang === 'jp'): ?>
                プレミアムコース
            <?php else: ?>
                Premium Courses
            <?php endif; ?>
        </h1>
        
        <div class="row">
            <?php if ($lang === 'my'): ?>
                <div class="col-md-6 mb-4">
                    <div class="course-card">
                        <div class="course-icon">
                            <i class="fas fa-keyboard"></i>
                        </div>
                        <h2 class="course-title">အခြေခံ သင်ခန်းစာများ</h2>
                        <p class="course-description">မြန်မာစာ စာရိုက်ခြင်း အခြေခံများကို လေ့လာပါ</p>
                        <a href="premium_lesson.php?lang=my&level=basic" class="course-button">စတင်လေ့လာမည်</a>
                        <div class="course-stats">
                            <i class="fas fa-clock me-2"></i>အခြေခံအဆင့်
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="course-card">
                        <div class="course-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h2 class="course-title">အဆင့်မြင့် သင်ခန်းစာများ</h2>
                        <p class="course-description">မြန်မာစာ စာရိုက်ခြင်း အဆင့်မြင့်နည်းများ</p>
                        <a href="premium_lesson.php?lang=my&level=advanced" class="course-button">စတင်မည်</a>
                        <div class="course-stats">
                            <i class="fas fa-star me-2"></i>အဆင့်မြင့်
                        </div>
                    </div>
                </div>

            <?php elseif ($lang === 'jp'): ?>
                <div class="col-md-6 mb-4">
                    <div class="course-card">
                        <div class="course-icon">
                            <i class="fas fa-keyboard"></i>
                        </div>
                        <h2 class="course-title">基本レッスン</h2>
                        <p class="course-description">日本語タイピングの基本を学びましょう</p>
                        <a href="premium_lesson.php?lang=jp&level=basic" class="course-button">始める</a>
                        <div class="course-stats">
                            <i class="fas fa-clock me-2"></i>基本レベル
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="course-card">
                        <div class="course-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h2 class="course-title">上級レッスン</h2>
                        <p class="course-description">日本語タイピングの高度なテクニック</p>
                        <a href="premium_lesson.php?lang=jp&level=advanced" class="course-button">始める</a>
                        <div class="course-stats">
                            <i class="fas fa-star me-2"></i>上級レベル
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="col-md-6 mb-4">
                    <div class="course-card">
                        <div class="course-icon">
                            <i class="fas fa-keyboard"></i>
                        </div>
                        <h2 class="course-title">Basic Lessons</h2>
                        <p class="course-description">Learn the fundamentals of English typing</p>
                        <a href="premium_lesson.php?lang=en&level=basic" class="course-button">Start Learning</a>
                        <div class="course-stats">
                            <i class="fas fa-clock me-2"></i>Basic Level
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="course-card">
                        <div class="course-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h2 class="course-title">Advanced Lessons</h2>
                        <p class="course-description">Master advanced English typing techniques</p>
                        <a href="premium_lesson.php?lang=en&level=advanced" class="course-button">Start Now</a>
                        <div class="course-stats">
                            <i class="fas fa-star me-2"></i>Advanced Level
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Common features for all languages -->
            <div class="col-md-6 mb-4">
                <div class="course-card">
                    <div class="course-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h2 class="course-title">
                        <?php if ($lang === 'my'): ?>
                            အသိအမှတ်ပြုလက်မှတ်
                        <?php elseif ($lang === 'jp'): ?>
                            認定証
                        <?php else: ?>
                            Certification
                        <?php endif; ?>
                    </h2>
                    <p class="course-description">
                        <?php if ($lang === 'my'): ?>
                            တရားဝင် အသိအမှတ်ပြုလက်မှတ် ရယူပါ
                        <?php elseif ($lang === 'jp'): ?>
                            公式認定証を取得しましょう
                        <?php else: ?>
                            Earn your official typing certificate
                        <?php endif; ?>
                    </p>
                    <a href="certificate_exam.php?lang=<?php echo $lang; ?>" class="course-button">
                        <?php if ($lang === 'my'): ?>
                            စတင်မည်
                        <?php elseif ($lang === 'jp'): ?>
                            始める
                        <?php else: ?>
                            Start Now
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>