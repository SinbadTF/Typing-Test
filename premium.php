<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Features - Boku no Typing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            color: #fff;
            position: relative;
            overflow-x: hidden;
        }

        /* Enhanced animated background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 30%, rgba(240, 178, 50, 0.1) 0%, transparent 70%),
                radial-gradient(circle at 80% 70%, rgba(247, 193, 87, 0.1) 0%, transparent 70%);
            z-index: -1;
            animation: backgroundPulse 15s ease-in-out infinite;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            padding-top: 20px;
        }

        .premium-hero {
            text-align: center;
            padding: 50px 30px;
            position: relative;
            background: rgba(15, 23, 42, 0.7);
            border-radius: 24px;
            margin: 30px auto;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(240, 178, 50, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 900px;
        }

        .premium-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #f0b232, #f7c157, #f0b232);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shimmer 3s linear infinite;
        }

        .premium-subtitle {
            font-size: 1.1rem;
            color: #adb5bd;
            max-width: 600px;
            margin: 0 auto 1.5rem;
            line-height: 1.6;
        }

        .pricing-section {
            background: rgba(15, 23, 42, 0.7);
            border-radius: 24px;
            padding: 40px;
            margin: 30px auto;
            max-width: 500px;
            text-align: center;
            border: 1px solid rgba(240, 178, 50, 0.2);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.3),
                0 0 80px rgba(240, 178, 50, 0.1);
            backdrop-filter: blur(10px);
            transform: translateY(0);
            transition: transform 0.3s ease;
        }

        .pricing-section:hover {
            transform: translateY(-5px);
        }

        .price-tag {
            font-size: 3rem;
            font-weight: 800;
            color: #f0b232;
            margin: 1.5rem 0;
            text-shadow: 0 2px 10px rgba(240, 178, 50, 0.3);
        }

        .price-period {
            font-size: 1rem;
            color: #adb5bd;
            margin-left: 5px;
        }

        .premium-features-list {
            list-style: none;
            padding: 0;
            margin: 2rem auto;
            max-width: 400px;
        }

        .premium-features-list li {
            background: rgba(240, 178, 50, 0.1);
            padding: 12px 20px;
            margin: 10px 0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .premium-features-list li::before {
            content: '✓';
            color: #f0b232;
            margin-right: 10px;
            font-weight: bold;
        }

        .premium-features-list li:hover {
            transform: translateX(5px);
            background: rgba(240, 178, 50, 0.15);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            padding: 20px;
            margin: 40px auto;
            max-width: 1000px;
        }

        .feature-box {
            background: rgba(15, 23, 42, 0.7);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.4s ease;
            border: 1px solid rgba(240, 178, 50, 0.1);
            backdrop-filter: blur(10px);
            animation: float 6s ease-in-out infinite;
        }

        .feature-box:nth-child(2) {
            animation-delay: 1s;
        }

        .feature-box:nth-child(3) {
            animation-delay: 2s;
        }

        .feature-box:hover {
            transform: translateY(-10px);
            border-color: rgba(240, 178, 50, 0.3);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.2),
                0 0 60px rgba(240, 178, 50, 0.1);
        }

        .premium-button {
            background: linear-gradient(45deg, #f0b232, #f7c157);
            color: #0f172a;
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            border: none;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .premium-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(240, 178, 50, 0.3);
            color: #0f172a;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes shimmer {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }

        @keyframes backgroundPulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .premium-title {
                font-size: 2.5rem;
            }
            .pricing-section {
                padding: 30px 20px;
            }
            .price-tag {
                font-size: 2.5rem;
            }
            .features-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Navbar styling */
        .navbar {
            background: rgba(15, 23, 42, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(240, 178, 50, 0.1);
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(240, 178, 50, 0.1);
            transition: transform 0.3s ease;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar.navbar-hidden {
            transform: translateY(-100%);
        }

        .navbar-brand {
            font-size: 1.3rem;
            font-weight: 600;
            background: linear-gradient(45deg, #f0b232, #f7c157);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: all 0.3s ease;
            padding: 0.3rem 0;
        }

        .navbar-brand:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .nav-link {
            color: #ffffff !important;
            opacity: 0.8;
            transition: all 0.3s ease;
            padding: 0.3rem 0.8rem;
            margin: 0 0.2rem;
            border-radius: 6px;
            font-size: 0.95rem;
        }

        .nav-link:hover {
            opacity: 1;
            background: rgba(240, 178, 50, 0.1);
            transform: translateY(-1px);
        }

        /* Add golden glow to active nav items */
        .nav-link.active {
            color: #f0b232 !important;
            background: rgba(240, 178, 50, 0.1);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <div class="premium-hero">
            <h1 class="premium-title">Unlock Premium Features</h1>
            <p class="premium-subtitle">Take your typing skills to the next level with our premium features and advanced training modules</p>
        </div>

        <div class="pricing-section">
            <h2 class="section-title">Premium Membership</h2>
            <div class="price-tag">10000 MMK<span class="price-period">/lifetime</span></div>
            <ul class="premium-features-list">
                <li>Official typing skill certification</li>
                <li>Downloadable certificate PDF</li>
                <li>Custom practice texts</li>
                <li>Exclusive keyboard themes</li>
                <li>Custom background images</li>
            </ul>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                    $stmt = $pdo->prepare("SELECT is_premium FROM users WHERE user_id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $user = $stmt->fetch();
                    
                    if ($user && $user['is_premium'] == 1):
                ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>You are already a premium member!
                    </div>
                <?php else: ?>
                    <a href="transaction/payment.php" class="premium-button">Get Premium Access</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="#" class="premium-button" onclick="showLoginPrompt(event)">
                    <i class="fas fa-crown me-2"></i>Get Premium Access
                </a>
            <?php endif; ?>
        </div>

        <h2 class="section-title" style="text-align: center; margin: 40px 0 20px; color: #f0b232;">Premium Features</h2>
        <div class="features-grid">
            <div class="feature-box">
                <div class="feature-icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <h3 class="feature-title">Official Certification</h3>
                <p class="feature-description">Earn verified typing certificates to showcase your skills</p>
            </div>
            <div class="feature-box">
                <div class="feature-icon">
                    <i class="fas fa-keyboard"></i>
                </div>
                <h3 class="feature-title">Custom Exercises</h3>
                <p class="feature-description">Create and practice with your own text</p>
            </div>
            <div class="feature-box">
                <div class="feature-icon">
                    <i class="fas fa-palette"></i>
                </div>
                <h3 class="feature-title">Custom Themes</h3>
                <p class="feature-description">Personalize your experience with custom keyboard themes and background images</p>
            </div>
        </div>
    </div>

    <!-- Add this modal HTML before the closing body tag -->
    <div class="modal fade" id="loginPromptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-body text-center p-4">
                    <i class="fas fa-lock text-warning mb-3" style="font-size: 3rem;"></i>
                    <h4 class="text-light mb-3">Please Log In</h4>
                    <p class="text-muted mb-4">Please log in to get premium access</p>
                    <a href="login.php" class="premium-button me-2">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showLoginPrompt(event) {
            event.preventDefault();
            new bootstrap.Modal(document.getElementById('loginPromptModal')).show();
        }

        let lastScroll = 0;
        const navbar = document.querySelector('.navbar');
        
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll <= 0) {
                navbar.classList.remove('navbar-hidden');
                return;
            }
            
            if (currentScroll > lastScroll && !navbar.classList.contains('navbar-hidden')) {
                // Scrolling down
                navbar.classList.add('navbar-hidden');
            } else if (currentScroll < lastScroll && navbar.classList.contains('navbar-hidden')) {
                // Scrolling up
                navbar.classList.remove('navbar-hidden');
            }
            
            lastScroll = currentScroll;
        });
    </script>
</body>
</html>