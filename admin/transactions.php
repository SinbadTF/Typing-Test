<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle transaction status updates
if (isset($_POST['action']) && isset($_POST['transaction_id'])) {
    $action = $_POST['action'];
    $transaction_id = $_POST['transaction_id'];
    
    try {
        $pdo->beginTransaction();
        
        if ($action === 'approve') {
            // Get user_id from transaction
            $stmt = $pdo->prepare("SELECT user_id FROM transactions WHERE id = ?");
            $stmt->execute([$transaction_id]);
            $transaction = $stmt->fetch();
            
            if ($transaction) {
                // Update transaction status
                $stmt = $pdo->prepare("UPDATE transactions SET status = 'approved' WHERE id = ?");
                $stmt->execute([$transaction_id]);
                
                // Update user's premium status
                $stmt = $pdo->prepare("UPDATE users SET is_premium = 1 WHERE user_id = ?");
                $stmt->execute([$transaction['user_id']]);
            }
        } elseif ($action === 'reject') {
            // Just update transaction status
            $stmt = $pdo->prepare("UPDATE transactions SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$transaction_id]);
        }
        
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error updating transaction: " . $e->getMessage());
    }
}

// Get all transactions with user information
$stmt = $pdo->query("
    SELECT 
        t.id,
        t.user_id,
        t.amount,
        t.status,
        t.created_at,
        t.screenshot,
        u.username,
        u.email
    FROM transactions t 
    JOIN users u ON t.user_id = u.user_id 
    ORDER BY t.created_at DESC
");
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add debug output
error_log("Transactions query result: " . print_r($transactions, true));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #ffffff;
            min-height: 100vh;
        }
        .transaction-container {
            padding: 40px;
            background: rgba(45, 45, 45, 0.98);
            border-radius: 25px;
            margin: 40px auto;
            box-shadow: 0 0 40px rgba(0,123,255,0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }
        .transaction-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #007bff, #00ff88);
        }
        .transaction-card {
            background: rgba(35, 35, 35, 0.7);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .transaction-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,123,255,0.2);
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .status-approved {
            color: #28a745;
            font-weight: bold;
        }
        .status-rejected {
            color: #dc3545;
            font-weight: bold;
        }
        .screenshot-preview {
            max-width: 150px;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        .screenshot-preview:hover {
            transform: scale(1.05);
        }
        .section-title {
            color: #adb5bd;
            font-size: 1.1rem;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .info-label {
            color: #6c757d;
            font-weight: 500;
        }
        .info-value {
            color: #ffffff;
        }
        .btn-action {
            padding: 8px 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .btn-action:hover {
            transform: translateY(-2px);
        }
        .back-button {
            color: #ffffff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        .back-button:hover {
            color: #007bff;
            transform: translateX(-5px);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <div class="transaction-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="dashboard.php" class="back-button">
                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                </a>
                <h2><i class="fas fa-money-bill-wave me-2"></i>Transaction Management</h2>
            </div>

            <?php if (empty($transactions)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No transactions found
                </div>
            <?php else: ?>
                <?php foreach ($transactions as $transaction): ?>
                    <div class="transaction-card">
                        <div class="row">
                            <div class="col-md-4">
                                <h5 class="section-title"><i class="fas fa-user me-2"></i>User Information</h5>
                                <p>
                                    <span class="info-label">Username:</span> 
                                    <span class="info-value"><?php echo htmlspecialchars($transaction['username']); ?></span>
                                </p>
                                <p>
                                    <span class="info-label">Email:</span> 
                                    <span class="info-value"><?php echo htmlspecialchars($transaction['email']); ?></span>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h5 class="section-title"><i class="fas fa-info-circle me-2"></i>Transaction Details</h5>
                                <p><span class="info-label">Amount:</span> <span class="info-value"><?php echo number_format($transaction['amount']); ?> MMK</span></p>
                                <p><span class="info-label">Date:</span> <span class="info-value"><?php echo date('Y-m-d H:i:s', strtotime($transaction['created_at'])); ?></span></p>
                                <p><span class="info-label">Status:</span> 
                                    <span class="status-<?php echo strtolower($transaction['status']); ?>">
                                        <?php echo ucfirst($transaction['status']); ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h5 class="section-title"><i class="fas fa-image me-2"></i>Payment Screenshot</h5>
                                <?php if (isset($transaction['screenshot']) && !empty($transaction['screenshot'])): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($transaction['screenshot']); ?>" 
                                         class="screenshot-preview img-fluid mb-3" 
                                         data-bs-toggle="modal" 
                                         data-bs-target="#screenshotModal"
                                         data-screenshot="../uploads/<?php echo htmlspecialchars($transaction['screenshot']); ?>"
                                         alt="Payment Screenshot">
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No screenshot uploaded
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($transaction['status'] === 'pending'): ?>
                                    <div class="d-flex gap-2">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                                            <button type="submit" name="action" value="approve" class="btn btn-success btn-action">
                                                <i class="fas fa-check me-2"></i>Approve
                                            </button>
                                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-action">
                                                <i class="fas fa-times me-2"></i>Reject
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Screenshot Modal -->
    <div class="modal fade" id="screenshotModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white">Payment Screenshot</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img src="" class="img-fluid" id="modalScreenshot" style="max-height: 80vh;">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.screenshot-preview').forEach(img => {
            img.addEventListener('click', function() {
                document.getElementById('modalScreenshot').src = this.dataset.screenshot;
            });
        });
    </script>
</body>
</html>