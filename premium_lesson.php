<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include database connection first
require_once 'config/database.php';

// Then check premium status
$stmt = $pdo->prepare("SELECT is_premium FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['is_premium'] != 1) {
    header('Location: premium.php');
    exit();
}

// Get parameters from URL
$category = $_GET['category'] ?? '';
$lessonNumber = $_GET['lesson'] ?? '';
$lang = $_GET['lang'] ?? 'en';

// Fetch lesson content based on category
if ($category === 'books') {
    
    $stmt = $pdo->prepare("SELECT * FROM premium_books WHERE language = ? AND lesson_number = ?");
    $stmt->execute([$lang, $lessonNumber]);
    $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
    $content = $lesson['content'] ?? '';
    $title = $lesson['title'] ?? '';
} elseif ($category === 'lyrics') {
    // Get song content from the songs array
    $songLessons = [
        1 => [
            'title' => 'Perfect - Ed Sheeran',
            'content' => "I found a love for me\nDarling, just dive right in and follow my lead\nWell, I found a girl, beautiful and sweet\nOh, I never knew you were the someone waiting for me",
            'difficulty' => 'Easy'
        ],
        // Add more songs here
    ];
    $content = $songLessons[$lessonNumber]['content'] ?? '';
    $title = $songLessons[$lessonNumber]['title'] ?? '';
}

// Add language-specific titles
$languageTitles = [
    'en' => 'Premium Typing Practice',
    'my' => 'Premium စာရိုက်လေ့ကျင့်ခန်း',
    'jp' => 'プレミアムタイピング練習'
];

