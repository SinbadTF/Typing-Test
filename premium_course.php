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
        /* Base Styles */
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }

        /* Layout */
        .course-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .container-fluid {
            display: flex;
            overflow: visible;
            min-height: 100vh;
        }

        .row {
            flex: 1;
            position: relative;
            margin: 0 -15px;
        }

        /* Sidebar Styles */
        .sidebar {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 20px;
            height: calc(100vh - 40px);
            overflow-y: auto;
        }

        .sidebar-heading {
            color: #f0b232;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(240, 178, 50, 0.2);
        }

        /* Navigation Links */
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

        /* Category Section */
        .category-section {
            margin-bottom: 40px;
            padding-top: 15px;
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
            position: sticky;
            top: 0;
            z-index: 10;
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

        /* Lesson Grid */
        .lessons-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            padding: 10px;
        }

        .lesson-box {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.4s ease;
            text-align: center;
            position: relative;
            overflow: hidden;
            display: grid;
            grid-template-rows: 240px auto 30px; /* Increased first row height */
            height: 340px; /* Increased total height */
            width: 100%;
            gap: 8px;
        }

        /* Top part - Image container */
        .book-cover-container {
            width: 100%;
            height: 240px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }

        .book-cover {
            width: 160px; /* Increased width */
            height: 220px; /* Increased height */
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease;
        }

        /* Middle part - Title */
        .book-title {
            font-size: 0.9rem;
            color: #f0b232;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 10px;
        }

        /* Bottom part - Button */
        .start-btn {
            background: linear-gradient(45deg, #f0b232, #f7c157);
            color: #0f172a;
            padding: 4px 12px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            width: 60px;
            align-self: center;
            justify-self: center;
            margin-bottom: 5px;
        }

        .lesson-box:hover:not(.locked) {
            transform: translateY(-10px);
            border-color: rgba(240, 178, 50, 0.3);
        }

        /* Responsive Design */
        @media (min-width: 1200px) {
            .lessons-grid { gap: 20px; }
        }

        @media (max-width: 1199px) {
            .lesson-box { padding: 15px; }
        }

        @media (max-width: 768px) {
            .lessons-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 576px) {
            .lessons-grid { grid-template-columns: 1fr; }
        }

        /* Update song cover styles */
        .song-cover {
            width: 180px; /* Increased width */
            height: 180px; /* Increased height to match width for perfect circle */
            object-fit: cover;
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease;
            margin: 20px auto; /* Add margin to center vertically */
        }

        /* Add hover effect for song covers */
        .lesson-box:hover .song-cover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
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
                            <?php 
                            $books = [
                                [
                                    'title' => 'Harry Potter1',
                                    'image' => 'assets/images/books/harry.jpg'
                                ],
                                [
                                    'title' => 'The Great Gatsby',
                                    'image' => 'assets/images/books/great.jpg'
                                ],
                                [
                                    'title' => 'Little Women',
                                    'image' => 'assets/images/books/little.jpg'
                                ],
                                [
                                    'title' => 'To Kill a Mockingbird',
                                    'image' => 'assets/images/books/toKill.jpg'
                                ],
                                [
                                    'title' => 'The Catcher in the Rye',
                                    'image' => 'assets/images/books/catcher.jpg'
                                ],
                                [
                                    'title' => 'Lord of the Flies',
                                    'image' => 'assets/images/books/lord.jpg'
                                ],
                                [
                                    'title' => 'The Hobbit',
                                    'image' => 'assets/images/books/hobbit.jpg'
                                ],
                                [
                                    'title' => 'Brave New World',
                                    'image' => 'assets/images/books/brave-new-world.jpg'
                                ],
                                [
                                    'title' => 'Animal Farm',
                                    'image' => 'assets/images/books/animal-farm.jpg'
                                ]
                            ];

                            foreach ($books as $index => $book): ?>
                                <div class="lesson-box">
                                    <div class="book-cover-container">
                                        <img src="<?php echo htmlspecialchars($book['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($book['title']); ?>" 
                                             class="book-cover">
                                    </div>
                                    <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                                    <a href="premium_lesson.php?lang=<?php echo $lang; ?>&category=books&lesson=<?php echo $index + 1; ?>" 
                                       class="start-btn">Start</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Lyrics Section -->
                    <div id="lyrics-section" class="category-section">
                        <h2 class="category-title"><i class="fas fa-music me-2"></i>Song Lyrics</h2>
                        <div class="lessons-grid">
                            <?php 
                            $songLessons = [
                                [
                                    'title' => 'Perfect',
                                    'image' => 'assets/images/songs/perfect.jpg'
                                ],
                                [
                                    'title' => 'Someone Like You',
                                    'image' => 'assets/images/songs/someoneLikeyou.jpg'
                                ],
                                [
                                    'title' => 'Shape of You',
                                    'image' => 'assets/images/songs/shape-of-you.jpg'
                                ],
                                [
                                    'title' => 'All of Me',
                                    'artist' => 'John Legend',
                                    'image' => 'assets/images/songs/all-of-me.jpg'
                                ],
                                [
                                    'title' => 'Hello',
                                    'artist' => 'Adele',
                                    'image' => 'assets/images/songs/hello.jpg'
                                ],
                                [
                                    'title' => 'Stay With Me',
                                    'artist' => 'Sam Smith',
                                    'image' => 'assets/images/songs/stay-with-me.jpg'
                                ],
                                [
                                    'title' => 'Thinking Out Loud',
                                    'artist' => 'Ed Sheeran',
                                    'image' => 'assets/images/songs/think-out-loud.jpg'
                                ],
                                [
                                    'title' => 'Rolling in the Deep',
                                    'artist' => 'Adele',
                                    'image' => 'assets/images/songs/rolling-in-the-deep.jpg'
                                ],
                                [
                                    'title' => 'Just the Way You Are',
                                    'artist' => 'Bruno Mars',
                                    'image' => 'assets/images/songs/just-the-way-you-are.jpg'
                                ]
                            ];

                            foreach ($songLessons as $index => $song): ?>
                                <div class="lesson-box">
                                    <div class="book-cover-container">
                                        <img src="<?php echo htmlspecialchars($song['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($song['title']); ?>" 
                                             class="song-cover">
                                    </div>
                                    <h3 class="book-title">
                                        <?php echo htmlspecialchars($song['title']); ?>
                                    </h3>
                                    <a href="premium_lesson.php?lang=<?php echo $lang; ?>&category=lyrics&lesson=<?php echo $index + 1; ?>" 
                                       class="start-btn">Start</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                   
                    <div id="programming-section" class="category-section">
                        <!-- Update the programming section ID to match the category -->
                        <div id="coding-section" class="category-section">
                            <h2 class="category-title"><i class="fas fa-code me-2"></i>Programming Codes Practice</h2>
                            <div class="lesson-box" style="max-width: 400px; margin: 0 auto; height: auto; display: flex; flex-direction: column; align-items: center;">
                                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px;">
                                    <i class="fas fa-code me-2" style="color: #f0b232; font-size: 3.5rem; margin-bottom: 20px;"></i>
                                    <h3 class="book-title" style="font-size: 1.2rem; margin: 15px 0;">Start Coding Practice</h3>
                                </div>
                                <a href="programming_test.php" class="start-btn" style="width: auto; padding: 8px 20px; margin: 15px 0;">
                                    Start Practice 
                                </a>
                            </div>
                        </div>

                   
                    <div id="certification-section" class="category-section">
                        <h2 class="category-title"><i class="fas fa-certificate me-2"></i>Typing Certification</h2>
                        <div class="lesson-box" style="max-width: 400px; margin: 0 auto; height: auto; display: flex; flex-direction: column; align-items: center;">
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px;">
                                <i class="fas fa-award" style="color: #f0b232; font-size: 3.5rem; margin-bottom: 20px;"></i>
                                <h3 class="book-title" style="font-size: 1.2rem; margin: 15px 0;">Official Typing Certificate</h3>
                                <div class="lesson-difficulty" style="text-align: center; margin: 15px 0;">
                                    <div style="font-weight: 600; color: #f0b232; margin-bottom: 10px;">Requirements:</div>
                                    <ul style="list-style: none; padding: 0; margin: 10px 0;">
                                        <li style="margin: 8px 0;"><i class="fas fa-tachometer-alt me-2"></i>40 WPM</li>
                                        <li style="margin: 8px 0;"><i class="fas fa-check-circle me-2"></i>95% Accuracy</li>
                                        <li style="margin: 8px 0;"><i class="fas fa-clock me-2"></i>5 Minutes Test</li>
                                    </ul>
                                </div>
                            </div>
                            <a href="certificate_exam.php" class="start-btn" style="width: auto; padding: 8px 20px; margin: 15px 0;">
                                Take Certification Exam
                            </a>
                        </div>
                    </div>
                    
                   
                    <!-- Change from custom-practice-section to custom-section -->
                    <div id="custom-section" class="category-section">
                        <h2 class="category-title"><i class="fas fa-keyboard me-2"></i>Custom Practice</h2>
                        <div class="lesson-box" style="max-width: 400px; margin: 0 auto; height: auto; display: flex; flex-direction: column; align-items: center;">
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px;">
                                <i class="fas fa-pencil-alt" style="color: #f0b232; font-size: 3.5rem; margin-bottom: 20px;"></i>
                                <h3 class="book-title" style="font-size: 1.2rem; margin: 15px 0;">Create Your Own Practice</h3>
                                <div class="lesson-difficulty" style="text-align: center; margin: 15px 0;">
                                    <div style="font-weight: 600; color: #f0b232; margin-bottom: 10px;">Features:</div>
                                    <ul style="list-style: none; padding: 0; margin: 10px 0;">
                                        <li style="margin: 8px 0;"><i class="fas fa-file-alt me-2"></i>Custom Text Input</li>
                                        <li style="margin: 8px 0;"><i class="fas fa-cog me-2"></i>Adjustable Settings</li>
                                        <li style="margin: 8px 0;"><i class="fas fa-chart-line me-2"></i>Real-time Statistics</li>
                                    </ul>
                                </div>
                            </div>
                            <a href="premium_custom_practice.php" class="start-btn" style="width: auto; padding: 8px 20px; margin: 15px 0;">
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
        // Get all sections and nav links
        const sections = document.querySelectorAll('.category-section');
        const navLinks = document.querySelectorAll('.nav-link');
        
        // Add scroll event listener
        window.addEventListener('scroll', () => {
            let current = '';
            
            sections.forEach(section => {
                // Get section position
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                
                // Check if we've scrolled to this section
                if (window.pageYOffset >= sectionTop - 200) { // 200px offset for better UX
                    current = section.getAttribute('id').replace('-section', '');
                }
            });
            
            // Update active state of nav links
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.href.includes(`category=${current}`)) {
                    link.classList.add('active');
                }
            });
        });

        // Existing click handlers
        const urlParams = new URLSearchParams(window.location.search);
        const category = urlParams.get('category');
        
        if (category) {
            const section = document.getElementById(category + '-section');
            if (section) {
                section.scrollIntoView({ behavior: 'smooth' });
            }
        }

        // Smooth scroll for category links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent default navigation
                const category = new URLSearchParams(this.href.split('?')[1]).get('category');
                const section = document.getElementById(category + '-section');
                
                if (section) {
                    // Update URL without reloading
                    const newUrl = `${window.location.pathname}?category=${category}`;
                    window.history.pushState({ path: newUrl }, '', newUrl);
                    
                    // Smooth scroll to section
                    section.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // Add intersection observer for better performance
        const observerOptions = {
            root: null,
            rootMargin: '-20% 0px -60% 0px',
            threshold: 0
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const current = entry.target.getAttribute('id').replace('-section', '');
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.href.includes(`category=${current}`)) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        }, observerOptions);

        // Observe all sections
        sections.forEach(section => observer.observe(section));
    });
    </script>
</body>
</html>