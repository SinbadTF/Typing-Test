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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Your existing styles here -->
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            padding-top: 0; /* Remove default padding */
            display: block; /* Change from flex to block */
        }

        /* Navbar styles */
        .navbar {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 123, 255, 0.1);
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            color: #ffffff !important;
            opacity: 0.8;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
            border-radius: 8px;
        }

        .nav-link:hover {
            opacity: 1;
            background: rgba(255, 255, 255, 0.1);
        }

        /* Login container styles */
        .main-container {
            padding-top: 100px; /* Add padding to account for fixed navbar */
            min-height: calc(100vh - 76px); /* Adjust for navbar height */
        }

        .auth-container {
            max-width: 500px;
            margin: 30px auto;
            padding: 40px;
            background: rgba(15, 23, 42, 0.8);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .auth-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #007bff, #00ff88);
            border-radius: 24px 24px 0 0;
        }

        /* Form controls */
        .form-control {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 12px 15px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: #007bff;
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.2);
        }

        .input-group-text {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #007bff;
        }

        /* Tabs styling */
        .nav-tabs {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 25px;
        }

        .nav-tabs .nav-link {
            color: #adb5bd;
            border: none;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link.active {
            color: #007bff;
            background: none;
            border-bottom: 2px solid #007bff;
        }

        /* Button styling */
        .btn-primary {
            background: linear-gradient(45deg, #007bff, #00ff88);
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 123, 255, 0.2);
        }

        /* Alert styling */
        .alert {
            background: rgba(15, 23, 42, 0.6);
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-keyboard me-2"></i>Boku no Typing
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="login.php">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </a>
                <a class="nav-link" href="premium.php">
                    <i class="fas fa-crown me-2"></i>Premium
                </a>
            </div>
        </div>
    </nav>

    <div class="main-container">
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