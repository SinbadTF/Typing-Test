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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            padding-top: 76px;
        }

        .team-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .team-member {
            background: rgba(15, 23, 42, 0.7);
            border-radius: 20px;
            padding: 30px;
            margin: 20px 0;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transform: translateY(50px);
            opacity: 0;
            transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            position: relative;
            overflow: hidden;
        }

        .team-member::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(240, 178, 50, 0.2),
                transparent
            );
            transition: 0.5s;
        }

        .team-member:hover::before {
            left: 100%;
        }

        .team-member.visible {
            transform: translateY(0);
            opacity: 1;
        }

        .member-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 20px;
            position: relative;
            overflow: hidden;
            border: 3px solid #f0b232;
            transform: scale(0);
            animation: imageAppear 0.5s forwards;
        }

        .member-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .team-member:hover .member-image img {
            transform: scale(1.1);
        }

        .member-info {
            text-align: center;
        }

        .member-name {
            font-size: 1.5rem;
            color: #f0b232;
            margin: 10px 0;
            opacity: 0;
            transform: translateY(20px);
            animation: textAppear 0.5s forwards 0.3s;
        }

        .member-role {
            color: #adb5bd;
            font-size: 1.1rem;
            margin-bottom: 15px;
            opacity: 0;
            transform: translateY(20px);
            animation: textAppear 0.5s forwards 0.5s;
        }

        .member-bio {
            color: #ffffff;
            line-height: 1.6;
            margin-bottom: 20px;
            opacity: 0;
            transform: translateY(20px);
            animation: textAppear 0.5s forwards 0.7s;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            opacity: 0;
            transform: translateY(20px);
            animation: textAppear 0.5s forwards 0.9s;
        }

        .social-links a {
            color: #f0b232;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            color: #ffffff;
            transform: translateY(-3px);
        }

        .omnitrix-effect {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, #f0b232, transparent 70%);
            opacity: 0;
            pointer-events: none;
        }

        @keyframes imageAppear {
            0% {
                transform: scale(0) rotate(180deg);
            }
            100% {
                transform: scale(1) rotate(0deg);
            }
        }

        @keyframes textAppear {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes omnitrixFlash {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0);
            }
            50% {
                opacity: 0.5;
                transform: translate(-50%, -50%) scale(1);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(2);
            }
        }

        .page-title {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
            padding-bottom: 20px;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, #f0b232, #f7c157);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="team-container">
        <h1 class="page-title">Meet Our Team</h1>
        
        <div class="row">
            <!-- Team Member 1 -->
            <div class="col-lg-4 col-md-6">
                <div class="team-member">
                    <div class="member-image">
                        <img src="assets/images/team/member1.jpg" alt="Team Member 1">
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">John Doe</h3>
                        <p class="member-role">Lead Developer</p>
                        <p class="member-bio">
                            Passionate about creating intuitive typing experiences and helping others improve their skills.
                        </p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-github"></i></a>
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                    <div class="omnitrix-effect"></div>
                </div>
            </div>

            <!-- Team Member 2 -->
            <div class="col-lg-4 col-md-6">
                <div class="team-member">
                    <div class="member-image">
                        <img src="assets/images/team/member2.jpg" alt="Team Member 2">
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">Jane Smith</h3>
                        <p class="member-role">UI/UX Designer</p>
                        <p class="member-bio">
                            Creative mind behind our beautiful interface and smooth user experience.
                        </p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-github"></i></a>
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                    <div class="omnitrix-effect"></div>
                </div>
            </div>

            <!-- Team Member 3 -->
            <div class="col-lg-4 col-md-6">
                <div class="team-member">
                    <div class="member-image">
                        <img src="assets/images/team/member3.jpg" alt="Team Member 3">
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">Mike Johnson</h3>
                        <p class="member-role">Content Manager</p>
                        <p class="member-bio">
                            Curates and creates engaging typing content for all skill levels.
                        </p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-github"></i></a>
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                    <div class="omnitrix-effect"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const teamMembers = document.querySelectorAll('.team-member');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        
                        // Trigger Omnitrix effect
                        const effect = entry.target.querySelector('.omnitrix-effect');
                        effect.style.animation = 'omnitrixFlash 1s ease-out';
                        
                        // Reset animation
                        effect.addEventListener('animationend', () => {
                            effect.style.animation = '';
                        });
                    }
                });
            }, {
                threshold: 0.3
            });

            teamMembers.forEach(member => {
                observer.observe(member);
            });
        });
    </script>
</body>
</html>