$pageTitle = $languageTitles[$lang] ?? $languageTitles['en'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Boku no Typing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #323437;
            color: #d1d0c5;
            font-family: 'Roboto Mono', monospace;
            min-height: 100vh;
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            transition: background 0.3s ease;
        }

        .typing-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
        }

        .lesson-title {
            color: #e2b714;
            font-size: 1.8rem;
            margin-bottom: 30px;
            text-align: center;
        }

        .typing-text {
            background: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 1.2rem;
            line-height: 1.6;
        }

        .stats-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
            background: rgba(0, 0, 0, 0.2);
            padding: 15px;
            border-radius: 10px;
        }

        .stat-item {
            font-size: 1.2rem;
        }

        .stat-value {
            color: #e2b714;
            font-weight: 600;
        }

        #input-field {
            width: 100%;
            background: rgba(0, 0, 0, 0.2);
            border: none;
            padding: 15px;
            color: #d1d0c5;
            font-family: 'Roboto Mono', monospace;
            font-size: 1.2rem;
            border-radius: 10px;
            margin-top: 20px;
        }

        #input-field:focus {
            outline: none;
            box-shadow: 0 0 0 2px #e2b714;
        }

        .correct {
            color: #98c379;
        }

        .incorrect {
            color: #e06c75;
            text-decoration: underline;
        }

        .current {
            background-color: #e2b714;
            color: #323437;
        }

        .keyboard {
            margin: 30px auto;
            max-width: 850px;
            background: rgba(25, 25, 25, 0.95);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .keyboard-row {
            display: flex;
            justify-content: center;
            margin-bottom: 7px;
            gap: 5px;
        }

        .key {
            width: 44px;
            height: 44px;
            background: #3c3c3c;
            border: none;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.1s ease;
            position: relative;
            box-shadow: 0 2px 0 #262626;
        }

        .key:active, .key.active {
            transform: translateY(2px);
            box-shadow: 0 0 0 #262626;
            background: #4a9eff;
            color: #ffffff;
        }

        .key.tab { width: 75px; }
        .key.caps { width: 85px; }
        .key.enter { width: 90px; }
        .key.shift { width: 105px; }
        .key.ctrl, .key.win, .key.alt { width: 60px; }
        .key.menu { width: 60px; }
        .key.space { width: 320px; }
        .key.delete { width: 85px; }

        .key.special {
            color: #a0a0a0;
            font-size: 0.75rem;
        }

        .key.tab, .key.caps, .key.shift, .key.ctrl, 
        .key.win, .key.alt, .key.menu, .key.enter,
        .key.delete {
            font-size: 0.7rem;
            text-transform: uppercase;
            background: #333333;
            color: #a0a0a0;
        }

        .theme-selector-practice {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .theme-button {
            margin-top: 50px;
            background: #2c2e31;
            color: #d1d0c5;
            border: 2px solid #4a4a4a;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .theme-button:hover {
            background: #3c3c3c;
            border-color: #5c5c5c;
            transform: translateY(-1px);
        }

        .theme-button i {
            font-size: 1rem;
            color: #e2b714;
        }

        .theme-options {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background: #2c2e31;
            border: 2px solid #4a4a4a;
            border-radius: 8px;
            padding: 8px;
            display: none;
            min-width: 150px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .theme-options.show {
            display: block;
            animation: fadeIn 0.2s ease;
        }

        .theme-option {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 8px 16px;
            background: none;
            border: none;
            color: #d1d0c5;
            cursor: pointer;
            text-align: left;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .theme-option:hover {
            background: #3c3c3c;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Theme styles */
        body.theme-light {
            background-color: #e0e0e0;
        }
        body.theme-midnight {
            background-color: #1a1b26;
        }
        body.theme-forest {
            background-color: #2b2f2b;
        }
        body.theme-sunset {
            background-color: #2d1b2d;
        }

        /* Keyboard theme styles */
        .keyboard.theme-light {
            background: #f0f0f0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .keyboard.theme-light .key {
            background: #ffffff;
            color: #2c2c2c;
            box-shadow: 0 2px 0 #d0d0d0;
            border: 1px solid #e0e0e0;
        }
        .keyboard.theme-light .key:active, 
        .keyboard.theme-light .key.active {
            background: #4a9eff;
            color: #ffffff;
            box-shadow: 0 0 0 #c0c0c0;
            border-color: #4a9eff;
        }
        .keyboard.theme-light .key.special,
        .keyboard.theme-light .key.tab, 
        .keyboard.theme-light .key.caps, 
        .keyboard.theme-light .key.shift, 
        .keyboard.theme-light .key.ctrl, 
        .keyboard.theme-light .key.win, 
        .keyboard.theme-light .key.alt, 
        .keyboard.theme-light .key.menu, 
        .keyboard.theme-light .key.enter,
        .keyboard.theme-light .key.delete {
            background: #e8e8e8;
            color: #404040;
            border: 1px solid #d8d8d8;
        }

        .keyboard.theme-midnight {
            background: #1a1b26;
        }
        .keyboard.theme-midnight .key {
            background: #24283b;
            color: #7aa2f7;
            box-shadow: 0 2px 0 #16161e;
        }

        .keyboard.theme-forest {
            background: #2b2f2b;
        }
        .keyboard.theme-forest .key {
            background: #1e231f;
            color: #95c085;
            box-shadow: 0 2px 0 #161916;
        }

        .keyboard.theme-sunset {
            background: #2d1b2d;
        }
        .keyboard.theme-sunset .key {
            background: #1f1520;
            color: #f67e7d;
            box-shadow: 0 2px 0 #170d17;
        }

        /* Also update the light theme text colors */
        body.theme-light {
            background-color: #e0e0e0;
            color: #2c2c2c;
        }

        body.theme-light .typing-text {
            background: rgba(255, 255, 255, 0.9);
            color: #2c2c2c;
        }

        body.theme-light .stat-item {
            color: #404040;
        }

        body.theme-light .stat-value {
            color: #2c2c2c;
        }

        /* Add these new theme styles after the existing theme styles */

        /* Neon Theme */
        body.theme-neon {
            background-color: #0f0f1a;
            color: #00ff95;
        }

        .keyboard.theme-neon {
            background: rgba(15, 15, 26, 0.95);
            box-shadow: 0 0 20px rgba(0, 255, 149, 0.2);
            border: 1px solid rgba(0, 255, 149, 0.1);
        }

        .keyboard.theme-neon .key {
            background: rgba(0, 255, 149, 0.1);
            color: #00ff95;
            border: 1px solid rgba(0, 255, 149, 0.3);
            box-shadow: 0 2px 0 rgba(0, 255, 149, 0.2);
        }

        .keyboard.theme-neon .key:active,
        .keyboard.theme-neon .key.active {
            background: #00ff95;
            color: #0f0f1a;
            box-shadow: 0 0 15px rgba(0, 255, 149, 0.5);
        }

        /* Ocean Theme */
        body.theme-ocean {
            background-color: #0a192f;
            color: #64ffda;
        }

        .keyboard.theme-ocean {
            background: rgba(10, 25, 47, 0.95);
            box-shadow: 0 4px 20px rgba(100, 255, 218, 0.1);
        }

        .keyboard.theme-ocean .key {
            background: #112240;
            color: #64ffda;
            border: 1px solid rgba(100, 255, 218, 0.2);
            box-shadow: 0 2px 0 #0a192f;
        }

        .keyboard.theme-ocean .key:active,
        .keyboard.theme-ocean .key.active {
            background: #64ffda;
            color: #0a192f;
            box-shadow: 0 0 10px rgba(100, 255, 218, 0.3);
        }

        /* Retro Theme */
        body.theme-retro {
            background-color: #2d2b55;
            color: #ff7edb;
        }

        .keyboard.theme-retro {
            background: rgba(45, 43, 85, 0.95);
            box-shadow: 0 4px 20px rgba(255, 126, 219, 0.2);
        }

        .keyboard.theme-retro .key {
            background: #1f1f41;
            color: #ff7edb;
            border: 1px solid rgba(255, 126, 219, 0.2);
            box-shadow: 0 2px 0 #1a1a38;
        }

        .keyboard.theme-retro .key:active,
        .keyboard.theme-retro .key.active {
            background: #ff7edb;
            color: #1f1f41;
            box-shadow: 0 0 10px rgba(255, 126, 219, 0.4);
        }

        /* Matrix Theme */
        body.theme-matrix {
            background-color: #000000;
            color: #00ff00;
        }

        .keyboard.theme-matrix {
            background: rgba(0, 0, 0, 0.95);
            box-shadow: 0 4px 20px rgba(0, 255, 0, 0.2);
            border: 1px solid rgba(0, 255, 0, 0.2);
        }

        .keyboard.theme-matrix .key {
            background: #001100;
            color: #00ff00;
            border: 1px solid rgba(0, 255, 0, 0.3);
            box-shadow: 0 2px 0 #000800;
            text-shadow: 0 0 5px #00ff00;
        }

        .keyboard.theme-matrix .key:active,
        .keyboard.theme-matrix .key.active {
            background: #00ff00;
            color: #000000;
            box-shadow: 0 0 10px #00ff00;
        }

        /* Add these navbar theme styles after the existing theme styles */

        /* Light Theme Navbar */
        .navbar.theme-light {
            background-color: #f0f0f0 !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar.theme-light .nav-link,
        .navbar.theme-light .navbar-brand {
            color: #2c2c2c !important;
        }
        .navbar.theme-light .nav-link:hover {
            color: #4a9eff !important;
        }

        /* Neon Theme Navbar */
        .navbar.theme-neon {
            background-color: rgba(15, 15, 26, 0.95) !important;
            box-shadow: 0 0 20px rgba(0, 255, 149, 0.2);
            border-bottom: 1px solid rgba(0, 255, 149, 0.1);
        }
        .navbar.theme-neon .nav-link,
        .navbar.theme-neon .navbar-brand {
            color: #00ff95 !important;
            text-shadow: 0 0 10px rgba(0, 255, 149, 0.5);
        }
        .navbar.theme-neon .nav-link:hover {
            color: #fff !important;
            text-shadow: 0 0 15px rgba(0, 255, 149, 0.8);
        }

        /* Ocean Theme Navbar */
        .navbar.theme-ocean {
            background-color: rgba(10, 25, 47, 0.95) !important;
            box-shadow: 0 2px 20px rgba(100, 255, 218, 0.1);
        }
        .navbar.theme-ocean .nav-link,
        .navbar.theme-ocean .navbar-brand {
            color: #64ffda !important;
        }
        .navbar.theme-ocean .nav-link:hover {
            color: #fff !important;
            text-shadow: 0 0 10px rgba(100, 255, 218, 0.5);
        }

        /* Retro Theme Navbar */
        .navbar.theme-retro {
            background-color: rgba(45, 43, 85, 0.95) !important;
            box-shadow: 0 2px 20px rgba(255, 126, 219, 0.2);
        }
        .navbar.theme-retro .nav-link,
        .navbar.theme-retro .navbar-brand {
            color: #ff7edb !important;
        }
        .navbar.theme-retro .nav-link:hover {
            color: #fff !important;
            text-shadow: 0 0 10px rgba(255, 126, 219, 0.5);
        }

        /* Matrix Theme Navbar */
        .navbar.theme-matrix {
            background-color: rgba(0, 0, 0, 0.95) !important;
            box-shadow: 0 2px 20px rgba(0, 255, 0, 0.2);
            border-bottom: 1px solid rgba(0, 255, 0, 0.2);
        }
        .navbar.theme-matrix .nav-link,
        .navbar.theme-matrix .navbar-brand {
            color: #00ff00 !important;
            text-shadow: 0 0 5px #00ff00;
        }
        .navbar.theme-matrix .nav-link:hover {
            color: #fff !important;
            text-shadow: 0 0 10px #00ff00;
        }

        /* Update existing theme navbars */
        .navbar.theme-midnight {
            background-color: rgba(26, 27, 38, 0.95) !important;
        }
        .navbar.theme-midnight .nav-link,
        .navbar.theme-midnight .navbar-brand {
            color: #7aa2f7 !important;
        }
        .navbar.theme-midnight .nav-link:hover {
            color: #fff !important;
        }

        .navbar.theme-forest {
            background-color: rgba(43, 47, 43, 0.95) !important;
        }
        .navbar.theme-forest .nav-link,
        .navbar.theme-forest .navbar-brand {
            color: #95c085 !important;
        }
        .navbar.theme-forest .nav-link:hover {
            color: #fff !important;
        }

        .navbar.theme-sunset {
            background-color: rgba(45, 27, 45, 0.95) !important;
        }
        .navbar.theme-sunset .nav-link,
        .navbar.theme-sunset .navbar-brand {
            color: #f67e7d !important;
        }
        .navbar.theme-sunset .nav-link:hover {
            color: #fff !important;
        }

        /* Add a new space theme */
        body.theme-space {
            background: url('assets/images/themes/space.jpg');
            background-color: #000000; /* Fallback */
            color: #ffffff;
        }
        body.theme-galaxylight {
            background: url('assets/images/themes/galaxy1.jpg');
            background-color: #000000; /* Fallback */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #ffffff;
        }
        
        body.theme-larvender {
            background: url('assets/images/themes/lar2.jpg');
            background-color: #000000; /* Fallback */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #ffffff;
        }
        body.theme-sweety {
            background: url('assets/images/themes/Sakura.jpg');
            
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #333333;
        }
        
        body.theme-kid {
            background: url('assets/images/themes/kid.jpg');
            background-color: #FFE5F1; /* Light pink fallback color */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #333333;
            min-height: 100vh;
            padding-bottom: 50px;
        }

        /* Kid theme keyboard - Baby blue colors */
        .keyboard.theme-kid {
            background: linear-gradient(135deg, rgba(176, 226, 255, 0.9), rgba(135, 206, 250, 0.9));
            backdrop-filter: blur(8px);
            border: 2px solid rgba(135, 206, 250, 0.4);
            box-shadow: 0 4px 20px rgba(100, 181, 246, 0.3);
            border-radius: 15px;
        }

        .keyboard.theme-kid .key {
            background: linear-gradient(135deg, rgba(240, 248, 255, 0.95), rgba(230, 240, 255, 0.95));
            color: #4a90e2; /* Sky blue */
            border: 1px solid rgba(135, 206, 250, 0.4);
            box-shadow: 0 3px 0 rgba(135, 206, 250, 0.3);
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .keyboard.theme-kid .key.special {
            background: linear-gradient(135deg, rgba(230, 240, 255, 0.95), rgba(220, 235, 255, 0.95));
            color: #1e88e5; /* Slightly darker blue */
            font-weight: 500;
        }

        .keyboard.theme-kid .key:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 0 rgba(135, 206, 250, 0.3);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(240, 248, 255, 0.98));
        }

        .keyboard.theme-kid .key:active,
        .keyboard.theme-kid .key.active {
            transform: translateY(2px);
            background: linear-gradient(135deg, #64b5f6, #90caf9); /* Light blue gradient */
            color: white;
            box-shadow: none;
            text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        }

        /* Special keys for kid theme */
        .keyboard.theme-kid .key.space {
            background: linear-gradient(135deg, rgba(240, 248, 255, 0.95), rgba(230, 240, 255, 0.95));
            border-radius: 12px;
        }

        .keyboard.theme-kid .key.enter,
        .keyboard.theme-kid .key.shift,
        .keyboard.theme-kid .key.caps,
        .keyboard.theme-kid .key.tab {
            background: linear-gradient(135deg, rgba(230, 240, 255, 0.95), rgba(220, 235, 255, 0.95));
            color: #2196f3; /* Bright blue */
            font-weight: 500;
        }

        /* Lavender theme keyboard - Updated design */
        .keyboard.theme-larvender {
            background: linear-gradient(135deg, rgba(230, 230, 255, 0.9), rgba(220, 220, 255, 0.9));
            backdrop-filter: blur(10px);
            border: 2px solid rgba(147, 112, 219, 0.4);
            box-shadow: 0 4px 25px rgba(147, 112, 219, 0.3);
            border-radius: 15px;
            padding: 20px;
        }

        .keyboard.theme-larvender .key {
            background: linear-gradient(135deg, rgba(250, 250, 255, 0.95), rgba(240, 240, 255, 0.95));
            color: #8e44ad; /* Rich purple */
            border: 1px solid rgba(147, 112, 219, 0.4);
            box-shadow: 0 3px 0 rgba(147, 112, 219, 0.3);
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .keyboard.theme-larvender .key.special {
            background: linear-gradient(135deg, rgba(240, 240, 255, 0.95), rgba(230, 230, 255, 0.95));
            color: #9b59b6; /* Softer purple */
            font-weight: 500;
        }

        .keyboard.theme-larvender .key:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 0 rgba(147, 112, 219, 0.3);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(245, 245, 255, 0.98));
        }

        .keyboard.theme-larvender .key:active,
        .keyboard.theme-larvender .key.active {
            transform: translateY(2px);
            background: linear-gradient(135deg, #a569bd, #9b59b6);
            color: white;
            box-shadow: none;
            text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        }

        /* Special keys for lavender theme */
        .keyboard.theme-larvender .key.space {
            background: linear-gradient(135deg, rgba(245, 245, 255, 0.95), rgba(235, 235, 255, 0.95));
            border-radius: 12px;
        }

        .keyboard.theme-larvender .key.enter,
        .keyboard.theme-larvender .key.shift,
        .keyboard.theme-larvender .key.caps,
        .keyboard.theme-larvender .key.tab {
            background: linear-gradient(135deg, rgba(235, 235, 255, 0.95), rgba(225, 225, 255, 0.95));
            color: #8e44ad;
            font-weight: 500;
        }

        /* Active key states for all themes */
        .keyboard.theme-space .key:active,
        .keyboard.theme-space .key.active,
        .keyboard.theme-galaxylight .key:active,
        .keyboard.theme-galaxylight .key.active,
        .keyboard.theme-larvender .key:active,
        .keyboard.theme-larvender .key.active,
        .keyboard.theme-kid .key:active,
        .keyboard.theme-kid .key.active {
            transform: translateY(2px);
            background: linear-gradient(135deg, #ff69b4, #ff8da1);
            color: #ffffff;
            box-shadow: none;
            text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        }

        /* Navbar text colors for each theme */
        .navbar.theme-space .nav-link,
        .navbar.theme-space .navbar-brand {
            color: #64ffda !important;
        }

        .navbar.theme-galaxylight .nav-link,
        .navbar.theme-galaxylight .navbar-brand {
            color: #ff4d82 !important;
            text-shadow: 0 0 10px rgba(255, 166, 193, 0.3);
        }

        .navbar.theme-larvender .nav-link,
        .navbar.theme-larvender .navbar-brand {
            color: #663399 !important;
        }

        .navbar.theme-kid .nav-link,
        .navbar.theme-kid .navbar-brand {
            color: #ff1493 !important;
            text-shadow: 0 0 10px rgba(255, 166, 193, 0.4);
        }

        /* Sakura theme text visibility fixes */
        body.theme-sweety {
            color: #333333;
        }

        body.theme-sweety .typing-text {
            background: linear-gradient(135deg, rgba(255, 219, 230, 0.7), rgba(255, 209, 220, 0.7));
            backdrop-filter: blur(15px);
            border: 2px solid rgba(255, 192, 203, 0.3);
            box-shadow: 
                0 4px 20px rgba(255, 182, 193, 0.15),
                inset 0 0 30px rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 25px;
        }

        body.theme-sweety .lesson-title {
            color: #ff4d82;
            text-shadow: 2px 2px 4px rgba(255, 166, 193, 0.4);
            font-size: 2rem;
            font-weight: 600;
        }

        body.theme-sweety .stats-container {
            background: linear-gradient(135deg, rgba(255, 219, 230, 0.7), rgba(255, 209, 220, 0.7));
            backdrop-filter: blur(15px);
            border: 2px solid rgba(255, 192, 203, 0.3);
            box-shadow: 
                0 4px 15px rgba(255, 182, 193, 0.15),
                inset 0 0 30px rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 20px;
        }

        body.theme-sweety #input-field {
            background: linear-gradient(135deg, rgba(255, 219, 230, 0.7), rgba(255, 209, 220, 0.7));
            color: #333333;
            border: 2px solid rgba(255, 192, 203, 0.3);
            box-shadow: 
                0 4px 15px rgba(255, 182, 193, 0.15),
                inset 0 0 30px rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 15px 20px;
            font-size: 1.2rem;
        }

        body.theme-sweety #input-field:focus {
            box-shadow: 
                0 0 0 2px #ff4d82,
                inset 0 0 30px rgba(255, 255, 255, 0.3);
            border-color: #ff4d82;
        }

        /* Text highlighting colors */
        body.theme-sweety .correct {
            color: #2ecc71;
        }

        body.theme-sweety .incorrect {
            color: #e74c3c;
        }

        body.theme-sweety .current {
            background-color: #ff4d82;
            color: #ffffff;
        }

        /* Sakura theme keyboard - Baby pink colors */
        .keyboard.theme-sweety {
            background: linear-gradient(135deg, rgba(255, 219, 230, 0.9), rgba(255, 209, 220, 0.9));
            backdrop-filter: blur(8px);
            border: 2px solid rgba(255, 192, 203, 0.4);
            box-shadow: 0 4px 20px rgba(255, 182, 193, 0.3);
            border-radius: 15px;
        }

        .keyboard.theme-sweety .key {
            background: linear-gradient(135deg, rgba(255, 240, 245, 0.95), rgba(255, 228, 235, 0.95));
            color: #ff8da1; /* Soft pink */
            border: 1px solid rgba(255, 192, 203, 0.4);
            box-shadow: 0 3px 0 rgba(255, 182, 193, 0.3);
            transition: all 0.2s ease;
        }

        .keyboard.theme-sweety .key.special {
            background: linear-gradient(135deg, rgba(255, 228, 235, 0.95), rgba(255, 218, 225, 0.95));
            color: #ff7a8c; /* Slightly darker pink */
        }

        .keyboard.theme-sweety .key:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 0 rgba(255, 182, 193, 0.3);
        }

        .keyboard.theme-sweety .key:active,
        .keyboard.theme-sweety .key.active {
            transform: translateY(2px);
            background: linear-gradient(135deg, #ffb6c1, #ffc0cb); /* Light pink to pink */
            color: white;
            box-shadow: none;
            text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        }

        /* Happy Kid theme text visibility fixes */
        body.theme-kid {
            color: #333333;
        }

        body.theme-kid .typing-text {
            background: linear-gradient(135deg, rgba(176, 226, 255, 0.7), rgba(135, 206, 250, 0.7));
            backdrop-filter: blur(15px);
            border: 2px solid rgba(135, 206, 250, 0.3);
            box-shadow: 
                0 4px 20px rgba(100, 181, 246, 0.15),
                inset 0 0 30px rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 25px;
        }

        body.theme-kid .lesson-title {
            color: #1e88e5;
            text-shadow: 2px 2px 4px rgba(135, 206, 250, 0.4);
            font-size: 2rem;
            font-weight: 600;
        }

        body.theme-kid .stats-container {
            background: linear-gradient(135deg, rgba(176, 226, 255, 0.7), rgba(135, 206, 250, 0.7));
            backdrop-filter: blur(15px);
            border: 2px solid rgba(135, 206, 250, 0.3);
            box-shadow: 
                0 4px 15px rgba(100, 181, 246, 0.15),
                inset 0 0 30px rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 20px;
        }

        body.theme-kid .stat-item {
            color: #333333;
            font-weight: 500;
            font-size: 1.1rem;
        }

        body.theme-kid .stat-value {
            color: #1e88e5;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(135, 206, 250, 0.3);
        }

        body.theme-kid #input-field {
            background: linear-gradient(135deg, rgba(176, 226, 255, 0.7), rgba(135, 206, 250, 0.7));
            color: #333333;
            border: 2px solid rgba(135, 206, 250, 0.3);
            box-shadow: 
                0 4px 15px rgba(100, 181, 246, 0.15),
                inset 0 0 30px rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 15px 20px;
            font-size: 1.2rem;
        }

        body.theme-kid #input-field:focus {
            box-shadow: 
                0 0 0 2px #4a90e2,
                inset 0 0 30px rgba(255, 255, 255, 0.3);
            border-color: #4a90e2;
        }

        /* Text highlighting colors */
        body.theme-kid .correct {
            color: #2ecc71;
            text-shadow: 0 0 1px rgba(46, 204, 113, 0.3);
        }

        body.theme-kid .incorrect {
            color: #e74c3c;
            text-shadow: 0 0 1px rgba(231, 76, 60, 0.3);
        }

        body.theme-kid .current {
            background-color: #4a90e2;
            color: #ffffff;
            border-radius: 3px;
            padding: 0 2px;
        }

        /* Sakura theme navbar text styling */
        .navbar.theme-sweety .nav-link,
        .navbar.theme-sweety .navbar-brand {
            color: #4a90e2 !important; /* Baby blue color */
            text-shadow: 0 0 10px rgba(135, 206, 250, 0.4);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .navbar.theme-sweety .nav-link:hover {
            color: #1e88e5 !important; /* Slightly darker blue on hover */
            transform: translateY(-1px);
        }

        /* Remove any background containers */
        .navbar.theme-sweety .navbar-collapse,
        .navbar.theme-sweety .container-fluid {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        .navbar.theme-sweety .navbar-toggler {
            border: none;
            padding: 8px 12px;
        }

        /* Galaxy Night theme keyboard - Updated design */
        .keyboard.theme-space {
            background: linear-gradient(135deg, rgba(13, 15, 30, 0.9), rgba(20, 20, 40, 0.9));
            backdrop-filter: blur(10px);
            border: 2px solid rgba(100, 149, 237, 0.3);
            box-shadow: 
                0 4px 25px rgba(100, 149, 237, 0.2),
                inset 0 0 30px rgba(100, 149, 237, 0.1);
            border-radius: 15px;
            padding: 20px;
        }

        .keyboard.theme-space .key {
            background: linear-gradient(135deg, rgba(30, 35, 60, 0.95), rgba(25, 30, 50, 0.95));
            color: #64ffda;
            border: 1px solid rgba(100, 149, 237, 0.3);
            box-shadow: 0 3px 0 rgba(100, 149, 237, 0.2);
            border-radius: 8px;
            transition: all 0.2s ease;
            text-shadow: 0 0 5px rgba(100, 255, 218, 0.5);
        }

        .keyboard.theme-space .key.special {
            background: linear-gradient(135deg, rgba(25, 30, 50, 0.95), rgba(20, 25, 45, 0.95));
            color: #7f7fd5;
            font-weight: 500;
        }

        .keyboard.theme-space .key:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 4px 0 rgba(100, 149, 237, 0.2),
                0 0 10px rgba(100, 255, 218, 0.3);
            background: linear-gradient(135deg, rgba(35, 40, 65, 0.98), rgba(30, 35, 55, 0.98));
        }

        .keyboard.theme-space .key:active,
        .keyboard.theme-space .key.active {
            transform: translateY(2px);
            background: linear-gradient(135deg, #64ffda, #91fff1);
            color: #1a1b26;
            box-shadow: none;
            text-shadow: none;
        }

        /* Special keys for space theme */
        .keyboard.theme-space .key.space {
            background: linear-gradient(135deg, rgba(35, 40, 65, 0.95), rgba(30, 35, 55, 0.95));
            border-radius: 12px;
        }

        .keyboard.theme-space .key.enter,
        .keyboard.theme-space .key.shift,
        .keyboard.theme-space .key.caps,
        .keyboard.theme-space .key.tab {
            background: linear-gradient(135deg, rgba(30, 35, 55, 0.95), rgba(25, 30, 50, 0.95));
            color: #91fff1;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="theme-selector-practice">
        <button class="theme-button" id="theme-toggle">
            <i class="fas fa-palette"></i>
            Theme
        </button>
        <div class="theme-options" id="theme-options">
            <button class="theme-option" data-theme="dark">Dark</button>
            <button class="theme-option" data-theme="retro">Retro</button>
            <button class="theme-option" data-theme="matrix">Matrix</button>
            <button class="theme-option" data-theme="space">Galaxy Night</button>
            <button class="theme-option" data-theme="galaxylight">Galaxy Light</button>
            <button class="theme-option" data-theme="larvender">Larvender</button>
            <button class="theme-option" data-theme="sweety">Sakura</button>
            <button class="theme-option" data-theme="kid">Happy kid</button>
        </div>
    </div>

    <div class="typing-container">
        <h2 class="lesson-title"><?php echo htmlspecialchars($title); ?></h2>
        
        <div class="stats-container">
            <div class="stat-item">WPM: <span class="stat-value" id="wpm">0</span></div>
            <div class="stat-item">Accuracy: <span class="stat-value" id="accuracy">100%</span></div>
            <div class="stat-item">Time: <span class="stat-value" id="timer">0:00</span></div>
        </div>

        <div class="typing-text">
            <div id="text-display"><?php echo htmlspecialchars($content); ?></div>
        </div>

        <input type="text" id="input-field" placeholder="Start typing..." autocomplete="off">

        <div class="keyboard">
            <div class="keyboard-row">
                <div class="key special" data-key="`">`</div>
                <div class="key" data-key="1">1</div>
                <div class="key" data-key="2">2</div>
                <div class="key" data-key="3">3</div>
                <div class="key" data-key="4">4</div>
                <div class="key" data-key="5">5</div>
                <div class="key" data-key="6">6</div>
                <div class="key" data-key="7">7</div>
                <div class="key" data-key="8">8</div>
                <div class="key" data-key="9">9</div>
                <div class="key" data-key="0">0</div>
                <div class="key special" data-key="-">-</div>
                <div class="key special" data-key="=">=</div>
                <div class="key delete special" data-key="Backspace">delete</div>
            </div>
            <div class="keyboard-row">
                <div class="key tab special" data-key="Tab">tab</div>
                <div class="key" data-key="q">q</div>
                <div class="key" data-key="w">w</div>
                <div class="key" data-key="e">e</div>
                <div class="key" data-key="r">r</div>
                <div class="key" data-key="t">t</div>
                <div class="key" data-key="y">y</div>
                <div class="key" data-key="u">u</div>
                <div class="key" data-key="i">i</div>
                <div class="key" data-key="o">o</div>
                <div class="key" data-key="p">p</div>
                <div class="key" data-key="[">[</div>
                <div class="key" data-key="]">]</div>
                <div class="key" data-key="\">\</div>
            </div>
            <div class="keyboard-row">
                <div class="key caps special" data-key="CapsLock">caps</div>
                <div class="key" data-key="a">a</div>
                <div class="key" data-key="s">s</div>
                <div class="key" data-key="d">d</div>
                <div class="key" data-key="f">f</div>
                <div class="key" data-key="g">g</div>
                <div class="key" data-key="h">h</div>
                <div class="key" data-key="j">j</div>
                <div class="key" data-key="k">k</div>
                <div class="key" data-key="l">l</div>
                <div class="key special" data-key=";">;</div>
                <div class="key special" data-key="'">'</div>
                <div class="key enter special" data-key="Enter">enter</div>
            </div>
            <div class="keyboard-row">
                <div class="key shift" data-key="Shift">shift</div>
                <div class="key" data-key="z">z</div>
                <div class="key" data-key="x">x</div>
                <div class="key" data-key="c">c</div>
                <div class="key" data-key="v">v</div>
                <div class="key" data-key="b">b</div>
                <div class="key" data-key="n">n</div>
                <div class="key" data-key="m">m</div>
                <div class="key" data-key=",">,</div>
                <div class="key" data-key=".">.</div>
                <div class="key" data-key="/">/</div>
                <div class="key shift" data-key="Shift">shift</div>
            </div>
            <div class="keyboard-row">
                <div class="key ctrl" data-key="Control">ctrl</div>
                <div class="key win" data-key="Meta">win</div>
                <div class="key alt" data-key="Alt">alt</div>
                <div class="key space" data-key=" ">space</div>
                <div class="key alt" data-key="Alt">alt</div>
                <div class="key win" data-key="Meta">win</div>
                <div class="key menu" data-key="ContextMenu">menu</div>
                <div class="key ctrl" data-key="Control">ctrl</div>
            </div>
        </div>

        <!-- Update the audio elements to use key-press.mp3 for correct sound -->
        <audio id="correctSound" preload="auto">
            <source src="assets/sounds/key-press.mp3" type="audio/mpeg">
        </audio>
        <audio id="wrongSound" preload="auto">
            <source src="assets/sounds/error.mp3" type="audio/mpeg">
        </audio>
    </div>

    <script>
        const textDisplay = document.getElementById('text-display');
        const inputField = document.getElementById('input-field');
        const correctSound = document.getElementById('correctSound');
        const wrongSound = document.getElementById('wrongSound');
        
        let currentIndex = 0;
        let mistakes = 0;
        let startTime = null;
        let timerInterval = null;

        // Split text into spans for individual character tracking
        const text = textDisplay.textContent;
        textDisplay.innerHTML = text.split('').map(char => 
            `<span>${char}</span>`
        ).join('');
        const characters = textDisplay.getElementsByTagName('span');

        inputField.addEventListener('input', (e) => {
            const typedChar = e.target.value;
            const currentChar = text[currentIndex];

            if (!startTime) {
                startTime = new Date();
                startTimer();
            }

            if (typedChar === currentChar) {
                // Correct input
                characters[currentIndex].classList.add('correct');
                characters[currentIndex].classList.remove('current', 'incorrect');
                currentIndex++;
                if (currentIndex < text.length) {
                    characters[currentIndex].classList.add('current');
                }
                correctSound.currentTime = 0;
                correctSound.play();
            } else {
                // Wrong input
                characters[currentIndex].classList.add('incorrect');
                mistakes++;
                wrongSound.currentTime = 0;
                wrongSound.play();
                
                // Highlight wrong key on keyboard
                const keyElement = document.querySelector(`.key[data-key="${typedChar}"]`);
                if (keyElement) {
                    keyElement.classList.add('wrong');
                    setTimeout(() => keyElement.classList.remove('wrong'), 200);
                }
            }

            // Clear input field
            e.target.value = '';

            // Update stats
            updateStats();

            // Check if typing is complete
            if (currentIndex === text.length) {
                finishTyping();
            }
        });

        function updateStats() {
            const timeElapsed = Math.round((new Date() - startTime) / 1000);
            const wpm = Math.round((currentIndex * 60) / (5 * timeElapsed));
            const accuracy = Math.round(((currentIndex - mistakes) / currentIndex) * 100);

            document.getElementById('wpm').textContent = wpm || 0;
            document.getElementById('accuracy').textContent = `${accuracy || 100}%`;
        }

        function startTimer() {
            const timerElement = document.getElementById('timer');
            let seconds = 0;
            timerInterval = setInterval(() => {
                seconds++;
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                timerElement.textContent = `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
            }, 1000);
        }

        function finishTyping() {
            clearInterval(timerInterval);
            inputField.disabled = true;
            // You can add more completion logic here
        }

        // Keyboard highlighting
        const keys = document.querySelectorAll('.key');
        
        document.addEventListener('keydown', (e) => {
            const key = e.key;
            const keyElement = document.querySelector(`.key[data-key="${key}"]`);
            if (keyElement) {
                keyElement.classList.add('active');
            }
        });

        document.addEventListener('keyup', (e) => {
            const key = e.key;
            const keyElement = document.querySelector(`.key[data-key="${key}"]`);
            if (keyElement) {
                keyElement.classList.remove('active');
                keyElement.classList.remove('wrong');
            }
        });

        // Debug sound loading
        correctSound.addEventListener('error', function(e) {
            console.error('Error loading correct sound:', e);
        });

        wrongSound.addEventListener('error', function(e) {
            console.error('Error loading wrong sound:', e);
        });

        // Test sounds on page load
        window.addEventListener('load', function() {
            console.log('Correct sound loaded:', correctSound.readyState);
            console.log('Wrong sound loaded:', wrongSound.readyState);
        });

        function changeTheme(theme) {
            // Update body class
            document.body.className = theme === 'dark' ? '' : `theme-${theme}`;
            
            // Update keyboard class
            document.querySelector('.keyboard').className = `keyboard ${theme === 'dark' ? '' : `theme-${theme}`}`;
            
            // Update navbar class
            const navbar = document.querySelector('.navbar');
            navbar.className = `navbar navbar-expand-lg ${theme === 'dark' ? '' : `theme-${theme}`}`;
        }

        // Theme toggle functionality
        document.getElementById('theme-toggle').addEventListener('click', () => {
            document.getElementById('theme-options').classList.toggle('show');
        });

        // Close theme options when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.theme-selector-practice')) {
                document.getElementById('theme-options').classList.remove('show');
            }
        });

        // Theme option click handlers
        document.querySelectorAll('.theme-option').forEach(option => {
            option.addEventListener('click', () => {
                const theme = option.dataset.theme;
                changeTheme(theme);
                document.getElementById('theme-options').classList.remove('show');
            });
        });
    </script>
</body>
</html> 