<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
$totalUsers = $stmt->fetch()['total_users'];

// Add these queries after the existing statistics queries
// Update the premium users query with COALESCE
$stmt = $pdo->query("SELECT COALESCE(COUNT(*), 0) as premium_users FROM users WHERE is_premium = 1");
$premiumUsers = $stmt->fetch()['premium_users'];

$stmt = $pdo->query("SELECT COALESCE(COUNT(*), 0) as normal_users FROM users WHERE is_premium = 0 OR is_premium IS NULL");
$normalUsers = $stmt->fetch()['normal_users'];

$stmt = $pdo->query("SELECT COUNT(*) as total_lessons FROM typing_texts");
$totalLessons = $stmt->fetch()['total_lessons'];

$stmt = $pdo->query("SELECT COUNT(*) as total_tests FROM typing_results");
$totalTests = $stmt->fetch()['total_tests'];

// Update the average WPM query to handle NULL values
$stmt = $pdo->query("SELECT COALESCE(AVG(wpm), 0) as avg_wpm FROM typing_results");
$avgWPM = round($stmt->fetch()['avg_wpm'], 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #ffffff;
            min-height: 100vh;
        }
        .dashboard {
            padding: 40px;
            background: rgba(45, 45, 45, 0.98);
            border-radius: 25px;
            margin: 40px auto;
            box-shadow: 0 0 40px rgba(0,123,255,0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }
        .dashboard::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #007bff, #00ff88);
        }
        .stat-card {
            background: rgba(35, 35, 35, 0.7);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,123,255,0.2);
        }
        .stat-title {
            font-size: 1.1rem;
            color: #adb5bd;
            margin-bottom: 10px;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .nav-link {
            color: #ffffff;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #007bff;
            transform: translateY(-2px);
        }
        .dashboard-menu {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.dashboard-item {
    background: rgba(35, 35, 35, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 20px;
    color: #d1d0c5;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 15px;
}

.dashboard-item:hover {
    transform: translateY(-5px);
    background: rgba(0, 123, 255, 0.1);
    border-color: rgba(0, 123, 255, 0.3);
    color: #fff;
}

.dashboard-item i {
    font-size: 1.5rem;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 123, 255, 0.1);
    border-radius: 10px;
    color: #007bff;
}

.dashboard-item span {
    font-size: 1.1rem;
    font-weight: 500;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Admin Dashboard</h2>
                <a href="logout.php" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>

           
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <div class="stat-title">
                            <i class="fas fa-users me-2"></i>Total Users
                        </div>
                        <div class="stat-value"><?php echo $totalUsers; ?></div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <div class="stat-title">
                            <i class="fas fa-crown me-2"></i>Premium Users
                        </div>
                        <div class="stat-value"><?php echo $premiumUsers; ?></div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <div class="stat-title">
                            <i class="fas fa-user me-2"></i>Normal Users
                        </div>
                        <div class="stat-value"><?php echo $normalUsers; ?></div>
                    </div>
                </div>
                
            </div>

            <div class="mt-5">
                <h3 class="mb-4">Quick Links</h3>
                <div class="dashboard-menu">
                    <a href="manage_users.php" class="dashboard-item">
                        <i class="fas fa-users"></i>
                        <span>Manage Users</span>
                    </a>
                    <a href="manage_lessons.php" class="dashboard-item">
                        <i class="fas fa-book"></i>
                        <span>Manage Lessons</span>
                    </a>
                    <a href="../admin/transactions.php" class="dashboard-item">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>View Transactions</span>
                    </a>
                    
                </div>
            </div>
        </div>
    </div>
</body>
</html>