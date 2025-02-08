<?php
session_start();
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
        .premium-hero {
            text-align: center;
            padding: 120px 0 80px;
            position: relative;
            overflow: hidden;
            background: rgba(25, 25, 25, 0.7);
            border-radius: 30px;
            margin: 80px auto;
            backdrop-filter: blur(10px);
        }

        .premium-hero::before {
            content: '✨';
            position: absolute;
            font-size: 200px;
            opacity: 0.1;
            top: -50px;
            right: -50px;
            transform: rotate(15deg);
        }

        .premium-title {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 2rem;
            background: linear-gradient(45deg, #f0b232, #f7c157);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .premium-subtitle {
            font-size: 1.4rem;
            color: #adb5bd;
            max-width: 700px;
            margin: 0 auto 3rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-box {
            background: rgba(35, 35, 35, 0.95);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(240, 178, 50, 0.1);
        }

        .feature-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(240, 178, 50, 0.1);
            border-color: rgba(240, 178, 50, 0.3);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            color: #f0b232;
        }

        .feature-title {
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 1rem;
        }

        .feature-description {
            color: #adb5bd;
            line-height: 1.6;
        }

        .pricing-section {
            background: rgba(35, 35, 35, 0.95);
            border-radius: 30px;
            padding: 60px 40px;
            margin: 60px auto;
            max-width: 900px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .pricing-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #f0b232, #f7c157);
        }

        .price-tag {
            font-size: 4rem;
            font-weight: 800;
            color: #f0b232;
            margin: 2rem 0;
        }

        .price-period {
            font-size: 1.2rem;
            color: #adb5bd;
        }

        .premium-features-list {
            list-style: none;
            padding: 0;
            margin: 3rem 0;
            text-align: left;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .premium-features-list li {
            margin: 1rem 0;
            padding-left: 2rem;
            position: relative;
            color: #fff;
        }

        .premium-features-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #f0b232;
        }

        .premium-button {
            background: linear-gradient(45deg, #f0b232, #f7c157);
            color: #1a1b26;
            border: none;
            padding: 1.2rem 3rem;
            font-size: 1.2rem;
            border-radius: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-block;
            margin-top: 2rem;
        }

        .premium-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(240, 178, 50, 0.3);
        }

        .guarantee-text {
            color: #adb5bd;
            margin-top: 1.5rem;
            font-size: 0.9rem;
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
            <a href="/transaction/index.php" class="premium-button">Get Premium Access</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>