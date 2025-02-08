<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Delete user if requested
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
}

// Fetch all users
// Update the SQL query at the top of the file
$stmt = $pdo->query("SELECT user_id, username, email, created_at, is_premium FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #7fffd4;
            min-height: 100vh;
            padding: 40px 0;
        }
        .users-container {
            background: rgba(25, 25, 25, 0.95);
            border-radius: 25px;
            padding: 40px;
            margin: 20px auto;
            box-shadow: 0 0 40px rgba(0,123,255,0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            backdrop-filter: blur(10px);
        }
        .users-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #007bff, #00ff88);
        }
        .table {
            color: #7fffd4;
            margin-top: 20px;
            border-collapse: separate;
            border-spacing: 0 10px;
        }
        .table thead th {
            background: rgba(0, 123, 255, 0.2);
            border: none;
            color: #00ff88;
            font-weight: 600;
            padding: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }
        .table tbody td {
            background: rgba(45, 45, 45, 0.9);
            border: none;
            padding: 20px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            color: #7fffd4;
        }
        .table tbody tr:hover td {
            background: rgba(0, 123, 255, 0.2);
        }
        .user-email {
            color: #7fffd4;
            font-weight: 500;
        }
        .user-date {
            color: #7fffd4;
            font-size: 0.95rem;
            opacity: 0.9;
        }
        .user-name {
            font-weight: 600;
            color: #7fffd4;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }
        .table tbody tr {
            transform: scale(1);
            transition: all 0.3s ease;
        }
        .table tbody tr:hover {
            transform: scale(1.02);
        }
        .table tbody tr:hover td {
            background: rgba(0, 123, 255, 0.1);
            box-shadow: 0 5px 15px rgba(0,123,255,0.2);
        }
        .btn-danger {
            background: linear-gradient(45deg, #dc3545, #ff4d4d);
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 2rem;
            background: linear-gradient(45deg, #007bff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            letter-spacing: 1px;
        }
        .back-btn {
            background: linear-gradient(45deg, #007bff, #00ff88);
            border: none;
            padding: 12px 25px;
            border-radius: 15px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            margin-bottom: 20px;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 5px 15px rgba(0,123,255,0.2);
        }
        .back-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,123,255,0.4);
            color: white;
        }
        .user-email {
            color: #00ff88;
            font-weight: 500;
        }
        .user-date {
            color: #adb5bd;
            font-size: 0.9rem;
        }
        .user-name {
            font-weight: 600;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
        <div class="users-container">
            <h2 class="page-title">Manage Users</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Premium</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>#<?php echo $user['user_id']; ?></td>
                                <td class="user-name"><?php echo htmlspecialchars($user['username']); ?></td>
                                <td class="user-email"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $user['is_premium'] ? 'premium' : 'free'; ?>">
                                        <?php echo $user['is_premium'] ? 'Premium' : 'Free'; ?>
                                    </span>
                                </td>
                                <td class="user-date"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete user <span id="deleteUserName" class="fw-bold"></span>?
                This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="deleteForm" style="display: inline;">
                    <input type="hidden" name="user_id" id="deleteUserId">
                    <button type="submit" name="delete_user" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
   
    .modal-content {
        background: rgba(25, 25, 25, 0.98);
        border: 1px solid rgba(0, 123, 255, 0.2);
        border-radius: 5px;
        box-shadow: 0 0 30px rgba(0, 123, 255, 0.1);
        backdrop-filter: blur(10px);
    }
    .modal-header {
        border-bottom: 1px solid rgba(0, 123, 255, 0.1);
        padding: 20px;
        background: rgba(20, 20, 20, 0.95);
    }
    .modal-body {
        background: rgba(30, 30, 30, 0.95);
        color: #7fffd4;
        font-size: 1.1rem;
        padding: 25px;
    }
    .modal-footer {
        background: rgba(20, 20, 20, 0.95);
        border-top: 1px solid rgba(0, 123, 255, 0.1);
        padding: 20px;
    }
    .modal-title {
        color: #00ff88;
        font-weight: 600;
        font-size: 1.4rem;
    }
    #deleteUserName {
        color: #00ff88;
        font-weight: 600;
    }
    .btn-secondary {
        background: rgba(108, 117, 125, 0.2);
        border: none;
        padding: 10px 25px;
        border-radius: 12px;
        color: #7fffd4;
        transition: all 0.3s ease;
    }
    .btn-secondary:hover {
        background: rgba(108, 117, 125, 0.3);
        transform: translateY(-2px);
        color: #7fffd4;
    }
</style>
<script>
function confirmDelete(userId, username) {
    document.getElementById('deleteUserId').value = userId;
    document.getElementById('deleteUserName').textContent = username;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

</body>
</html>
