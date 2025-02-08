<?php
session_start();

// Check if the confirmation is received
if (isset($_POST['confirm_logout'])) {
    session_destroy();
    
    // Set a success message in a temporary session
    session_start();
    $_SESSION['logout_message'] = "Successfully logged out!";
    
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<link rel="stylesheet" href="assets/css/style.css">
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Confirmation - TypeMaster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logout-container {
            background: rgba(45, 45, 45, 0.98);
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 0 40px rgba(0,123,255,0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
            position: relative;
            overflow: hidden;
        }
        .logout-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #007bff, #00ff88);
        }
        .logout-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .btn {
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.4s ease;
        }
        .btn-cancel {
            background: rgba(108, 117, 125, 0.2);
            color: #fff;
            border: none;
            margin-right: 15px;
        }
        .btn-cancel:hover {
            background: rgba(108, 117, 125, 0.3);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .btn-danger {
            background: linear-gradient(45deg, #dc3545, #ff6b6b);
            border: none;
        }
        .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(220,53,69,0.4);
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <h2 class="logout-title">Confirm Logout</h2>
        <p class="mb-4">Are you sure you want to end your session?</p>
        <form method="POST" class="d-flex justify-content-center">
            <a href="index.php" class="btn btn-cancel">
                <i class="fas fa-times me-2"></i>Cancel
            </a>
            <button type="submit" name="confirm_logout" class="btn btn-danger">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </button>
        </form>
    </div>
</body>
</html>