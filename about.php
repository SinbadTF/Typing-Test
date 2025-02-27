<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Boku no Typing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #00c6ff;
            --secondary-color: #0072ff;
            --dark-bg: #111827;
            --card-bg: rgba(17, 24, 39, 0.95);
        }

        body {
            background: linear-gradient(135deg, #111827 0%, #1e293b 50%, #0f172a 100%);
            color: #ffffff;
            font-family: 'Segoe UI', sans-serif;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 30%, rgba(0, 198, 255, 0.15) 0%, transparent 70%),
                radial-gradient(circle at 80% 70%, rgba(0, 114, 255, 0.15) 0%, transparent 70%);
            pointer-events: none;
            z-index: -1;
        }

        .value-card, .team-member {
            background: rgba(17, 24, 39, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 198, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 114, 255, 0.1);
        }
        .hero-title {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .value-icon {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .member-img {
            border: 3px solid var(--primary-color);
            box-shadow: 0 0 20px rgba(78, 84, 200, 0.3);
        }
        .hero-section {
            min-height: 80vh;
            display: flex;
            align-items: center;
            position: relative;
            padding: 100px 0;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(0,123,255,0.1), rgba(0,255,136,0.1));
            z-index: -1;
        }
        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
        }
        .hero-subtitle {
            font-size: 1.8rem;
            color: #d1d0c5;
            margin-bottom: 1.5rem;
        }
        .hero-description {
            font-size: 1.1rem;
            color: #a1a1a1;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .hero-features {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
        }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #d1d0c5;
        }
        .feature-item i {
            color: var(--primary-color);
            font-size: 1.2rem;
        }
        .image-container {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            transform: perspective(1000px) rotateY(-5deg);
            transition: transform 0.5s ease;
        }
        .image-container:hover {
            transform: perspective(1000px) rotateY(0deg);
        }
        .hero-image {
            width: 100%;
            height: auto;
            border-radius: 20px;
            transition: transform 0.5s ease;
        }
        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                135deg,
                rgba(0, 198, 255, 0.1),
                rgba(0, 114, 255, 0.1)
            );
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
        }
        .floating-text {
            position: absolute;
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.2rem;
            padding: 8px 15px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            border-radius: 10px;
            animation: float 6s infinite;
        }
        .floating-text:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        .floating-text:nth-child(2) {
            top: 40%;
            right: 15%;
            animation-delay: 2s;
        }
        .floating-text:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }
        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(-3deg);
            }
            50% {
                transform: translateY(-20px) rotate(3deg);
            }
        }
        @media (max-width: 991px) {
            .hero-section {
                padding: 60px 0;
                min-height: auto;
            }
            .hero-title {
                font-size: 3rem;
            }
            .hero-features {
                flex-direction: column;
                gap: 1rem;
            }
            .image-container {
                margin-top: 3rem;
                transform: none;
            }
        }
        .timeline {
            position: relative;
            padding: 60px 0;
        }
        .timeline-item {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            position: relative;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }
        .timeline-item:hover {
            transform: translateY(-5px);
        }
        .container {
            max-width: 1400px;
            padding: 0 15px;
        }
        .values-section, .stats-section {
            padding: 50px 0;
            margin: 30px 0;
        }
        .team-section {
            padding: 80px 0;
        }
        .team-section .row {
            margin: 0 -15px;
        }
        .team-section .col-lg,
        .team-section .col-md-4 {
            padding: 0 15px;
            margin-bottom: 30px;
        }
        .team-member {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 30px 20px;
            text-align: center;
            height: 100%;
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .team-member:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .member-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 20px;
            border: 3px solid var(--primary-color);
            padding: 5px;
            transition: transform 0.3s ease;
            object-fit: cover;
        }
        .team-member:hover .member-img {
            transform: scale(1.1);
        }
        .team-member h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: #ffffff;  /* Changed from #d1d0c5 to white for better visibility */
        }
        .team-member p {
            font-size: 0.9rem;
            margin-bottom: 1rem;
            color: #a8a8a8;  /* Changed from #646669 to lighter gray */
        }
        .social-links {
            margin-top: auto;
        }
        .social-links a {
            transition: color 0.3s ease;
            font-size: 1.2rem;
        }
        .social-links a:hover {
            color: var(--primary-color) !important;
        }
        @media (max-width: 991px) {
            .team-section .row {
                justify-content: center;
            }
            
            .team-section .col-md-4 {
                width: 33.333%;
            }
        }
        @media (max-width: 768px) {
            .team-section .col-md-4 {
                width: 50%;
            }
        }
        @media (max-width: 576px) {
            .team-section .col-md-4 {
                width: 100%;
            }
        }
    </style>
    <style>
        .team-member {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            margin: 20px 0;
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s ease;
        }
    </style>
    <style>
        .team-member:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
    </style>
    <style>
        .member-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 20px;
            border: 3px solid var(--primary-color);
            padding: 5px;
            transition: transform 0.3s ease;
        }
    </style>
    <style>
        .team-member:hover .member-img {
            transform: scale(1.1);
        }
    </style>
    <style>
        .stats-section {
            background: linear-gradient(45deg, rgba(0,123,255,0.1), rgba(0,255,136,0.1));
            padding: 100px 0;
            margin: 80px 0;
        }
    </style>
    <style>
        .stat-card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.1);
        }
    </style>
    <style>
        .stat-number {
            font-size: 3.5rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
    <style>
        .values-section {
            padding: 100px 0;
        }
    </style>
    <style>
        .value-card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 30px;
            height: 100%;
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s ease;
        }
    </style>
    <style>
        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
    </style>
    <style>
        .value-icon {
            font-size: 2.5rem;
            margin-bottom: 20px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
    <style>
        .floating-image {
            animation: float 6s ease-in-out infinite;
        }
        .team-member .text-muted {
    color: #d1d0c5 !important;  /* Override Bootstrap's text-muted with a lighter color */
}
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h1 class="hero-title">Boku no Typing</h1>
                    <p class="hero-subtitle mb-4">Your Ultimate Typing Practice Platform</p>
                    <p class="hero-description">Master typing skills in multiple languages. Practice with books, lyrics, coding examples, and custom texts to enhance your typing speed and accuracy.</p>
                    <div class="hero-features">
                        <div class="feature-item">
                            <i class="fas fa-keyboard"></i>
                            <span>Multi-Language Support</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-chart-line"></i>
                            <span>Real-time Statistics</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-trophy"></i>
                            <span>Achievement System</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="image-container">
                        <img src="assets/images/about.png" alt="About Us" class="hero-image floating-image">
                        <div class="image-overlay"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="values-section">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Key Features</h2>
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="value-card">
                        <i class="fas fa-language value-icon"></i>
                        <h3>Multi-Language Support</h3>
                        <p>English Typing Practice</p>
                        <p>Myanmar Language Support</p>
                        <p>Japanese Text Practice</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="value-card">
                        <i class="fas fa-graduation-cap value-icon"></i>
                        <h3>Learning Resources</h3>
                        <p>Classic Literature Excerpts</p>
                        <p>Popular Song Lyrics</p>
                        <p>Programming Code Samples</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="value-card">
                        <i class="fas fa-certificate value-icon"></i>
                        <h3>Professional Growth</h3>
                        <p>Typing Certification</p>
                        <p>Real-time Statistics</p>
                        <p>Progress Tracking</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="team-section">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Development Team</h2>
            <div class="row g-4 justify-content-center align-items-stretch">
                <div class="col-lg col-md-4" data-aos="fade-up">
                    <div class="team-member">
                        <img src="assets/images/team/member1.png" alt="Team Member" class="member-img">
                        <h3>Hein Htet Zaw</h3>
                       
                        <div class="social-links mt-3">
                            <a href="https://github.com/SinbadTF" class="text-light mx-2"><i class="fab fa-github"></i></a>
                            <a href="#" class="text-light mx-2"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="team-member">
                        <img src="assets/images/team/member2.png" alt="Team Member" class="member-img">
                        <h3>Htet Yupar Tun</h3>
                        
                        <div class="social-links mt-3">
                            <a href="#" class="text-light mx-2"><i class="fab fa-github"></i></a>
                            <a href="#" class="text-light mx-2"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="team-member">
                        <img src="assets/images/team/member3.png" alt="Team Member" class="member-img">
                        <h3>Chaw Su Thwe</h3>
                        
                        <div class="social-links mt-3">
                            <a href="#" class="text-light mx-2"><i class="fab fa-github"></i></a>
                            <a href="#" class="text-light mx-2"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="team-member">
                        <img src="assets/images/team/member4.png" alt="Team Member" class="member-img">
                        <h3>Yoon Wati Khin</h3>
                        
                        <div class="social-links mt-3">
                            <a href="#" class="text-light mx-2"><i class="fab fa-github"></i></a>
                            <a href="#" class="text-light mx-2"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="team-member">
                        <img src="assets/images/team/member5.png" alt="Team Member" class="member-img">
                        <h3>Htet Mon Myint</h3>
                       
                        <div class="social-links mt-3">
                            <a href="#" class="text-light mx-2"><i class="fab fa-github"></i></a>
                            <a href="#" class="text-light mx-2"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });

        // Animate statistics numbers
        const stats = document.querySelectorAll('.stat-number');
        stats.forEach(stat => {
            const target = parseInt(stat.getAttribute('data-count'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const updateNumber = () => {
                current += step;
                if (current < target) {
                    stat.textContent = Math.floor(current);
                    requestAnimationFrame(updateNumber);
                } else {
                    stat.textContent = target;
                }
            };

            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting) {
                    updateNumber();
                    observer.unobserve(stat);
                }
            });

            observer.observe(stat);
        });
    </script>
</body>
</html>