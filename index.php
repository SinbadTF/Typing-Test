
<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';
?>
<!--Htet Htet is commanding-->
<!-- another -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Typing Test - Improve Your Typing Skills</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            min-height: 100vh;
            padding-bottom: 30px;
            font-family: 'Inter', sans-serif;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Add animated background effect */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 30%, rgba(0, 123, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(0, 255, 136, 0.1) 0%, transparent 50%);
            z-index: -1;
            animation: backgroundPulse 10s ease-in-out infinite;
        }

        @keyframes backgroundPulse {
            0% { opacity: 0.5; }
            50% { opacity: 1; }
            100% { opacity: 0.5; }
        }

        .navbar {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 123, 255, 0.1);
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            color: #ffffff !important;
            opacity: 0.8;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
            border-radius: 8px;
        }

        .nav-link:hover {
            opacity: 1;
            background: rgba(255, 255, 255, 0.1);
        }

        .container {
            max-width: 1200px;
            padding: 0 20px;
        }

        .hero-section {
            position: relative;
            padding: 80px 0;
            margin: 30px auto;
            max-width: 1000px;
            text-align: center;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                linear-gradient(45deg, rgba(0, 123, 255, 0.1), transparent),
                linear-gradient(-45deg, rgba(0, 255, 136, 0.1), transparent);
            z-index: -1;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .hero-subtitle {
            font-size: 1.1rem;
            color: #adb5bd;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-primary {
            padding: 12px 32px;
            font-size: 1rem;
        }

        .lessons-section {
            padding: 50px 0;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            margin: 30px auto;
            max-width: 1000px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
        }

        .lesson-card {
            background: rgba(15, 23, 42, 0.95);
            border-radius: 20px;
            padding: 25px;
            height: auto;
            max-width: 450px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.4s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 
                0 10px 30px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }

        .lesson-card:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 20px 40px rgba(0, 123, 255, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .lesson-icon {
            background: linear-gradient(45deg, #007bff, #00ff88);
            min-width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            box-shadow: 0 10px 25px rgba(0,123,255,0.3);
        }

        .lesson-content {
            flex: 1;
        }

        .lesson-content h4 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #ffffff, #adb5bd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .lesson-content p {
            font-size: 0.95rem;
            color: #adb5bd;
            margin-bottom: 1rem;
        }

        .btn-outline-primary {
            padding: 8px 20px;
            font-size: 0.9rem;
        }

        .features-section {
            padding: 40px 0;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            margin: 30px auto;
            max-width: 1000px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        .feature-card {
            background: rgba(15, 23, 42, 0.95);
            border-radius: 16px;
            padding: 20px;
            height: auto;
            margin-bottom: 15px;
            max-width: 300px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 
                0 10px 30px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 20px 40px rgba(0, 123, 255, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .feature-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .feature-card h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: #ffffff;
        }

        .feature-card p {
            font-size: 0.9rem;
            color: #adb5bd;
            margin-bottom: 0;
            line-height: 1.4;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(0,123,255,0.4);
        }

        .modal-content {
            border-radius: 16px;
            padding: 20px;
        }

        .modal-dialog {
            max-width: 400px;
        }

        .row {
            margin-bottom: 0;
        }

        .col-md-4, .col-md-6 {
            padding: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-keyboard me-2"></i>Boku no Typing
            </a>
            <div class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="profile.php">
                        <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                <?php endif; ?>
                <a class="nav-link" href="premium.php">
                    <i class="fas fa-crown me-2"></i>Premium
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="hero-section">
            <h1 class="hero-title">Master Your Typing Skills</h1>
            <p class="hero-subtitle">Improve your typing speed and accuracy with our advanced typing test platform</p>
            <a href="typing_test.php" class="btn btn-primary">Start Typing Practice</a>
        </div>

        <div class="lessons-section">
            <h2 class="section-title">Typing Lessons</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="lesson-card">
                        <div class="lesson-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="lesson-content">
                            <h4>Official Course</h4>
                            <p>Learn the proper way to position your fingers on the home row keys.</p>
                            <a href="#" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#languageModal">
                                <i class="fas fa-play me-2"></i>Start Course
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="lesson-card">
                        <div class="lesson-icon">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div class="lesson-content">
                            <h4>Premium</h4>
                            <p>Practice typing the most frequently used words in English.</p>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <?php
                                    $stmt = $pdo->prepare("SELECT is_premium FROM users WHERE user_id = ?");
                                    $stmt->execute([$_SESSION['user_id']]);
                                    $user = $stmt->fetch();
                                    
                                    if ($user && $user['is_premium'] == 1):
                                ?>
                                    <a href="#" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#premiumLanguageModal">
                                        <i class="fas fa-play me-2"></i>Premium Lessons
                                    </a>
                                <?php else: ?>
                                    <a href="premium.php" class="btn btn-outline-primary btn-sm">Get Premium</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-outline-primary btn-sm">Premium Lessons</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="features-section">
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">⚡</div>
                        <h3>Speed Training</h3>
                        <p>Enhance your typing speed with progressive exercises and real-time feedback</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">🎯</div>
                        <h3>Accuracy Focus</h3>
                        <p>Improve your accuracy with our advanced error tracking and analysis</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">📊</div>
                        <h3>Progress Tracking</h3>
                        <p>Monitor your improvement with detailed statistics and progress reports</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Language selection modal -->
    <div class="modal fade" id="languageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Choose Your Language</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-3">
                        <a href="course.php?lang=en" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-language me-2"></i>English
                        </a>
                        <a href="course.php?lang=my" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-language me-2"></i>မြန်မာ
                        </a>
                        <a href="course.php?lang=jp" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-language me-2"></i>日本語
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add premium language modal -->
    <div class="modal fade" id="premiumLanguageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Choose Your Language</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-3">
                        <a href="premium_course.php?lang=en" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-language me-2"></i>English
                        </a>
                        <a href="premium_course.php?lang=my" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-language me-2"></i>မြန်မာ
                        </a>
                        <a href="premium_course.php?lang=jp" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-language me-2"></i>日本語
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this modal for login prompt -->
    <div class="modal fade" id="loginPromptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Login Required</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-lock text-warning mb-3" style="font-size: 3rem;"></i>
                    <h4 class="mb-3">Please Log In</h4>
                    <p class="text-muted mb-4">You need to log in to access premium lessons</p>
                    <a href="login.php" class="btn btn-primary me-2">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>