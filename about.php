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
            overflow: hidden;
            padding: 100px 0;
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
            margin-bottom: 2rem;
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
        <style>
                /* Add these size adjustments */
                .container {
                    max-width: 1400px;
                    padding: 0 15px;
                }
        .hero-section {
            min-height: 60vh;
            padding: 60px 0;
        }
        .values-section, .stats-section {
            padding: 50px 0;
            margin: 30px 0;
        }
        /* Keep team member styles unchanged but add margin control */
        .team-section {t
            padding: 40px 0;
        }
        .team-section .row {
            margin: 0 -10px;
        }
        .team-section .col {
            padding: 0 10px;
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
    </style>
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h1 class="hero-title">Boku no Typing</h1>
                    <p class="lead mb-4">Your ultimate destination for mastering typing skills in multiple languages. Practice with books, lyrics, coding examples, and custom texts to enhance your typing speed and accuracy.</p>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <img src="assets/images/about.jpg" alt="About Us" class="img-fluid floating-image">
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
            <div class="row justify-content-center">
                <div class="col mb-4" data-aos="fade-up">
                    <div class="team-member">
                        <img src="assets/images/team/member1.jpg" alt="Team Member" class="member-img">
                        <h3>Hein Htet Zaw</h3>
                        <p class="text-muted">Lead Developer</p>
                        <div class="social-links mt-3">
                            <a href="#" class="text-light mx-2"><i class="fab fa-github"></i></a>
                            <a href="#" class="text-light mx-2"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="team-member">
                        <img src="assets/images/team/member2.jpg" alt="Team Member" class="member-img">
                        <h3>Aung Kaung Myat</h3>
                        <p class="text-muted">Backend Developer</p>
                        <div class="social-links mt-3">
                            <a href="#" class="text-light mx-2"><i class="fab fa-github"></i></a>
                            <a href="#" class="text-light mx-2"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="team-member">
                        <img src="assets/images/team/member3.jpg" alt="Team Member" class="member-img">
                        <h3>Htet Myat Aung</h3>
                        <p class="text-muted">Frontend Developer</p>
                        <div class="social-links mt-3">
                            <a href="#" class="text-light mx-2"><i class="fab fa-github"></i></a>
                            <a href="#" class="text-light mx-2"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="team-member">
                        <img src="assets/images/team/member4.jpg" alt="Team Member" class="member-img">
                        <h3>Thant Zin Oo</h3>
                        <p class="text-muted">UI/UX Designer</p>
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