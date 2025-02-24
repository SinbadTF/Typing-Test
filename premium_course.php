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

// Get language and category
$lang = $_GET['lang'] ?? 'en';
$category = $_GET['category'] ?? '';

// Add language-specific titles
$languageTitles = [
    'en' => 'English Premium Courses',
    'my' => 'Myanmar Premium Courses',
    'jp' => 'Japanese Premium Courses'
];

$title = $languageTitles[$lang] ?? $languageTitles['en'];

// Initialize arrays
$books = [];
$lessons = [];

// Fetch all books regardless of category selection
$stmt = $pdo->prepare("SELECT * FROM premium_books WHERE language = ? ORDER BY lesson_number");
$stmt->execute([$lang]);
$books = $stmt->fetchAll();

// Fetch all lessons regardless of category selection
$stmt = $pdo->prepare("SELECT * FROM premium_lessons WHERE language = ? ORDER BY lesson_number");
$stmt->execute([$lang]);
$lessons = $stmt->fetchAll();

// When displaying books, fetch from database
if ($category === 'books') {
    $stmt = $pdo->prepare("SELECT * FROM premium_books WHERE category = ? AND language = ? ORDER BY lesson_number");
    $stmt->execute(['books', $lang]);
    $books = $stmt->fetchAll();
}
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

        .container-fluid {
            display: flex;
        }

        .row {
            flex: 1;
        }

        .sidebar {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .sidebar-sticky {
            position: sticky;
            top: 0;
        }

        .sidebar-heading {
            color: #f0b232;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(240, 178, 50, 0.2);
        }

        .nav-link {
            color: #d1d0c5;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 10px;
            transition: all 0.4s ease;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .nav-link:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, #f0b232, #f7c157);
            transition: width 0.4s ease;
            z-index: 0;
            opacity: 0.2;
        }

        .nav-link:hover:before,
        .nav-link.active:before {
            width: 100%;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #f0b232;
            transform: translateX(5px);
        }

        .nav-link i {
            margin-right: 12px;
            position: relative;
            z-index: 1;
        }

        .sub-menu {
            padding-left: 30px;
        }

        .sub-menu a {
            display: block;
            color: #adb5bd;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .sub-menu a:hover {
            color: #f0b232;
            background: rgba(240, 178, 50, 0.05);
        }

        .lesson-box {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .lesson-box:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, #f0b232, #f7c157);
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: 0;
        }

        .lesson-box:hover:not(.locked) {
            transform: translateY(-10px);
            border-color: rgba(240, 178, 50, 0.3);
        }

        .lesson-box:hover:not(.locked):before {
            opacity: 0.1;
        }

        .lesson-number {
            font-size: 2.2rem;
            font-weight: 700;
            color: #f0b232;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 10px rgba(240, 178, 50, 0.3);
        }

        .lesson-title {
            color: #ffffff;
            font-size: 1rem;
            margin: 10px 0;
            font-weight: 500;
            position: relative;
            z-index: 1;
            line-height: 1.4;
        }

        .start-btn {
            background: linear-gradient(45deg, #f0b232, #f7c157);
            color: #0f172a;
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            position: relative;
            z-index: 1;
            transition: all 0.4s ease;
            font-size: 0.9rem;
        }

        .start-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(240, 178, 50, 0.3);
            color: #0f172a;
        }

        .lesson-box.locked {
            opacity: 0.5;
        }

        .locked-text {
            color: #d1d0c5;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .locked-text:before {
            content: '\f023';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
        }

        .col-md-4 {
            padding: 15px;
        }

        .row {
            margin: 0 -15px;
        }

        .level-container {
            padding: 20px;
        }

        .level-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .level-icon {
            width: 60px;
            height: 60px;
            background-color: #00e1d4;
            border-radius: 10px;
            padding: 10px;
        }

        .level-title {
            font-size: 24px;
            color: #e2b714;
            margin: 0;
        }

        .lessons-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            padding: 10px;
        }

        .category-header {
            margin-bottom: 30px;
        }

        .level-header {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: #2c2e31;
            border-radius: 10px;
        }

        .level-icon {
            width: 60px;
            height: 60px;
            background-color: #00e1d4;
            border-radius: 10px;
            padding: 10px;
        }

        .level-title {
            font-size: 24px;
            color: #e2b714;
            margin: 0;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 20px;
        }

        .category-card {
            background: #2c2e31;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .category-card:hover {
            transform: translateY(-5px);
        }

        .category-icon {
            font-size: 40px;
            color: #00e1d4;
            margin-bottom: 20px;
        }

        .category-card h2 {
            color: #e2b714;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .category-card p {
            color: #adb5bd;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .category-btn {
            background: #00e1d4;
            color: #2c2e31;
            padding: 10px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
        }

        .category-btn:hover {
            background: #00bfb3;
            color: #2c2e31;
            text-decoration: none;
        }

        .col-md-9 {
            padding: 20px;
        }

        .category-sidebar {
            background: #2c2e31;
            padding: 20px;
            border-radius: 10px;
            position: sticky;
            top: 20px;
        }

        .category-card {
            background: #1e1e1e;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .category-card:last-child {
            margin-bottom: 0;
        }

        .category-card.active {
            background: #00e1d4;
        }

        .category-card.active .lesson-number,
        .category-card.active .lesson-title {
            color: #2c2e31;
        }

        .welcome-message {
            text-align: center;
            padding: 50px;
            color: #adb5bd;
        }

        .welcome-message h2 {
            color: #e2b714;
            margin-bottom: 20px;
        }

        .category-title {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 20px 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            color: #f0b232;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .category-title:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 5px;
            background: linear-gradient(to bottom, #f0b232, #f7c157);
        }

        .category-title i {
            font-size: 2rem;
            margin-right: 15px;
            background: linear-gradient(45deg, #f0b232, #f7c157);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .lessons-container {
            padding: 15px;
            height: 100vh;
            overflow-y: auto;
        }

        .category-section {
            margin-bottom: 40px;
            padding-top: 15px;
        }

        .category-section.active {
            scroll-margin-top: 20px;
        }

        .category-title {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #1e1e1e;
            margin-bottom: 20px;
        }

        @media (min-width: 1200px) {
            .lessons-grid {
                gap: 20px;
            }
        }

        @media (max-width: 1199px) {
            .lesson-box {
                padding: 15px;
            }
            .lesson-number {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            .lessons-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .lessons-grid {
                grid-template-columns: 1fr;
            }
        }

        .lesson-difficulty {
            font-size: 0.8rem;
            color: #adb5bd;
            margin: 5px 0;
        }
        
        .lesson-title {
            font-size: 0.9rem;
            line-height: 1.3;
            margin: 8px 0;
            height: 2.6em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
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
        
        <div class="container-fluid">
            <div class="row">
                <!-- Left side: Categories -->
                <div class="col-md-3">
                    <div class="sidebar">
                        <div class="sidebar-sticky">
                            <h5 class="sidebar-heading">Categories</h5>
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $category === 'books' ? 'active' : ''; ?>" 
                                       href="?lang=<?php echo $lang; ?>&category=books">
                                        <i class="fas fa-book me-2"></i>Books
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $category === 'lyrics' ? 'active' : ''; ?>" 
                                       href="?lang=<?php echo $lang; ?>&category=lyrics">
                                        <i class="fas fa-music me-2"></i>Song Lyrics
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $category === 'coding' ? 'active' : ''; ?>" 
                                       href="?lang=<?php echo $lang; ?>&category=coding">
                                        <i class="fas fa-code me-2"></i>Coding
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $category === 'knowledge' ? 'active' : ''; ?>" 
                                       href="?lang=<?php echo $lang; ?>&category=knowledge">
                                        <i class="fas fa-brain me-2"></i>Knowledge
                                    </a>
                                </li>
                                <li class="nav-item">
                        <a class="nav-link <?php echo $category === 'certification' ? 'active' : ''; ?>" 
                           href="?lang=<?php echo $lang; ?>&category=certification">
                            <i class="fas fa-certificate me-2"></i>Certification
                        </a>
                    </li>
                    <li class="nav-item">
                                    <a class="nav-link <?php echo $category === 'custom' ? 'active' : ''; ?>" 
                                       href="?lang=<?php echo $lang; ?>&category=custom">
                                        <i class="fas fa-keyboard me-2"></i>Custom Practice
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right side: Lessons -->
                <div class="col-md-9">
                    <!-- Books Section -->
                    <div id="books-section" class="category-section">
                        <h2 class="category-title"><i class="fas fa-book me-2"></i>Classic Literature</h2>
                        <div class="lessons-grid">
                            <?php for ($i = 1; $i <= 9; $i++): ?>
                                <div class="lesson-box">
                                    <div class="lesson-number"><?php echo $i; ?></div>
                                    <h3 class="lesson-title">
                                        <?php 
                                        $title = "Book " . $i;
                                        if (!empty($books)) {
                                            foreach ($books as $book) {
                                                if ((int)$book['lesson_number'] === $i) {
                                                    $title = htmlspecialchars($book['title']);
                                                    break;
                                                }
                                            }
                                        }
                                        echo $title;
                                        ?>
                                    </h3>
                                    <a href="premium_lesson.php?lang=<?php echo $lang; ?>&category=books&lesson=<?php echo $i; ?>" 
                                       class="start-btn">Start</a>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <!-- Lyrics Section -->
                    <div id="lyrics-section" class="category-section">
                        <h2 class="category-title"><i class="fas fa-music me-2"></i>Song Lyrics</h2>
                        <div class="lessons-grid">
                            <?php 
                            // Define song lessons
                            $songLessons = [
                                1 => ['title' => 'Perfect - Ed Sheeran', 'difficulty' => 'Easy'],
                                2 => ['title' => 'Someone Like You - Adele', 'difficulty' => 'Medium'],
                                3 => ['title' => 'Shape of You - Ed Sheeran', 'difficulty' => 'Medium'],
                                4 => ['title' => 'All of Me - John Legend', 'difficulty' => 'Medium'],
                                5 => ['title' => 'Hello - Adele', 'difficulty' => 'Hard'],
                                6 => ['title' => 'Stay With Me - Sam Smith', 'difficulty' => 'Medium'],
                                7 => ['title' => 'Thinking Out Loud - Ed Sheeran', 'difficulty' => 'Hard'],
                                8 => ['title' => 'Rolling in the Deep - Adele', 'difficulty' => 'Hard'],
                                9 => ['title' => 'Just the Way You Are - Bruno Mars', 'difficulty' => 'Medium']
                            ];

                            for ($i = 1; $i <= 9; $i++): 
                            ?>
                                <div class="lesson-box">
                                    <div class="lesson-number"><?php echo $i; ?></div>
                                    <h3 class="lesson-title">
                                        <?php echo htmlspecialchars($songLessons[$i]['title']); ?>
                                    </h3>
                                    <div class="lesson-difficulty">
                                        <?php echo $songLessons[$i]['difficulty']; ?>
                                    </div>
                                    <a href="premium_lesson.php?lang=<?php echo $lang; ?>&category=lyrics&lesson=<?php echo $i; ?>" 
                                       class="start-btn">Start</a>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <!-- Coding Section -->
                    <div id="coding-section" class="category-section">
                        <h2 class="category-title"><i class="fas fa-code me-2"></i>Programming Practice</h2>
                        <div class="lessons-grid">
                            <?php for ($i = 1; $i <= 9; $i++): ?>
                                <div class="lesson-box">
                                    <div class="lesson-number"><?php echo $i; ?></div>
                                    <h3 class="lesson-title">Code <?php echo $i; ?></h3>
                                    <a href="premium_lesson.php?lang=<?php echo $lang; ?>&category=coding&lesson=<?php echo $i; ?>" 
                                       class="start-btn">Start</a>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <!-- Knowledge Section -->
                    <div id="knowledge-section" class="category-section">
                        <h2 class="category-title"><i class="fas fa-brain me-2"></i>Educational Content</h2>
                        <div class="lessons-grid">
                            <?php for ($i = 1; $i <= 9; $i++): ?>
                                <div class="lesson-box">
                                    <div class="lesson-number"><?php echo $i; ?></div>
                                    <h3 class="lesson-title">Topic <?php echo $i; ?></h3>
                                    <a href="premium_lesson.php?lang=<?php echo $lang; ?>&category=knowledge&lesson=<?php echo $i; ?>" 
                                       class="start-btn">Start</a>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    

                    <!-- Add this section before the closing div.col-md-9 -->
                    <div id="certification-section" class="category-section">
                        <h2 class="category-title"><i class="fas fa-certificate me-2"></i>Typing Certification</h2>
                        <div class="lesson-box" style="max-width: 400px; margin: 0 auto;">
                            <div class="lesson-number">
                                <i class="fas fa-award" style="color: #f0b232; font-size: 2.5rem;"></i>
                            </div>
                            <h3 class="lesson-title">Official Typing Certificate</h3>
                            <div class="lesson-difficulty">
                                Requirements:
                                <ul style="list-style: none; padding: 0; margin: 10px 0;">
                                    <li><i class="fas fa-tachometer-alt me-2"></i>40 WPM</li>
                                    <li><i class="fas fa-check-circle me-2"></i>95% Accuracy</li>
                                    <li><i class="fas fa-clock me-2"></i>5 Minutes Test</li>
                                </ul>
                            </div>
                            <a href="certificate_exam.php" class="start-btn">
                                Take Certification Exam
                            </a>
                        </div>
                    </div>
                    
                    <!-- Add new Custom Practice feature box -->
                    <div id="custom-practice-section" class="category-section">
                        <h2 class="category-title"><i class="fas fa-keyboard me-2"></i>Custom Practice</h2>
                        <div class="lesson-box" style="max-width: 400px; margin: 0 auto;">
                            <div class="lesson-number">
                                <i class="fas fa-pencil-alt" style="color: #f0b232; font-size: 2.5rem;"></i>
                            </div>
                            <h3 class="lesson-title">Create Your Own Practice</h3>
                            <div class="lesson-difficulty">
                                Features:
                                <ul style="list-style: none; padding: 0; margin: 10px 0;">
                                    <li><i class="fas fa-file-alt me-2"></i>Custom Text Input</li>
                                    <li><i class="fas fa-cog me-2"></i>Adjustable Settings</li>
                                    <li><i class="fas fa-chart-line me-2"></i>Real-time Statistics</li>
                                </ul>
                            </div>
                            <a href="premium_custom_practice.php" class="start-btn">
                                Start Custom Practice
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the category from URL
        const urlParams = new URLSearchParams(window.location.search);
        const category = urlParams.get('category');
        
        if (category) {
            // Scroll to the selected category section
            const section = document.getElementById(category + '-section');
            if (section) {
                section.scrollIntoView({ behavior: 'smooth' });
            }
        }

        // Add click handlers for category links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                const category = this.href.split('category=')[1];
                const section = document.getElementById(category + '-section');
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    });
    </script>
</body>
</html>