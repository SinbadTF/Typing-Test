<?php
require_once 'config/database.php';
session_start();
$logoutMessage = '';
if (isset($_SESSION['logout_message'])) {
    $logoutMessage = $_SESSION['logout_message'];
    unset($_SESSION['logout_message']); // Clear the message
}

$loginError = '';
$registerError = '';
$adminError = '';
$logoutMessage = '';

if (isset($_SESSION['logout_message'])) {
    $logoutMessage = $_SESSION['logout_message'];
    unset($_SESSION['logout_message']); // Clear the message
}

// Handle login form submission

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $email = $_POST['loginEmail'];
        $password = $_POST['loginPassword'];

        $stmt = $pdo->prepare("SELECT user_id, username, password, profile_image FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['profile_image'] = $user['profile_image'];
            header('Location: index.php');
            exit();
        } else {
            $loginError = "Invalid email or password";
        }
    }
    // Handle registration form submission
    else if (isset($_POST['register'])) {
        $username = $_POST['registerUsername'];
        $email = $_POST['registerEmail'];
        $password = password_hash($_POST['registerPassword'], PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, profile_image) VALUES (?, ?, ?, NULL)");
            $stmt->execute([$username, $email, $password]);
            
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            $_SESSION['profile_image'] = null;
            header('Location: index.php');
            exit();
        }
        catch (PDOException $e) {
            $registerError = "Registration failed. Email or username might already exist.";
        }
    }
    // Handle admin login
    else if (isset($_POST['admin'])) {
        $username = $_POST['adminUsername'];
        $password = $_POST['adminPassword'];
        
        // Add debug logging
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['is_admin'] = true;
            header('Location: admin/dashboard.php');
            exit();
        } else {
            $adminError = "Invalid admin credentials";
        }
    }
}

// Add this near the top of the file, after session_start()




