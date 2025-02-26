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
    <title>Story Mode - Typing Practice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .section-title {
            margin-top: -100px;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 3rem;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }
        .story-container {
            margin-top: 100px;
            padding: 30px;
        }

        .story-card {
            background: rgba(25, 25, 25, 0.95);
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 400px;
        }

        .story-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .story-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .story-content {
            padding: 20px;
            height: 250px;
            overflow-y: auto;
        }

        .story-title {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #fff;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .story-description {
            color: #adb5bd;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .story-stats {
            display: flex;
            justify-content: space-between;
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .story-difficulty {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .difficulty-easy {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .difficulty-medium {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .difficulty-hard {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        .story-content::-webkit-scrollbar {
            width: 6px;
        }

        .story-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        .story-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .story-content::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container story-container">
        <h2 class="section-title">Choose Story</h2>
        
        <div class="row">
            <!-- Story 1 -->
            <div class="col-12 mb-4">
                <div class="story-card">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="uploads/story/story1.jpg" 
                                 alt="Little Red Riding Hood" 
                                 class="story-image h-100">
                        </div>
                        <div class="col-md-8">
                            <div class="story-content">
                                <h3 class="story-title">Little Red Riding Hood</h3>
                                <p class="story-description">A classic tale about a young girl's journey through the woods and her encounter with a wolf.</p>
                                <div class="story-stats">
                                    <span><i class="fas fa-clock me-2"></i>5 min</span>
                                    <span><i class="fas fa-keyboard me-2"></i>200 words</span>
                                </div>
                                <span class="story-difficulty difficulty-easy">Easy</span>
                                <div class="text-center">
                                    <a href="story_lesson.php?story=1" class="btn btn-outline-primary btn-sm mt-3">Start Story</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Story 2 -->
            <div class="col-12 mb-4">
                <div class="story-card">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="uploads/story/peter.jpg" 
                                 alt="Three Little Pigs" 
                                 class="story-image h-100">
                        </div>
                        <div class="col-md-8">
                            <div class="story-content">
                                <h3 class="story-title">The Peter Rabbit</h3>
                                <p class="story-description">Peter Rabbit is a mischievous, adventurous, and energetic young rabbit who wears a blue jacket</p>
                                <div class="story-stats">
                                    <span><i class="fas fa-clock me-2"></i>15 min</span>
                                    <span><i class="fas fa-keyboard me-2"></i>1000 words</span>
                                </div>
                                <span class="story-difficulty difficulty-medium">Medium</span>
                                <div class="text-center">
                                    <a href="story_lesson.php?story=2" class="btn btn-outline-primary btn-sm mt-3">Start Story</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Story 3 -->
            <div class="col-12 mb-4">
                <div class="story-card">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="uploads/story/rapunzel.jpg" 
                                 alt="Snow White" 
                                 class="story-image h-100">
                        </div>
                        <div class="col-md-8">
                            <div class="story-content">
                                <h3 class="story-title">Rapunzel</h3>
                                <p class="story-description"> Rapunzel is a Disney princess with long blonde hair who first appeared in the 2010 animated film Tangled.</p>
                                <div class="story-stats">
                                    <span><i class="fas fa-clock me-2"></i>25min</span>
                                    <span><i class="fas fa-keyboard me-2"></i>1200 words</span>
                                </div>
                                <span class="story-difficulty difficulty-medium">Medium</span>
                                <div class="text-center">
                                    <a href="story_lesson.php?story=3" class="btn btn-outline-primary btn-sm mt-3">Start Story</a>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
            <!-- Story 3 -->
            <div class="col-12 mb-4">
                <div class="story-card">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="uploads/story/dragon.jpg" 
                                 alt="Snow White" 
                                 class="story-image h-100">
                        </div>
                        <div class="col-md-8">
                            <div class="story-content">
                                <h3 class="story-title">The Dragon and His Grandmother</h3>
                                <p class="story-description">It's also about a rather unusual dragon who has a human grandmother who is even trickier than he is, but who has a kinder heart.</p>
                                <div class="story-stats">
                                    <span><i class="fas fa-clock me-2"></i>25 min</span>
                                    <span><i class="fas fa-keyboard me-2"></i>1200 words</span>
                                </div>
                                <span class="story-difficulty difficulty-medium">Medium</span>
                                <div class="text-center">
                                    <a href="story_lesson.php?story=3" class="btn btn-outline-primary btn-sm mt-3">Start Story</a>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Story 3 -->
            <div class="col-12 mb-4">
                <div class="story-card">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="uploads\story\brother.jpg" 
                                 alt=" " 
                                 class="story-image h-100">
                        </div>
                        <div class="col-md-8">
                            <div class="story-content">
                                <h3 class="story-title">The Four Clever Brothers</h3>
                                <p class="story-description">The four brothers met at the four cross-roads; and having welcomed each other, set off towards their father's home, where they told him all that had happened ...</p>
                                <div class="story-stats">
                                    <span><i class="fas fa-clock me-2"></i>40 min</span>
                                    <span><i class="fas fa-keyboard me-2"></i>2000 words</span>
                                </div>
                                <span class="story-difficulty difficulty-hard">Hard</span>
                                <div class="text-center">
                                    <a href="story_lesson.php?story=3" class="btn btn-outline-primary btn-sm mt-3">Start Story</a>
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
    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 