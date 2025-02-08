<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';
?>

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
        .container {
            max-width: 1400px;
            padding: 0 30px;
        }
        .hero-section {
            padding: 120px 0;
            margin: 80px auto 60px;
            max-width: 1200px;
        }
        .lessons-section, .features-section {
            max-width: 1200px;
            padding: 80px 50px;
            margin: 30px auto;
            width: 100%;
        }
        .row {
            max-width: 1000px;
            margin: 0 auto;
        }
        .lesson-card {
            max-width: 550px;
            margin: 0 auto 25px;
        }
        .feature-card {
            max-width: 550;
            margin: 0 auto;
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
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #007bff, #00ff88, #007bff);
            background-size: 200% 100%;
            animation: gradient 3s linear infinite;
        }
        .hero-title {
            font-size: 4.5rem;
            font-weight: 800;
            margin-bottom: 2rem;
            background: linear-gradient(45deg, #007bff, #00ff88, #007bff);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient 3s linear infinite;
        }
        .hero-subtitle {
            font-size: 1.3rem;
            color: #adb5bd;
            margin-bottom: 3rem;
        }
        .lessons-section {
            padding: 100px 0;
            background: rgba(35, 35, 35, 0.95);
            border-radius: 30px;
            margin: 50px auto;
            box-shadow: 0 20px 60px rgba(0,123,255,0.15);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .lesson-card {
            background: rgba(25, 25, 25, 0.95);
            border-radius: 25px;
            padding: 35px;
            display: flex;
            align-items: center;
            gap: 30px;
            transition: all 0.4s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .lesson-icon {
            background: linear-gradient(45deg, #007bff, #00ff88);
            width: 80px;
            height: 80px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            box-shadow: 0 10px 25px rgba(0,123,255,0.3);
            transition: all 0.4s ease;
        }
        .lesson-content h4 {
            font-size: 1.4rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #ffffff, #adb5bd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .features-section {
            padding: 100px 0;
            background: rgba(35, 35, 35, 0.95);
            border-radius: 30px;
            margin: 50px auto;
            box-shadow: 0 20px 60px rgba(0,123,255,0.15);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .feature-card {
            background: rgba(25, 25, 25, 0.95);
            border-radius: 25px;
            padding: 40px;
            margin: 20px 0;
            transition: all 0.4s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            height: 100%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .feature-icon {
            font-size: 3.5rem;
            margin-bottom: 2rem;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 10px 20px rgba(0,123,255,0.2);
        }
        .feature-card h3 {
            font-size: 1.6rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #ffffff, #adb5bd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .feature-card p {
            color: #adb5bd;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        .btn-primary {
            background: linear-gradient(45deg, #007bff, #00ff88);
            border: none;
            padding: 18px 48px;
            font-size: 1.3rem;
            border-radius: 15px;
            font-weight: 600;
            transition: all 0.4s ease;
            box-shadow: 0 10px 25px rgba(0,123,255,0.3);
        }
        .btn-outline-primary {
            color: #fff;
            border: 2px solid #007bff;
            border-radius: 12px;
            padding: 10px 25px;
            transition: all 0.3s ease;
            background: transparent;
            font-weight: 500;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
     <style>
        /* Add these new styles */
        .hero-section {
            position: relative;
            overflow: hidden;
            padding: 150px 0;
            background: rgba(25, 25, 25, 0.7);
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 60px rgba(0,123,255,0.15);
        }

        .hero-section::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0,123,255,0.1) 0%, rgba(0,255,136,0.1) 100%);
            z-index: -1;
        }

        .hero-title {
            font-size: 5rem;
            letter-spacing: -1px;
            text-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .lesson-card {
            transform: translateY(0);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .lesson-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .lesson-icon {
            transform: scale(1);
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .lesson-card:hover .lesson-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .feature-card {
            transform: translateY(0);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #007bff, #00ff88);
            transform: scaleX(0);
            transition: transform 0.5s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .btn-primary {
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, #00ff88, #007bff);
            opacity: 0;
            z-index: -1;
            transition: opacity 0.3s ease;
        }

        .btn-primary:hover::before {
            opacity: 1;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(45deg, #007bff, #00ff88);
            border-color: transparent;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,123,255,0.3);
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .feature-icon {
            animation: float 3s ease-in-out infinite;
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
                    <a class="nav-link d-flex align-items-center" href="profile.php">
                        <?php if (isset($_SESSION['profile_image']) && $_SESSION['profile_image']): ?>
                            <img src="uploads/profile_images/<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" 
                                 class="rounded-circle me-2" 
                                 width="30" 
                                 height="30" 
                                 alt="Profile">
                        <?php else: ?>
                            <i class="fas fa-user-circle me-2"></i>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                    
                <?php endif; ?>
                <a class="nav-link" href="<?php 
                    if (!isset($_SESSION['user_id'])) {
                        echo 'login.php';
                    } else {
                        $stmt = $pdo->prepare("SELECT is_premium FROM users WHERE user_id = ?");
                        $stmt->execute([$_SESSION['user_id']]);
                        $user = $stmt->fetch();
                        echo ($user && $user['is_premium'] == 1) ? 'premium_course.php' : 'premium.php';
                    }
                ?>" class="btn btn-primary">Premium Lessons</a>
                
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
                            <h4>Course</h4>
                            <p>Learn the proper way to position your fingers on the home row keys.</p>
                            <button class="btn btn-outline-primary btn-sm" id="startCourseBtn">Start Course</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="lesson-card">
                        <div class="lesson-icon">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div class="lesson-content">
                            <!-- Update the premium lessons link in the navbar -->
                            <a class="nav-link" href="<?php 
                                if (!isset($_SESSION['user_id'])) {
                                    echo 'login.php';
                                } else {
                                    $stmt = $pdo->prepare("SELECT is_premium FROM users WHERE user_id = ?");
                                    $stmt->execute([$_SESSION['user_id']]);
                                    $user = $stmt->fetch();
                                    echo ($user && $user['is_premium'] == 1) ? 'premium_course.php' : 'premium.php';
                                }
                            ?>">
                            </a>
                            
                            <!-- Update the premium card link in the lessons section -->
                            <div class="lesson-content">
                                <h4>Premium</h4>
                                <p>Practice typing the most frequently used words in English.</p>
                                <a href="<?php 
                                    if (!isset($_SESSION['user_id'])) {
                                        echo 'login.php';
                                    } else {
                                        $stmt = $pdo->prepare("SELECT is_premium FROM users WHERE user_id = ?");
                                        $stmt->execute([$_SESSION['user_id']]);
                                        $user = $stmt->fetch();
                                        echo ($user && $user['is_premium'] == 1) ? 'premium_course.php' : 'premium.php';
                                    }
                                ?>" class="btn btn-outline-primary btn-sm">Premium Lessons</a>
                            </div>
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

    <!-- Add this modal after the features-section div -->
    <div class="modal fade" id="languageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Choose Your Language</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-3">
                        <a href="course.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-language me-2"></i>English
                        </a>
                        <a href="japanese__course.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-language me-2"></i>日本語
                        </a>
                        <a href="#" class="btn btn-outline-light btn-lg disabled">
                            <i class="fas fa-language me-2"></i>မြန်မာ (Coming Soon)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Add this before the closing body tag -->
        <script>
            document.getElementById('startCourseBtn').addEventListener('click', function() {
                new bootstrap.Modal(document.getElementById('languageModal')).show();
            });
        </script>
    </body>
    </html>