// Close PHP tag to start HTML content
?>
<!DOCTYPE html>
<link rel="stylesheet" href="assets/css/style.css">
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register - TypeMaster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Your existing styles here -->
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .auth-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 40px;
            background: rgba(45, 45, 45, 0.98);
            border-radius: 25px;
            box-shadow: 0 0 40px rgba(0,123,255,0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }
        .auth-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #007bff, #00ff88);
        }
        .form-control {
            background: rgba(54, 54, 54, 0.8);
            border: 2px solid rgba(64, 64, 64, 0.5);
            color: #fff;
            padding: 15px;
            border-radius: 12px;
        }
        .form-control:focus {
            background: rgba(64, 64, 64, 0.9);
            border-color: #007bff;
            color: #fff;
            box-shadow: 0 0 15px rgba(0,123,255,0.3);
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
        .nav-tabs {
            border-bottom: 2px solid rgba(64, 64, 64, 0.5);
        }
        .nav-tabs .nav-link {
            color: #adb5bd;
            border: none;
            transition: all 0.3s ease;
        }
        .nav-tabs .nav-link.active {
            color: #007bff;
            background: none;
            border: none;
        }
        .nav-tabs .nav-link.active::after {
            background: linear-gradient(90deg, #007bff, #00ff88);
        }
        .form-label {
            color: #adb5bd;
        }
        .alert {
            background: rgba(45, 45, 45, 0.98);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .alert-success {
            border-color: rgba(40, 167, 69, 0.3);
            color: #28a745;
        }
        .alert-danger {
            border-color: rgba(220, 53, 69, 0.3);
            color: #dc3545;
        }
    .form-control.error {
            border-color: #ff0000;
            animation: errorShake 0.5s linear;
        }

        @keyframes errorShake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .alert-danger {
            background: rgba(255, 0, 0, 0.1);
            border: 2px solid #ff0000;
            color: #ff0000;
            animation: glowError 1.5s ease-in-out infinite;
        }

        @keyframes glowError {
            0%, 100% {
                box-shadow: 0 0 5px rgba(255, 0, 0, 0.5),
                           0 0 10px rgba(255, 0, 0, 0.3);
            }
            50% {
                box-shadow: 0 0 15px rgba(255, 0, 0, 0.8),
                           0 0 20px rgba(255, 0, 0, 0.5);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-container">
        <?php if ($logoutMessage): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($logoutMessage); ?>
            </div>
        <?php endif; ?>
            <?php if ($loginError): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($loginError); ?></div>
            <?php endif; ?>
            <?php if ($registerError): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($registerError); ?></div>
            <?php endif; ?>
            <?php if ($adminError): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($adminError); ?></div>
            <?php endif; ?>

            <ul class="nav nav-tabs" id="authTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button">
                        <i class="fas fa-sign-in-alt"></i>Login
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button">
                        <i class="fas fa-user-plus"></i>Register
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button">
                        <i class="fas fa-user-shield"></i>Admin
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="authTabsContent">
                
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                
               
                <div class="tab-pane fade show active" id="login" role="tabpanel">
                    <form id="loginForm" method="POST" novalidate>
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label"><i class="fas fa-envelope me-2"></i>Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="loginEmail" name="loginEmail" required>
                            </div>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label"><i class="fas fa-lock me-2"></i>Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="loginPassword" name="loginPassword" required>
                            </div>
                            <div class="invalid-feedback">Password is required.</div>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </form>
                </div>
                
                <div class="tab-pane fade" id="register" role="tabpanel">
                    <form id="registerForm" method="POST" novalidate>
                        <div class="mb-3">
                            <label for="registerUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="registerUsername" name="registerUsername" required pattern="^[a-zA-Z0-9_ ]{3,20}$">
                            <div class="invalid-feedback">Username must be 3-20 characters and can only contain letters, numbers, spaces, and underscores.</div>
                        </div>
                        <div class="mb-3">
                            <label for="registerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="registerEmail" name="registerEmail" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        <div class="mb-3">
                            <label for="registerPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="registerPassword" name="registerPassword" required pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$">
                            <div class="invalid-feedback">Password must be at least 8 characters long and contain at least one letter and one number.</div>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" required>
                            <div class="invalid-feedback">Passwords do not match.</div>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary">Register</button>
                    </form>
                </div>

                <div class="tab-pane fade" id="admin" role="tabpanel">
                    <form id="adminForm" method="POST" novalidate>
                        <div class="mb-3">
                            <label for="adminUsername" class="form-label">Admin Username</label>
                            <input type="text" class="form-control" id="adminUsername" name="adminUsername" required>
                            <div class="invalid-feedback">Please enter admin username.</div>
                        </div>
                        <div class="mb-3">
                            <label for="adminPassword" class="form-label">Admin Password</label>
                            <input type="password" class="form-control" id="adminPassword" name="adminPassword" required>
                            <div class="invalid-feedback">Please enter admin password.</div>
                        </div>
                        <button type="submit" name="admin" class="btn btn-primary">Admin Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const adminForm = document.getElementById('adminForm');

        // Login form validation
        loginForm.addEventListener('submit', function(event) {
            if (!loginForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            loginForm.classList.add('was-validated');
            
            // Add error class if there's a login error
            if (document.querySelector('.alert-danger')) {
                document.getElementById('loginEmail').classList.add('error');
                document.getElementById('loginPassword').classList.add('error');
            }
        });

        // Register form validation
        registerForm.addEventListener('submit', function(event) {
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (password !== confirmPassword) {
                event.preventDefault();
                document.getElementById('confirmPassword').setCustomValidity('Passwords do not match');
            } else {
                document.getElementById('confirmPassword').setCustomValidity('');
            }

            if (!registerForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            registerForm.classList.add('was-validated');
        });

        // Admin form validation
        adminForm.addEventListener('submit', function(event) {
            if (!adminForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            adminForm.classList.add('was-validated');
        });
    </script>
</body>
</html>