<?php
require_once 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$message = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    try {
        if ($newPassword) {
            if ($newPassword === $confirmPassword) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE user_id = ?");
                $stmt->execute([$username, $email, $hashedPassword, $userId]);
            } else {
                throw new Exception("Passwords do not match");
            }
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
            $stmt->execute([$username, $email, $userId]);
        }
        
        $_SESSION['username'] = $username;
        $message = "Profile updated successfully!";
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch user data
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();


// Fetch user data with typing statistics
$stmt = $pdo->prepare("
    SELECT 
        u.username, 
        u.email, 
        u.profile_image,
        u.is_premium,
        ROUND(COALESCE(AVG(NULLIF(t.wpm, 0)), 0), 1) as avg_speed,
        ROUND(COALESCE(MAX(t.wpm), 0), 1) as best_speed,
        ROUND(COALESCE(AVG(NULLIF(t.accuracy, 0)), 0), 1) as avg_accuracy,
        COUNT(DISTINCT t.result_id) as tests_completed
    FROM users u 
    LEFT JOIN typing_results t ON u.user_id = t.user_id 
    WHERE u.user_id = ?
    GROUP BY u.user_id, u.username, u.email, u.profile_image, u.is_premium"
);
$stmt->execute([$_SESSION['user_id']]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

// Debug output
if ($userData === false) {
    error_log("No user data found for ID: " . $_SESSION['user_id']);
}

// Remove this line as it's not needed
// $stats = $stmt->fetch();
?>

<!DOCTYPE html>
<link rel="stylesheet" href="assets/css/style.css">
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - TypeMaster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #ffffff;
            min-height: 100vh;
        }
        .profile-section {
            padding: 60px 40px;  /* increased horizontal padding */
            text-align: center;
            background: rgba(45, 45, 45, 0.98);
            border-radius: 25px;
            margin: 80px auto;
            max-width: 800px;
            box-shadow: 0 0 40px rgba(0,123,255,0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .stats-card {
            background: rgba(45, 45, 45, 0.98);
            border-radius: 20px;
            padding: 30px;
            margin: 20px;  /* added margin on all sides */
            transition: all 0.4s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .row {
            padding: 0 15px;  /* added horizontal padding to row */
        }
        .profile-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #007bff, #00ff88);
        }
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 20px;
            border: 3px solid transparent;
            background: linear-gradient(45deg, #007bff, #00ff88) border-box;
            object-fit: cover;
        }
        .stats-card {
            background: rgba(45, 45, 45, 0.98);
            border-radius: 20px;
            padding: 30px;
            margin: 20px 0;
            transition: all 0.4s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .stats-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,123,255,0.2);
            border-color: rgba(0,123,255,0.3);
        }
        .stats-title {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .btn-primary {
            background: linear-gradient(45deg, #007bff, #00ff88);
            border: none;
            padding: 15px 40px;
            font-size: 1.2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.4s ease;
        }
        .btn-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,123,255,0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-section">
            <div class="profile-image" style="display: flex; align-items: center; justify-content: center; background: rgba(45, 45, 45, 0.98);">
                <?php if (isset($userData['profile_image']) && $userData['profile_image'] !== null): ?>
                    <img src="uploads/profile_images/<?php echo htmlspecialchars($userData['profile_image']); ?>" 
                         class="profile-image" 
                         alt="Profile"
                         onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-user\' style=\'font-size: 80px; color: #007bff;\'></i>';">
                <?php else: ?>
                    <i class="fas fa-user" style="font-size: 80px; color: #007bff;"></i>
                <?php endif; ?>
            </div>
            
           
            <h1 class="stats-title mb-4">
                <?php echo htmlspecialchars($_SESSION['username']); ?>'s Profile
                <?php if ($userData['is_premium']): ?>
                    <span class="premium-badge">
                        <i class="fas fa-crown"></i> Premium Member
                    </span>
                <?php endif; ?>
            </h1>

            <div class="row">
                <div class="col-md-6">
                    <div class="stats-card">
                        <i class="fas fa-tachometer-alt mb-3" style="font-size: 2rem; color: #007bff;"></i>
                        <h3>Average Speed</h3>
                        <p class="h2 mb-0"><?php echo number_format($userData['avg_speed'], 1); ?> WPM</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stats-card">
                        <i class="fas fa-bullseye mb-3" style="font-size: 2rem; color: #00ff88;"></i>
                        <h3>Accuracy</h3>
                        <p class="h2 mb-0"><?php echo number_format($userData['avg_accuracy'], 1); ?>%</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stats-card">
                        <i class="fas fa-trophy mb-3" style="font-size: 2rem; color: #ffd700;"></i>
                        <h3>Best Score</h3>
                        <p class="h2 mb-0"><?php echo number_format($userData['best_speed'], 1); ?> WPM</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stats-card">
                        <i class="fas fa-keyboard mb-3" style="font-size: 2rem; color: #ff69b4;"></i>
                        <h3>Tests Completed</h3>
                        <p class="h2 mb-0"><?php echo $userData['tests_completed']; ?></p>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <a href="edit_profile.php" class="btn btn-primary me-3">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </a>
                <a href="test_history.php" class="btn btn-primary me-3">
                    <i class="fas fa-history me-2"></i>Test History
                </a>
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>Back to Home
                </a>
            </div>
        </div>
    </div>

    

    <style>
        .modal-content {
            background: rgba(25, 25, 25, 0.98);
            border: 1px solid rgba(220, 53, 69, 0.2);
            border-radius: 15px;
        }
        .modal-header {
            border-bottom: 1px solid rgba(220, 53, 69, 0.1);
            padding: 20px;
            background: rgba(20, 20, 20, 0.95);
        }
        .modal-body {
            background: rgba(30, 30, 30, 0.95);
            color: #ffffff;
            padding: 25px;
        }
        .modal-footer {
            background: rgba(20, 20, 20, 0.95);
            border-top: 1px solid rgba(220, 53, 69, 0.1);
            padding: 20px;
        }
        .modal-title {
            color: #dc3545;
        }
        .form-control {
            background: rgba(45, 45, 45, 0.9);
            border: 1px solid rgba(220, 53, 69, 0.2);
            color: #ffffff !important; /* Make text white */
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6); /* Light gray placeholder text */
        }
        .form-control:focus {
            background: rgba(45, 45, 45, 0.9);
            border-color: #dc3545;
            color: #ffffff !important;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


 <style> section
.premium-badge {
    background: linear-gradient(135deg, #e2b714, #e28c14);
    color: #323437;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-left: 10px;
}

.premium-badge i {
    color: #323437;
}