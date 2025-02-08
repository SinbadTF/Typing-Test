<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$error = '';

// Handle delete account
if (isset($_POST['delete_account'])) {
    $password = $_POST['confirm_password'];
    
    try {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $userCheck = $stmt->fetch();
        
        if ($userCheck && password_verify($password, $userCheck['password'])) {
            // Start transaction
            $pdo->beginTransaction();
            
            // Delete test results
            $stmt = $pdo->prepare("DELETE FROM test_results WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            
            // Delete profile image
            if (isset($_SESSION['profile_image']) && $_SESSION['profile_image']) {
                $image_path = 'uploads/profile_images/' . $_SESSION['profile_image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            // Delete user account
            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            
            // Commit transaction
            $pdo->commit();
            
            // Clear session
            session_destroy();
            $_SESSION = array();
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time()-42000, '/');
            }
            
            header('Location: inedx.php?message=account_deleted');
            exit();
        } else {
            $error = "Incorrect password. Account deletion failed.";
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Error deleting account. Please try again.";
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_account'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);
    
    try {
        // Handle profile image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['profile_image']['name'];
            $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($filetype, $allowed)) {
                // Delete old profile image if exists
                if (isset($_SESSION['profile_image']) && $_SESSION['profile_image']) {
                    $old_image = 'uploads/profile_images/' . $_SESSION['profile_image'];
                    if (file_exists($old_image)) {
                        unlink($old_image);
                    }
                }
                
                $new_filename = uniqid() . '.' . $filetype;
                $upload_path = 'uploads/profile_images/' . $new_filename;
                
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                    $_SESSION['profile_image'] = $new_filename;
                }
            }
        }

        // Update user data
        if ($new_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ?, profile_image = ? WHERE user_id = ?");
            $stmt->execute([$username, $email, $hashed_password, $_SESSION['profile_image'], $_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, profile_image = ? WHERE user_id = ?");
            $stmt->execute([$username, $email, $_SESSION['profile_image'], $_SESSION['user_id']]);
        }
        
        $_SESSION['username'] = $username;
        $message = "Profile updated successfully!";
    } catch (PDOException $e) {
        $error = "Error updating profile. Please try again.";
    }
}

// Get current user data
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();


if (isset($_POST['delete_account'])) {
    $password = $_POST['confirm_password'];
    $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (password_verify($password, $user['password'])) {
        // Update the delete account section
        if (isset($_POST['delete_account'])) {
            try {
                // Start transaction
                $pdo->beginTransaction();
        
                // First delete all typing results for this user
                $stmt = $pdo->prepare("DELETE FROM typing_results WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
        
                // Then delete the user account
                $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
        
                // Commit transaction
                $pdo->commit();
        
                // Clear session and redirect
                session_destroy();
                header('Location: login.php');
                exit();
            } catch (PDOException $e) {
                // Rollback transaction on error
                $pdo->rollBack();
                $error = "Failed to delete account. Please try again.";
            }
        }
        
        // Delete profile image if exists
        if (isset($_SESSION['profile_image']) && $_SESSION['profile_image']) {
            $image_path = 'uploads/profile_images/' . $_SESSION['profile_image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        session_destroy();
        header('Location: login.php');
        exit();
    } else {
        $error = "Incorrect password. Account deletion failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - TypeMaster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #ffffff;
            min-height: 100vh;
        }
        .edit-profile-section {
            padding: 60px 40px;
            background: rgba(45, 45, 45, 0.98);
            border-radius: 25px;
            margin: 80px auto;
            max-width: 800px;
            box-shadow: 0 0 40px rgba(0,123,255,0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }
        .edit-profile-section::before {
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
        .profile-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 2rem;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }
        .form-label {
            color: #adb5bd;
            font-weight: 500;
        }
        .btn-secondary {
            background: rgba(108, 117, 125, 0.2);
            border: none;
            padding: 15px 40px;
            font-size: 1.2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.4s ease;
            color: #fff;
        }
        .btn-secondary:hover {
            background: rgba(108, 117, 125, 0.3);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            color: #fff;
        }
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
        color: #ffffff;
    }
    .form-control:focus {
        background: rgba(45, 45, 45, 0.9);
        border-color: #dc3545;
        color: #ffffff;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }
    </style>
</head>
<body>
    <div class="container">
        <div class="edit-profile-section">
            <h1 class="profile-title">Edit Profile</h1>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" 
                           value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                
                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="mb-4">
                    <label for="new_password" class="form-label">New Password (leave blank to keep current)</label>
                    <input type="password" class="form-control" id="new_password" name="new_password">
                </div>
                
                <div class="mb-4">
                    <!-- Replace the file input section with this -->
                    <div class="mb-4">
                        <label class="form-label">Profile Image</label>
                        <div class="custom-file-input">
                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*" onchange="updateFileName(this)">
                            <label for="profile_image" class="custom-file-label">
                                <i class="fas fa-upload"></i>
                                <span class="file-name">Choose file...</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3">
                    <a href="profile.php" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>

                <div class="mt-5 pt-4 border-top border-danger">
                    <div class="danger-zone text-center">
                        <h3 class="text-danger mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                        </h3>
                        <p class="text-muted mb-4">Once you delete your account, there is no going back. Please be certain.</p>
                        <button type="button" class="btn btn-outline-danger btn-lg" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <i class="fas fa-user-times me-2"></i>Delete Account
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Account Modal -->
   
    
    
    <div class="modal fade" id="deleteAccountModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-danger">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>Delete Account
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-slash text-danger" style="font-size: 3rem;"></i>
                            <h4 class="text-danger mt-3">Are you absolutely sure?</h4>
                            <p class="text-muted">This action cannot be undone. All your data will be permanently deleted.</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Please enter your password to confirm:</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-danger">
                                    <i class="fas fa-lock text-danger"></i>
                                </span>
                                <input type="password" class="form-control border-danger" name="confirm_password" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-danger">
                        <div class="d-flex justify-content-between w-100">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" name="delete_account" class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>Delete My Account
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Bootstrap JS before closing body tag -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function confirmDelete() {
        return confirm('Are you absolutely sure you want to delete your account? This action cannot be undone!');
    }
    </script>
    <style>
        .danger-zone {
            background: rgba(220, 53, 69, 0.1);
            padding: 2rem;
            border-radius: 15px;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }
        .btn-outline-danger {
            border-width: 2px;
            padding: 1rem 2rem;
            transition: all 0.3s ease;
        }
        .btn-outline-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
        .modal-content {
            background: rgba(25, 25, 25, 0.98);
            border: 1px solid rgba(220, 53, 69, 0.2);
            border-radius: 5px;
        }
        .modal-header, .modal-footer {
            background: rgba(20, 20, 20, 0.95);
            padding: 1.5rem;
        }
        .modal-body {
            background: rgba(30, 30, 30, 0.95);
            padding: 2rem;
        }
        .input-group-text {
            background: rgba(20, 20, 20, 0.95) !important;
            color: #dc3545;
        }
        .form-control {
            background: rgba(45, 45, 45, 0.9);
            color: #ffffff;
        }
        .form-control:focus {
            background: rgba(45, 45, 45, 0.9);
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }
    </style>
</body>
</html>